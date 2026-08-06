<?php

namespace App\Support;

use App\Models\Client;
use App\Models\ClientUser;
use App\Models\CodePurchase;

class CodePurchaseResellerDetails
{
    public static function forOrder(CodePurchase $order): ?ClientUser
    {
        $order->loadMissing('client.primaryUser');

        $resellerCode = $order->client?->primaryUser?->client_reseller_code;

        if (! filled($resellerCode)) {
            return null;
        }

        $resellerClientId = Client::findByResellerCode($resellerCode)?->id;

        if (! $resellerClientId) {
            return null;
        }

        return ClientUser::query()
            ->where('client_id', $resellerClientId)
            ->where('role', 5)
            ->first();
    }
}
