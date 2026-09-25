<?php

namespace App\Services;

use App\Models\ClientUser;
use App\Models\CodePurchase;
use App\Models\CodePurchaseDetail;
use App\Models\EquipmentType;
use App\Models\Profile;

/**
 * Turns a code order into the codes themselves.
 *
 * An order row on its own buys nothing: what a client actually spends is an open `profiles` slot
 * (update_or_not = 0), which "Add a New Code" claims and Code Balance counts. Legacy created one
 * slot plus one `code_purchase_detail` row per code, in the same three places an order is
 * created — registration's free code (classes/model/codepurchase.php:113), the admin's free-code
 * grant (siteadmin classes/model/codeorder.php:164) and a paid purchase.
 *
 * The rebuild only did it for paid purchases, so a new sign-up and an admin goodwill grant both
 * produced an order and no codes at all.
 */
class CodeSlotIssuer
{
    /**
     * Create the open slots for an order, and the detail row that ties each to it.
     *
     * @return list<Profile>
     */
    public function issue(
        CodePurchase $order,
        ClientUser $member,
        int $quantity,
        float $perCodeAmount = 0.0,
        bool $freeCode = true,
        string $isResellerCode = '0',
        int $resellerClientId = 0,
    ): array {
        if ($quantity < 1) {
            return [];
        }

        $typeId = $this->defaultTypeId();
        $slots = [];

        for ($i = 0; $i < $quantity; $i++) {
            $profile = Profile::query()->create([
                'client_id' => $order->client_id,
                'user_id' => $member->id,
                'code_purchase_id' => $order->id,
                'type_id' => $typeId,
                // Legacy gives every code a year from the day it is issued.
                'expired_at' => now()->addYear(),
                'update_or_not' => false,
                'is_reseller_code' => $isResellerCode,
                'reseller_client_id' => $resellerClientId,
                'free_code' => $freeCode,
            ]);

            CodePurchaseDetail::query()->create([
                'code_purchase_id' => $order->id,
                'profile_id' => (int) $profile->id,
                // Live table has NOT NULL amount.
                'amount' => $perCodeAmount,
            ]);

            $slots[] = $profile;
        }

        return $slots;
    }

    /** The "code" equipment type, or the first one when a site has none by that name. */
    private function defaultTypeId(): int
    {
        $typeId = (int) (EquipmentType::query()->where('slag', 'code')->value('id') ?? 0);

        return $typeId > 0
            ? $typeId
            : (int) (EquipmentType::query()->orderBy('id')->value('id') ?? 1);
    }
}
