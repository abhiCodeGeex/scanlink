<?php

namespace App\Services;

use App\Enums\PhysicalOrderStatus;
use App\Models\ClientUser;
use App\Models\Order;
use App\Models\Profile;
use Illuminate\Validation\ValidationException;

class LabelOrderService
{
    /**
     * @param  'small'|'large'  $size
     */
    public function createLabelOrder(Profile $profile, string $size, int $qty, ?ClientUser $orderedBy = null): Order
    {
        $size = strtolower($size);

        if (! in_array($size, ['small', 'large'], true)) {
            throw ValidationException::withMessages([
                'size' => 'Label size must be small or large.',
            ]);
        }

        if ($qty < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'Quantity must be at least 1.',
            ]);
        }

        $profile->loadMissing('client');
        $member = $orderedBy ?? $profile->client?->primaryUser;
        $unitPrice = $size === 'small'
            ? (float) config('scanlink.label_price_small')
            : (float) config('scanlink.label_price_large');

        $attributes = [
            'client_id' => $profile->client_id,
            'user_id' => $member?->id,
            'profile_id' => $profile->id,
            'qty_small' => $size === 'small' ? $qty : 0,
            'qty_large' => $size === 'large' ? $qty : 0,
            'price_small' => $size === 'small' ? $unitPrice : 0,
            'price_large' => $size === 'large' ? $unitPrice : 0,
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
