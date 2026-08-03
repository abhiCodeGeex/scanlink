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

        if (! $member?->id) {
            throw ValidationException::withMessages([
                'quantity' => 'Unable to place order — no account member is linked to this client.',
            ]);
        }

        $priceSmall = (float) config('scanlink.label_price_small');
        $priceLarge = (float) config('scanlink.label_price_large');

        $address = trim((string) ($member->billing_address ?: $profile->client?->address ?: ''));
        $town = trim((string) ($member->town ?: ''));

        // Live `orders` table: many columns are NOT NULL with no defaults (incl. transaction_id).
        // Legacy placeTempOrder inserts status "New" then setTransactionId immediately
        // overwrites it to "Paid" with placeholder transaction_id "After new order set"
        // (dashboard.php:2403-2406). We land on the same end state directly.
        $attributes = [
            'client_id' => $profile->client_id,
            'user_id' => (int) $member->id,
            'profile_id' => $profile->id,
            'qty_small' => $qtySmall,
            'qty_large' => $qtyLarge,
            'price_small' => $qtySmall > 0 ? $priceSmall : 0,
            'price_large' => $qtyLarge > 0 ? $priceLarge : 0,
            'status' => PhysicalOrderStatus::Paid,
            'transaction_id' => 'After new order set',
            'first_name' => (string) ($member->first_name ?: ''),
            'last_name' => (string) ($member->last_name ?: ''),
            'address1' => $address,
            'address2' => $address,
            'city' => $town,
            'state' => $town,
            'zip' => (string) ($member->postal_code ?: ''),
            // Legacy mis-maps town into the country column (dashboard.php:2383).
            'country' => $town,
            'email' => (string) ($member->email ?: $profile->client?->email ?: ''),
            'contact' => (string) ($member->phone ?: $profile->client?->telephone ?: ''),
            'ordered_on' => now(),
        ];

        return Order::query()->create($attributes);
    }
}
