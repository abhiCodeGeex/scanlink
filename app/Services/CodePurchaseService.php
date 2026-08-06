<?php

namespace App\Services;

use App\Enums\CodeOrderStatus;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\CodePrising;
use App\Models\CodePurchase;
use Illuminate\Validation\ValidationException;

class CodePurchaseService
{
    public function createPurchase(Client $client, int $qty, ?string $resellerCode = null, ?ClientUser $orderedBy = null): CodePurchase
    {
        $tier = $this->resolveTierForQuantity($qty);

        if (! $tier) {
            throw ValidationException::withMessages([
                'quantity' => 'No pricing tier matches the requested quantity.',
            ]);
        }

        if ($qty < $tier->code_min_qty || $qty > $tier->code_max_qty) {
            throw ValidationException::withMessages([
                'quantity' => "Quantity must be between {$tier->code_min_qty} and {$tier->code_max_qty}.",
            ]);
        }

        $member = $orderedBy ?? $client->primaryUser;
        $resellerClientId = null;
        $isResellerPricing = false;
        $perCodeAmount = (float) $tier->amount;

        $resellerCode = trim((string) $resellerCode);

        if ($resellerCode !== '') {
            $resellerClientId = Client::findByResellerCode($resellerCode)?->id;

            if ($resellerClientId) {
                $isResellerPricing = true;
                $perCodeAmount = (float) $tier->reseller_amount;
            }
        }

        $totalAmount = round($qty * $perCodeAmount, 2);

        return $client->codePurchases()->create([
            'email' => $member?->email ?: $client->email,
            'first_name' => $member?->first_name,
            'last_name' => $member?->last_name,
            'company_name' => $member?->company_name ?: $client->client_name,
            'billing_address' => $member?->billing_address ?: $client->address,
            'town' => $member?->town,
            'phone' => $member?->phone ?: $client->telephone,
            'postal_code' => $member?->postal_code,
            'no_of_codes' => $qty,
            'per_code_amount' => $perCodeAmount,
            'total_amount' => $totalAmount,
            'status' => CodeOrderStatus::New,
            'enable' => false,
            'is_reseller_pricing_code' => $isResellerPricing,
            'reseller_client_id' => $resellerClientId,
            'free_code' => false,
            'ordered_on' => now(),
        ]);
    }

    protected function resolveTierForQuantity(int $qty): ?CodePrising
    {
        return CodePrising::query()
            ->where('code_min_qty', '<=', $qty)
            ->where('code_max_qty', '>=', $qty)
            ->orderBy('code_min_qty')
            ->first();
    }
}
