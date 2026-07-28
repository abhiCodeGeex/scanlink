<?php

namespace App\Services;

use App\Models\EquipmentType;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Legacy getprofileidtoupdate(): claim an unused paid code slot for create/edit.
 */
class ProfileDraftSlotService
{
    public function availableSlotForClient(int $clientId): ?Profile
    {
        return Profile::query()
            ->where('client_id', $clientId)
            ->active()
            ->openSlot()
            ->where(function ($query): void {
                $query->whereNull('expired_at')
                    ->orWhere('expired_at', '>', now());
            })
            ->orderBy('id')
            ->first();
    }

    /**
     * Bind equipment type onto an unused paid slot for the create form.
     *
     * Does NOT activate the slot (`update_or_not` stays `0`) — Code Balance only
     * decreases after Save via finalize(). Legacy insertLocation sets update_or_not on save.
     *
     * Always scrubs leftover row/relation data first — open slots in the live DB
     * sometimes still hold old profile content, and CreateProfile is EditRecord
     * so that junk would otherwise pre-fill the "Add a New Code" form.
     */
    public function assignType(Profile $profile, int $typeId): Profile
    {
        $this->resetContentForCreate($profile);

        $profile->forceFill([
            'type_id' => $typeId,
            'update_or_not' => false,
            'updated_at' => now(),
        ])->save();

        return $profile->fresh(['equipmentType', 'client']);
    }

    /**
     * Bind the next unused paid slot for "Add a New Code".
     * Returns null when Code Balance is empty (do not invent free profiles).
     */
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

        if (! $slot) {
            return null;
        }

        if ($userId) {
            $slot->forceFill(['user_id' => $userId])->save();
        }

