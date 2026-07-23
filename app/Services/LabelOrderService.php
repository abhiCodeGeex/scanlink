<?php

namespace App\Services;

use App\Enums\PhysicalOrderStatus;
use App\Models\ClientUser;
use App\Models\Order;
use App\Models\Profile;
use Illuminate\Validation\ValidationException;

class LabelOrderService
{
    public function createLabelOrder(
        Profile $profile,
        int $qtySmall,
        int $qtyLarge,
        ?ClientUser $orderedBy = null,
    ): Order {
        $qtySmall = max(0, $qtySmall);
        $qtyLarge = max(0, $qtyLarge);

        if ($qtySmall < 1 && $qtyLarge < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'Select quantity.',
            ]);
        }

        $profile->loadMissing('client');
        $member = $orderedBy ?? $profile->client?->primaryUser;
        $priceSmall = (float) config('scanlink.label_price_small');
        $priceLarge = (float) config('scanlink.label_price_large');

        $attributes = [
            'client_id' => $profile->client_id,
            'user_id' => $member?->id,
            'profile_id' => $profile->id,
            'qty_small' => $qtySmall,
            'qty_large' => $qtyLarge,
            'price_small' => $qtySmall > 0 ? $priceSmall : 0,
            'price_large' => $qtyLarge > 0 ? $priceLarge : 0,
            'status' => PhysicalOrderStatus::New,
            'first_name' => $member?->first_name,
            'last_name' => $member?->last_name,
            'address1' => $member?->billing_address ?: $profile->client?->address,
            'city' => $member?->town,
            'zip' => $member?->postal_code,
            'country' => 'Australia',
            'email' => $member?->email ?: $profile->client?->email,
            'contact' => $member?->phone ?: $profile->client?->telephone,
            'ordered_on' => now(),
        ];

        return Order::query()->create($attributes);
    }
}
