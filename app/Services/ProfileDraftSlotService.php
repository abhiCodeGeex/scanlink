<?php

namespace App\Services;

use App\Models\EquipmentType;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;

/**
 * Legacy getprofileidtoupdate(): claim an unused paid code slot for create/edit.
 */
class ProfileDraftSlotService
{
    public function availableSlotForClient(int $clientId): ?Profile
    {
        return Profile::query()
            ->where('client_id', $clientId)
            ->where('deleted', false)
            ->where('update_or_not', false)
            ->where(function ($query): void {
                $query->whereNull('expired_at')
                    ->orWhere('expired_at', '>', now());
            })
            ->orderBy('id')
            ->first();
    }

    /**
     * When no purchased open slot exists, create a blank open slot so location/add
     * can always bind a profile_id (legacy Kohana always has profile_id before Form Builder).
     */
    public function createOpenSlot(int $clientId, ?int $userId = null, ?int $typeId = null): Profile
    {
        if (! $typeId) {
            throw new \InvalidArgumentException('createOpenSlot requires a valid equipment type_id (FK).');
        }

        $profile = new Profile;
        $profile->forceFill([
            'client_id' => $clientId,
            'user_id' => $userId ?: null,
            'type_id' => $typeId,
            'code_profile_name' => '',
            'name' => '',
            'update_or_not' => false,
            'deleted' => false,
            'free_code' => true,
            'expired_at' => now()->addYear(),
            'activation_start_date' => null,
            'activation_end_date' => null,
        ])->save();

        return $profile->fresh();
    }

    /**
     * Assign equipment type to a blank slot (legacy updateProfileIdForUploadData).
     */
    public function assignType(Profile $profile, int $typeId): Profile
    {
        $profile->forceFill([
            'type_id' => $typeId,
            'update_or_not' => true,
            'updated_at' => now(),
        ])->save();

        return $profile->fresh(['equipmentType', 'client']);
    }

    public function claimForCreate(int $clientId, ?string $typeSlag, ?int $userId = null): ?Profile
    {
        $typeId = filled($typeSlag)
            ? EquipmentType::query()->where('slag', $typeSlag)->value('id')
            : null;

        if (! $typeId) {
            return null;
        }

        $typeId = (int) $typeId;
        $slot = $this->availableSlotForClient($clientId);

        // Live always has a profile_id before showing the add form + Form Builder iframe.
        if (! $slot) {
            $slot = $this->createOpenSlot($clientId, $userId, $typeId);

            return $this->assignType($slot, $typeId);
        }

        return $this->assignType($slot, $typeId);
    }

    public function finalize(Profile $profile): void
    {
        if (! $profile->update_or_not) {
            $profile->forceFill(['update_or_not' => true])->save();
        }
    }

    /**
     * @return array{total: int, open: int}
     */
    public function slotCounts(int $clientId): array
    {
        $row = DB::selectOne(
            'SELECT COUNT(*) AS total,
                    SUM(CASE WHEN update_or_not = 0 AND (expired_at IS NULL OR expired_at > NOW()) THEN 1 ELSE 0 END) AS open
             FROM profiles WHERE client_id = ? AND deleted = 0',
            [$clientId]
        );

        return [
            'total' => (int) ($row->total ?? 0),
            'open' => (int) ($row->open ?? 0),
        ];
    }
}
