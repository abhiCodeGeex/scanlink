<?php

namespace App\Services;

use App\Enums\CodeOrderStatus;
use App\Models\ClientUser;
use App\Models\CodePrising;
use App\Models\CodePurchase;
use App\Models\CodePurchaseDetail;
use App\Models\Profile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CodeProfileRenewalService
{
    /**
     * Legacy renew pricing quote (code/renewCode).
     *
     * - Not yet expired: reuse original purchase per_code_amount
     * - Already expired: single-code tier amount (reseller_amount when reseller code)
     *
     * @param  Collection<int, Profile>|iterable<int, Profile|int>  $profiles
     * @return array{
     *     profile_ids: list<int>,
     *     amounts: list<float>,
     *     total: float,
     *     mixing_code: string,
     *     quantity: int
     * }
     */
    public function quote(iterable $profiles): array
    {
        $profileModels = $this->resolveProfiles($profiles);

        if ($profileModels->isEmpty()) {
            throw new \InvalidArgumentException('Please select code to be renew.');
        }

        $resellerFlags = $profileModels
            ->map(fn (Profile $p): string => $this->resellerFlag($p))
            ->unique()
            ->values();

        $mixingCode = $resellerFlags->count() > 1 ? '2' : '0';

        $amounts = [];
        $total = 0.0;

        foreach ($profileModels as $profile) {
            $monthly = $this->monthlyAmountForProfile($profile);
            $amounts[] = $monthly;
            $total += round($monthly * 12, 2);

            if ($this->resellerFlag($profile) === '1' && $mixingCode !== '2') {
                $mixingCode = '1';
            }
        }

        return [
            'profile_ids' => $profileModels->map(fn (Profile $p): int => (int) $p->id)->all(),
            'amounts' => $amounts,
            'total' => round($total, 2),
            'mixing_code' => $mixingCode,
            'quantity' => $profileModels->count(),
        ];
    }

    /**
     * @param  Collection<int, Profile>|iterable<int, Profile|int>  $profiles
     * @param  array{
     *     profile_ids?: list<int>,
     *     amounts?: list<float>,
     *     total?: float,
     *     mixing_code?: string,
     *     quantity?: int
     * }|null  $quote
     */
    public function renew(iterable $profiles, ?int $clientId = null, ?array $quote = null, ?ClientUser $billingUser = null): CodePurchase
    {
        $profileModels = $this->resolveProfiles($profiles);

        if ($profileModels->isEmpty()) {
            throw new \InvalidArgumentException('Please select code to be renew.');
        }

        $quote ??= $this->quote($profileModels);
        $clientId ??= (int) $profileModels->first()->client_id;
        $amounts = array_values($quote['amounts'] ?? []);
        $total = (float) ($quote['total'] ?? 0);
        $mixingCode = (string) ($quote['mixing_code'] ?? '0');
        $qty = (int) ($quote['quantity'] ?? $profileModels->count());

        $uniqueAmounts = array_values(array_unique(array_map(
            fn ($a): string => number_format((float) $a, 2, '.', ''),
            $amounts
        )));
        $orderPerCode = count($uniqueAmounts) === 1
            ? (float) $uniqueAmounts[0]
            : 0.0;

        return DB::transaction(function () use ($profileModels, $clientId, $amounts, $total, $mixingCode, $qty, $orderPerCode, $billingUser): CodePurchase {
            $client = $profileModels->first()->client()->firstOrFail();
            $member = $billingUser;

            $order = CodePurchase::query()->create([
                'client_id' => $clientId,
                'email' => (string) ($member?->email ?: $client->email),
                'first_name' => (string) ($member?->first_name ?? $client->contact_person ?? ''),
                'last_name' => (string) ($member?->last_name ?? ''),
                'company_name' => (string) ($member?->company_name ?? $client->client_name ?? ''),
                'billing_address' => (string) ($member?->billing_address ?? $client->address ?? ''),
                'town' => (string) ($member?->town ?? ''),
                'phone' => (string) ($member?->phone ?? $client->telephone ?? ''),
                'postal_code' => (string) ($member?->postal_code ?? ''),
                'no_of_codes' => $qty,
                'per_code_amount' => $orderPerCode,
                'total_amount' => $total,
                'status' => CodeOrderStatus::Renew,
                'enable' => false,
                'exipry_date' => now()->addYear(),
                'is_reseller_pricing_code' => $mixingCode,
                'reseller_client_id' => 0,
                'free_code' => false,
                'ordered_on' => now(),
            ]);

            foreach ($profileModels->values() as $index => $profile) {
                $base = $profile->expired_at && $profile->expired_at->isFuture()
                    ? $profile->expired_at
                    : now();

                $monthly = (float) ($amounts[$index] ?? $this->monthlyAmountForProfile($profile));

                // Legacy renew updates ONLY expired_at (codepurchase.php:302-307); it does
                // NOT repoint profiles.code_purchase_id — the renewal links to the profile
                // via code_purchase_detail. Overwriting it would corrupt the later
                // "original purchase" pre-expiry price lookup on the next renewal.
                $profile->update([
                    'expired_at' => $base->copy()->addYear(),
                ]);

                CodePurchaseDetail::query()->create([
                    'code_purchase_id' => $order->id,
                    'profile_id' => $profile->id,
                    'amount' => $monthly,
                ]);
            }

            return $order;
        });
    }

    public function amountForQuantity(int $quantity): float
    {
        $tier = CodePrising::query()
            ->where('code_min_qty', '<=', $quantity)
            ->where('code_max_qty', '>=', $quantity)
            ->orderBy('code_min_qty')
            ->first();

        return (float) ($tier?->amount ?? 0);
    }

    public function resellerAmountForQuantity(int $quantity): float
    {
        $tier = CodePrising::query()
            ->where('code_min_qty', '<=', $quantity)
            ->where('code_max_qty', '>=', $quantity)
            ->orderBy('code_min_qty')
            ->first();

        return (float) ($tier?->reseller_amount ?? $tier?->amount ?? 0);
    }

    /**
     * @param  iterable<int, Profile|int>  $profiles
     * @return Collection<int, Profile>
     */
    protected function resolveProfiles(iterable $profiles): Collection
    {
        return collect($profiles)
            ->map(function ($profile): ?Profile {
                if ($profile instanceof Profile) {
                    return $profile;
                }

                $id = (int) $profile;

                return $id > 0
                    ? Profile::query()->find($id)
                    : null;
            })
            ->filter()
            ->values();
    }

    protected function monthlyAmountForProfile(Profile $profile): float
    {
        $notExpired = $profile->expired_at && $profile->expired_at->isFuture();

        if ($notExpired) {
            $purchaseId = (int) ($profile->code_purchase_id ?? 0);

            if ($purchaseId <= 0) {
                $purchaseId = (int) (CodePurchaseDetail::query()
                    ->where('profile_id', $profile->id)
                    ->orderByDesc('id')
                    ->value('code_purchase_id') ?? 0);
            }

            if ($purchaseId > 0) {
                $original = CodePurchase::query()->whereKey($purchaseId)->value('per_code_amount');
                if ($original !== null) {
                    return (float) $original;
                }
            }
        }

        // Renew after expired — single-code registration price (legacy getAmountByQuantity(1)).
        if ($this->resellerFlag($profile) === '1') {
            return $this->resellerAmountForQuantity(1);
        }

        return $this->amountForQuantity(1);
    }

    protected function resellerFlag(Profile $profile): string
    {
        return (string) ($profile->getRawOriginal('is_reseller_code') ?? $profile->is_reseller_code ?? '0');
    }
}
