<?php

namespace App\Models;

use App\Enums\ProfileCodeType;
use App\Models\Concerns\FillsLegacyNotNullDefaults;
use Database\Factories\ProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'client_id', 'user_id', 'type_id', 'name', 'position', 'code_profile_name', 'identification',
    'serial_no', 'address', 'description', 'notes', 'name_company', 'telephone',
    'mobile', 'email', 'name2', 'description2',
    'gps_coordinates', 'shorturl', 'url', 'protect', 'password', 'code_type', 'color_code', 'show_header',
    'show_name', 'show_description', 'show_address', 'show_telephone', 'show_mobile', 'show_email', 'show_url',
    'buttonbackcolor', 'buttonfontcolor', 'enable_data_collection', 'set_up_compulsory',
    'data_collection_mobile', 'data_collection_email', 'data_collection_name',
    'data_collection_surname', 'data_collection_content', 'data_collection_btn_text',
    'data_collection_btn_color', 'display_share_link', 'application', 'activate_bridge_graphic',
    'deleted', 'update_or_not', 'code_purchase_id', 'form_id', 'form_title',
    'form_active', 'form_is_enable', 'form_submission_format', 'form_email_tag',
    'pop_up_formbuilder', 'free_code', 'is_reseller_code', 'enable_form_analytics',
    'expired_at', 'activation_start_date', 'activation_end_date',
    'voc_first_name', 'voc_last_name', 'voc_address', 'voc_town', 'voc_state', 'voc_phone', 'voc_dob',
    'voc_known_allergies', 'voc_blood_type', 'voc_next_of_kin', 'voc_contact_phone',
    'voc_employer', 'voc_emp_address', 'voc_emp_town', 'voc_emp_state', 'voc_emp_phone',
    'voc_email_text', 'voc_email_url', 'voc_email_sign_line1', 'voc_email_sign_line2',
    'voc_title_bar_enable', 'voc_title_bar_text', 'voc_title_bar_colour',
])]
class Profile extends Model
{
    /** @use HasFactory<ProfileFactory> */
    use FillsLegacyNotNullDefaults;
    use HasFactory;