        return $this->assignType($slot, $typeId);
    }

    /**
     * Code Balance: bind a specific unused slot as the chosen template type (still open until Save).
     */
    public function claimSpecific(int $clientId, int $profileId, string $typeSlag, ?int $userId = null): ?Profile
    {
        $typeId = EquipmentType::query()->where('slag', $typeSlag)->value('id');

        if (! $typeId) {
            return null;
        }

        $slot = Profile::query()
            ->whereKey($profileId)
            ->where('client_id', $clientId)
            ->where('deleted', false)
            ->first();

        if (! $slot) {
            return null;
        }

        // Slot already activated (saved) as a real profile — cannot reclaim.
        if ($slot->update_or_not) {
            // Exception: empty draft wrongly claimed by older create flow — release & reuse.
            if ((int) $slot->type_id === (int) $typeId && ! $this->hasLeftoverCreateContent($slot)) {
                if ($userId) {
                    $slot->forceFill(['user_id' => $userId])->save();
                }

                return $this->assignType($slot, (int) $typeId);
            }

            return null;
        }

        if ($userId) {
            $slot->forceFill(['user_id' => $userId])->save();
        }

        return $this->assignType($slot, (int) $typeId);
    }

    /**
     * True when an open/claimed create draft still carries leftover profile content
     * (template seed rows left as update_or_not=0, abandoned claims, etc.).
     */
    public function hasLeftoverCreateContent(Profile $profile): bool
    {
        $profile->loadMissing(['logos', 'logosExtra', 'pictures', 'picturesExtra', 'documents', 'videos', 'weblinks', 'contacts']);

        if (
            filled(trim((string) $profile->code_profile_name))
            || filled(trim((string) $profile->name))
            || filled(trim((string) $profile->identification))
            || filled(trim((string) $profile->serial_no))
            || filled(trim((string) $profile->description))
            || filled(trim((string) $profile->notes))
            || filled(trim((string) $profile->address))
            || filled(trim((string) $profile->url))
            || filled(trim((string) $profile->password))
            || (bool) $profile->protect
            || (int) ($profile->form_id ?? 0) > 0
            || app(FormBuilderService::class)->profileHasFormQuestions($profile)
        ) {
            return true;
        }

        return $profile->logos->isNotEmpty()
            || $profile->logosExtra->isNotEmpty()
            || $profile->pictures->isNotEmpty()
            || $profile->picturesExtra->isNotEmpty()
            || $profile->documents->isNotEmpty()
            || $profile->videos->isNotEmpty()
            || $profile->weblinks->isNotEmpty()
            || $profile->contacts->isNotEmpty();
    }

    /**
     * Resume path: scrub polluted drafts so /create never shows prior profile data.
     * Keeps the slot open until Save (update_or_not = 0).
     */
    public function scrubIfPollutedCreateDraft(Profile $profile): Profile
    {
        if (! $this->hasLeftoverCreateContent($profile)) {
            // Ensure abandoned create drafts stay in Code Balance until Save.
            if ($profile->update_or_not) {
                $profile->forceFill(['update_or_not' => false])->save();
            }

            return $profile->fresh(['equipmentType', 'client']) ?? $profile->load(['equipmentType', 'client']);
        }

        $typeId = (int) $profile->type_id;
        $this->resetContentForCreate($profile);

        if ($typeId > 0) {
            $profile->forceFill([
                'type_id' => $typeId,
                'update_or_not' => false,
            ])->save();
        }

        return $profile->fresh(['equipmentType', 'client']);
    }

    /**
     * Clear profile content + media so "Add a New Code" matches legacy blank add forms.
     * Preserves slot identity: id, client_id, user_id, expiry, purchase, free_code flags.
     */
    public function resetContentForCreate(Profile $profile): Profile
    {
        $profileId = (int) $profile->id;

        DB::transaction(function () use ($profile, $profileId): void {
            $previousFormId = (int) ($profile->form_id ?: 0);

            $profile->logos()->delete();
            $profile->logosExtra()->delete();
            $profile->pictures()->delete();
            $profile->picturesExtra()->delete();
            $profile->documents()->delete();
            $profile->videos()->delete();
            $profile->weblinks()->delete();
            $profile->contacts()->delete();

            $blank = [
                'name' => '',
                'name2' => '',
                'code_profile_name' => '',
                'identification' => '',
                'serial_no' => '',
                'description' => '',
                'description2' => '',
                'notes' => '',
                'name_company' => '',
                'telephone' => '',
                'mobile' => '',
                'email' => '',
                'address' => '',
                'gps_coordinates' => '',
                'url' => '',
                'shorturl' => '',
                'protect' => false,
                'password' => '',
                'show_header' => false,
                'display_share_link' => false,
                'enable_data_collection' => false,
                'set_up_compulsory' => false,
                'data_collection_mobile' => false,
                'data_collection_email' => false,
                'data_collection_name' => false,
                'data_collection_surname' => false,
                'data_collection_content' => '',
                'data_collection_btn_text' => '',
                'data_collection_btn_color' => '',
                'buttonbackcolor' => '',
                'buttonfontcolor' => '',
                'application' => '',
                'form_id' => 0,
                'form_title' => '',
                'form_active' => false,
                'form_is_enable' => false,
                'form_email_tag' => '',
                'enable_form_analytics' => false,
                'activation_start_date' => null,
                'activation_end_date' => null,
                'voc_first_name' => '',
                'voc_last_name' => '',
                'voc_address' => '',
                'voc_town' => '',
                'voc_state' => '',
                'voc_phone' => '',
                'voc_dob' => null,
                'voc_known_allergies' => '',
                'voc_blood_type' => '',
                'voc_next_of_kin' => '',
                'voc_contact_phone' => '',
                'voc_employer' => '',
                'voc_emp_address' => '',
                'voc_emp_town' => '',
                'voc_emp_state' => '',
                'voc_emp_phone' => '',
                'voc_email_text' => '',
                'voc_email_url' => '',
                'voc_email_sign_line1' => '',
                'voc_email_sign_line2' => '',
                'voc_title_bar_text' => '',
                'voc_title_bar_colour' => '',
            ];

            $fill = [];
            foreach ($blank as $column => $value) {
                if (Schema::hasColumn('profiles', $column)) {
                    $fill[$column] = $value;
                }
            }

            $profile->forceFill($fill)->save();

            // Form Builder questions live on `form_builder_question` (not …_questions).
            app(FormBuilderService::class)->clearFormForCreate(
                $profile->fresh() ?? $profile,
                $previousFormId
            );

            // Optional related tables that may exist on live imports.
            foreach (['checklist_items', 'voc_profile_image', 'voc_recipients', 'voc_users'] as $table) {
                if (Schema::hasTable($table) && Schema::hasColumn($table, 'profile_id')) {
                    DB::table($table)->where('profile_id', $profileId)->delete();
                }
            }
        });

        return $profile->fresh();
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
            "SELECT COUNT(*) AS total,
                    SUM(CASE WHEN update_or_not = '0' AND (expired_at IS NULL OR expired_at > NOW()) THEN 1 ELSE 0 END) AS open
             FROM profiles WHERE client_id = ? AND deleted = '0'",
            [$clientId]
        );

        return [
            'total' => (int) ($row->total ?? 0),
            'open' => (int) ($row->open ?? 0),
        ];
    }
}
