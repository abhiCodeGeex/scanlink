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

    public function claimForCreate(int $clientId, ?string $typeSlag): ?Profile
    {
        $slot = $this->availableSlotForClient($clientId);

        if (! $slot) {
            return null;
        }

        if (filled($typeSlag)) {
            $typeId = EquipmentType::query()->where('slag', $typeSlag)->value('id');

            if ($typeId) {
                return $this->assignType($slot, (int) $typeId);
            }
        }

        return $slot;
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