    protected static function booted(): void
    {
        // Live dump uses zero-dates; Carbon date casts reject them.
        static::creating(function (Profile $profile): void {
            // NOT NULL date columns + NO_ZERO_DATE sql_mode: store a sentinel blank date.
            foreach (['activation_start_date', 'activation_end_date', 'voc_dob'] as $column) {
                $raw = $profile->getAttributes()[$column] ?? null;

                if ($raw === null || $raw === '' || $raw === '0000-00-00') {
                    $profile->attributes[$column] = '1970-01-01';
                }
            }
        });

        static::retrieved(function (Profile $profile): void {
            foreach (['activation_start_date', 'activation_end_date', 'voc_dob'] as $column) {
                $raw = $profile->getAttributes()[$column] ?? null;

                if (in_array((string) $raw, ['0000-00-00', '1970-01-01'], true)) {
                    $profile->attributes[$column] = null;
                }
            }
        });

        static::saving(function (Profile $profile): void {
            foreach (['activation_start_date', 'activation_end_date', 'voc_dob'] as $column) {
                if (! array_key_exists($column, $profile->getAttributes())) {
                    continue;
                }

                $raw = $profile->getAttributes()[$column];

                if ($raw === null || $raw === '') {
                    $profile->attributes[$column] = '1970-01-01';
                }
            }

            // Live DB: password is NOT NULL with no default. Filament sends null when blank.
            if (array_key_exists('password', $profile->getAttributes())) {
                if ($profile->attributes['password'] === null) {
                    if ($profile->exists) {
                        unset($profile->attributes['password']);
                    } else {
                        $profile->attributes['password'] = '';
                    }
                }
            }

            if (! $profile->protect && array_key_exists('password', $profile->getAttributes())) {
                $profile->attributes['password'] = '';
            }

            // Filament may send null for optional legacy NOT NULL text/int columns.
            foreach (static::legacyNotNullDefaults() as $column => $default) {
                if ($column === 'password') {
                    continue;
                }

                if (
                    array_key_exists($column, $profile->getAttributes())
                    && $profile->attributes[$column] === null
                ) {
                    $profile->attributes[$column] = $default;
                }
            }
        });

        // Legacy parity: archiving a profile (deleted -> true) clears its Galatech
        // analytics item (legacy delete calls item/clearItem?key=analytic_key). Fire
        // only on the false->true transition; never on restore/other updates. A remote
        // failure must not break the archive action.
        static::updated(function (Profile $profile): void {
            if (! $profile->wasChanged('deleted') || ! $profile->deleted) {
                return;
            }

            $key = (string) $profile->analytic_key;

            if ($key === '') {
                return;
            }

            try {
                app(\App\Services\AnalyticsApiService::class)->clearItem($key);
            } catch (\Throwable $exception) {
                \Illuminate\Support\Facades\Log::warning('Failed to clear analytics on archive', [
                    'profile_id' => $profile->getKey(),
                    'message' => $exception->getMessage(),
                ]);
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    protected static function legacyNotNullDefaults(): array
    {
        return [
            'code_purchase_id' => 0,
            'form_title' => '',
            'code_profile_name' => '',
            'name' => '',
            'position' => '',
            'identification' => '',
            'serial_no' => '',
            'description' => '',
            'name2' => '',
            'description2' => '',
            'notes' => '',
            'name_company' => '',
            'telephone' => '',
            'address' => '',
            'gps_coordinates' => '',
            'password' => '',
            'show_header' => 0,
            'shorturl' => '',
            'analytic_key' => '',
            'buttonbackcolor' => '',
            'buttonfontcolor' => '',
            'data_collection_mobile' => 0,
            'data_collection_email' => 0,
            'data_collection_name' => '',
            'data_collection_surname' => '',
            'data_collection_content' => '',
            'data_collection_btn_text' => '',
            'data_collection_btn_color' => '',
            'link_button' => 0,
            'link_button_text' => '',
            'link_button_url' => '',
            'link_button_color' => '',
            'url' => '',
            'reseller_client_id' => 0,
            'mobile' => '',
            'email' => '',
            'voc_first_name' => '',
            'voc_last_name' => '',
            'voc_address' => '',
            'voc_town' => '',
            'voc_state' => '',
            'voc_phone' => '',
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
            'tiles_order' => '',
            'color_code' => '',
        ];
    }

    protected function casts(): array
    {
        return [
            'protect' => \App\Casts\LegacyZeroOne::class,
            'show_header' => \App\Casts\LegacyZeroOne::class.':true',
            'show_name' => \App\Casts\LegacyZeroOne::class.':true',
            'show_description' => \App\Casts\LegacyZeroOne::class.':true',
            'show_address' => \App\Casts\LegacyZeroOne::class.':true',
            'show_telephone' => \App\Casts\LegacyZeroOne::class.':true',
            'show_mobile' => \App\Casts\LegacyZeroOne::class.':true',
            'show_email' => \App\Casts\LegacyZeroOne::class.':true',
            'show_url' => \App\Casts\LegacyZeroOne::class.':true',
            'voc_title_bar_enable' => \App\Casts\LegacyZeroOne::class.':true',
            'enable_data_collection' => \App\Casts\LegacyZeroOne::class.':true',
            'set_up_compulsory' => \App\Casts\LegacyZeroOne::class,
            'data_collection_name' => \App\Casts\LegacyZeroOne::class,
            'data_collection_surname' => \App\Casts\LegacyZeroOne::class,
            'data_collection_email' => \App\Casts\LegacyZeroOne::class.':true',
            'data_collection_mobile' => \App\Casts\LegacyZeroOne::class.':true',
            'display_share_link' => \App\Casts\LegacyZeroOne::class,
            'activate_bridge_graphic' => \App\Casts\LegacyZeroOne::class.':true',
            'link_button' => \App\Casts\LegacyZeroOne::class.':true',
            // Live DB uses enum('0','1') — boolean cast sends int 0 which MySQL truncates.
            'deleted' => \App\Casts\LegacyZeroOne::class,
            'update_or_not' => \App\Casts\LegacyZeroOne::class,
            'form_active' => \App\Casts\LegacyZeroOne::class,
            'form_is_enable' => \App\Casts\LegacyZeroOne::class,
            'enable_form_analytics' => \App\Casts\LegacyZeroOne::class,
            'pop_up_formbuilder' => \App\Casts\LegacyZeroOne::class,
            'free_code' => \App\Casts\LegacyZeroOne::class,
            'is_reseller_code' => \App\Casts\LegacyZeroOne::class,
            'form_submission_format' => 'integer',
            'code_type' => ProfileCodeType::class,
            'expired_at' => 'datetime',
            'activation_start_date' => 'date',
            'activation_end_date' => 'date',
            'voc_dob' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class, 'user_id');
    }

    public function equipmentType(): BelongsTo
    {
        return $this->belongsTo(EquipmentType::class, 'type_id');
    }

    public function codePurchase(): BelongsTo
    {
        return $this->belongsTo(CodePurchase::class, 'code_purchase_id');
    }

    public function logos(): HasMany
    {
        return $this->hasMany(Logo::class);
    }

    public function logosExtra(): HasMany
    {
        return $this->hasMany(LogoExtra::class);
    }

    public function pictures(): HasMany
    {
        return $this->hasMany(Picture::class);
    }

    public function picturesExtra(): HasMany
    {
        return $this->hasMany(PictureExtra::class);
    }

    public function documents(): HasMany
    {
        // Legacy parity: documents display in their saved drag-order (sort_order),
        // falling back to insertion order for rows never reordered.
        return $this->hasMany(Document::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    public function videosPrimary(): HasMany
    {
        return $this->hasMany(Video::class)->where('is_extra', false);
    }

    public function videosExtra(): HasMany
    {
        return $this->hasMany(Video::class)->where('is_extra', true);
    }

    public function weblinks(): HasMany
    {
        return $this->hasMany(Weblink::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ProfileContact::class);
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(ChecklistItem::class);
    }

    public function formQuestions(): HasMany
    {
        return $this->hasMany(FormBuilderQuestion::class)->orderBy('question_order');
    }

    public function formAnswers(): HasMany
    {
        return $this->hasMany(FormBuilderAnswer::class);
    }

    public function visitors(): HasMany
    {
        return $this->hasMany(VisitorContact::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function vocDocuments(): HasMany
    {
        return $this->hasMany(VocDocument::class);
    }

    public function vocRecipients(): HasMany
    {
        return $this->hasMany(VocRecipient::class);
    }

    public function vocUsers(): HasMany
    {
        return $this->hasMany(VocUser::class);
    }

    public function qrImage(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(QrImage::class, 'profile_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('deleted', false);
    }

    /**
     * Unused paid code slots (Code Balance / draft claim).
     * Live column is enum('0','1') — boolean false does not match.
     */
    public function scopeOpenSlot(Builder $query): Builder
    {
        return $query->where('update_or_not', '0');
    }

    /**
     * Activated / claimed profiles.
     */
    public function scopeClaimedSlot(Builder $query): Builder
    {
        return $query->where('update_or_not', '1');
    }

    /**
     * Codes that participate in expiry / renewal handling.
     *
     * Legacy (paging.php / profileexpiry.php): free codes are ignored for expiry &
     * renewal reminders UNLESS they have been flagged renewal_required. Equivalent to
     * (free_code = 0 OR (free_code = 1 AND renewal_required = 1)).
     */
    public function scopeExpiryManaged(Builder $query): Builder
    {
        return $query->where(function (Builder $inner): void {
            $inner->where('free_code', 0)
                ->orWhere('renewal_required', 1);
        });
    }

    /**
     * Filter by Master Code List row colour buckets (expired / expiring / active).
     * Mirrors expiryStatusClass() so legend clicks match painted rows.
     */
    public function scopeWhereExpiryStatus(Builder $query, string $status): Builder
    {
        $now = now();
        $within30 = now()->copy()->addDays(30);

        return match ($status) {
            'expired' => $query
                ->expiryManaged()
                ->whereNotNull('expired_at')
                ->where('expired_at', '<', $now),
            'expiring' => $query
                ->expiryManaged()
                ->whereNotNull('expired_at')
                ->where('expired_at', '>=', $now)
                ->where('expired_at', '<=', $within30),
            'active' => $query->where(function (Builder $q) use ($within30): void {
                $q->whereNull('expired_at')
                    ->orWhere(function (Builder $free): void {
                        // Free codes not flagged for renewal stay "Active" regardless of date.
                        $free->where('free_code', 1)
                            ->where(function (Builder $renewal): void {
                                $renewal->where('renewal_required', 0)
                                    ->orWhereNull('renewal_required');
                            });
                    })
                    ->orWhere('expired_at', '>', $within30);
            }),
            default => $query,
        };
    }

    public function scopeLegacyVisible(Builder $query): Builder
    {
        return $query->where('deleted', false)
            ->where(function (Builder $q): void {
                $q->whereBetween('type_id', [1, 8])
                    ->orWhere(function (Builder $inner): void {
                        $inner->whereHas('equipmentType', fn (Builder $t) => $t->where('slag', 'code'))
                            ->where('update_or_not', '1');
                    });
            });
    }

    /**
     * Human label for selects / tables. Many legacy profiles leave `name` empty.
     */
    public function displayLabel(): string
    {
        $candidates = [
            $this->name,
            $this->code_profile_name,
            $this->identification,
            $this->form_title,
            $this->getAttribute('name2'),
            $this->url,
            $this->shorturl,
            $this->getAttribute('application'),
        ];

        foreach ($candidates as $candidate) {
            $label = trim((string) $candidate);

            if ($label !== '') {
                return $label;
            }
        }

        return 'Profile #'.$this->getKey();
    }

    /**
     * @param  callable(\Illuminate\Database\Eloquent\Builder): void|null  $constrain
     * @return \Illuminate\Support\Collection<int|string, string>
     */
    public static function selectOptionsForClient(int $clientId, ?callable $constrain = null): \Illuminate\Support\Collection
    {
        $query = static::query()
            ->where('client_id', $clientId)
            ->active();

        if ($constrain) {
            $constrain($query);
        }

        return $query
            ->orderByRaw("COALESCE(NULLIF(TRIM(name), ''), NULLIF(TRIM(code_profile_name), ''), NULLIF(TRIM(identification), ''), NULLIF(TRIM(form_title), ''), CONCAT('Profile #', id))")
            ->get(['id', 'name', 'code_profile_name', 'identification', 'form_title', 'name2', 'url', 'shorturl'])
            ->mapWithKeys(fn (self $profile): array => [$profile->id => $profile->displayLabel()]);
    }

    public function isExpired(): bool
    {
        if ($this->expired_at === null) {
            return false;
        }

        if ((bool) $this->free_code) {
            $renewalRequired = (bool) ($this->getAttribute('renewal_required') ?? false);

            if (! $renewalRequired) {
                return false;
            }
        }

        return $this->expired_at->isPast();
    }

    /**
     * Legacy mastercode row colour class (red / orange / green).
     */
    public function expiryStatusClass(): string
    {
        if ($this->expired_at === null) {
            return 'sl-row-active';
        }

        // Legacy: a free code only counts as active/ignored while it is NOT flagged
        // for renewal. A free code with renewal_required is coloured like any other.
        if ((bool) $this->free_code && ! (bool) ($this->getAttribute('renewal_required') ?? false)) {
            return 'sl-row-active';
        }

        if ($this->expired_at->isPast()) {
            return 'sl-row-expired';
        }

        if ($this->expired_at->lte(now()->addDays(30))) {
            return 'sl-row-expiring';
        }

        return 'sl-row-active';
    }

    public function typeSlug(): ?string
    {
        return $this->equipmentType?->slag;
    }
}
