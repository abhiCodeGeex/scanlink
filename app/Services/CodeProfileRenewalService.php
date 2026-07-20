<?php

namespace App\Services;

use App\Enums\CodeOrderStatus;
use App\Models\CodePrising;
use App\Models\CodePurchase;
use App\Models\Profile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CodeProfileRenewalService
{
    /**
     * @param  Collection<int, Profile>|array<int, Profile|int>  $profiles
     */
    public function renew(iterable $profiles, ?int $clientId = null): CodePurchase
    {
        $profileModels = collect($profiles)
            ->map(function ($profile): Profile {
                return $profile instanceof Profile
                    ? $profile
                    : Profile::query()->findOrFail($profile);
            })
            ->values();

        if ($profileModels->isEmpty()) {
            throw new \InvalidArgumentException('Please select code to be renew.');
        }

        $clientId ??= $profileModels->first()->client_id;
        $qty = $profileModels->count();
        $perMonth = $this->amountForQuantity($qty);
        $total = round($perMonth * $qty * 12, 2);

        return DB::transaction(function () use ($profileModels, $clientId, $qty, $perMonth, $total): CodePurchase {
            $client = $profileModels->first()->client()->firstOrFail();

            $order = CodePurchase::query()->create([
                'client_id' => $clientId,
                'email' => $client->email,
                'first_name' => $client->contact_person,
                'company_name' => $client->client_name,
                'billing_address' => $client->address,
                'phone' => $client->telephone,
                'no_of_codes' => $qty,
                'per_code_amount' => $perMonth,
                'total_amount' => $total,
                'status' => CodeOrderStatus::Renew,
                'enable' => false,
                'exipry_date' => now()->addYear(),
                'is_reseller_pricing_code' => false,
                'free_code' => false,
                'ordered_on' => now(),
            ]);

            foreach ($profileModels as $profile) {
                $base = $profile->expired_at && $profile->expired_at->isFuture()
                    ? $profile->expired_at
                    : now();

                $profile->update([
                    'expired_at' => $base->copy()->addYear(),
                    'code_purchase_id' => $order->id,
                ]);

                $order->details()->create([
                    'profile_id' => $profile->id,
                    'amount' => $perMonth,
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
}
