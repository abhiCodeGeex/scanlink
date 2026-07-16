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
    'client_id', 'user_id', 'type_id', 'name', 'code_profile_name', 'identification',
    'serial_no', 'address', 'description', 'notes', 'name_company', 'telephone',
    'shorturl', 'url', 'protect', 'password', 'code_type', 'color_code', 'show_header',
    'buttonbackcolor', 'buttonfontcolor', 'enable_data_collection', 'set_up_compulsory',
    'data_collection_mobile', 'data_collection_email', 'data_collection_name',
    'data_collection_content', 'display_share_link', 'application', 'activate_bridge_graphic',
    'deleted', 'update_or_not', 'code_purchase_id', 'form_id', 'form_title',
    'form_active', 'form_is_enable', 'form_submission_format', 'form_email_tag',
    'pop_up_formbuilder', 'free_code', 'is_reseller_code',
    'expired_at', 'activation_start_date', 'activation_end_date',
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
            foreach (['activation_start_date', 'activation_end_date', 'voc_dob'] as $column) {
                if (! array_key_exists($column, $profile->getAttributes()) || $profile->getAttributes()[$column] === null) {
                    $profile->attributes[$column] = '0000-00-00';
                }
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
            'protect' => 'boolean',
            'show_header' => 'boolean',
            'enable_data_collection' => 'boolean',
            'set_up_compulsory' => 'boolean',
            'display_share_link' => 'boolean',
            'activate_bridge_graphic' => 'boolean',
            'deleted' => 'boolean',
            'update_or_not' => 'boolean',
            'form_active' => 'boolean',
            'form_is_enable' => 'boolean',
            'pop_up_formbuilder' => 'boolean',
            'free_code' => 'boolean',
            'is_reseller_code' => 'boolean',
            'code_type' => ProfileCodeType::class,
            'expired_at' => 'datetime',
            'activation_start_date' => 'date',
            'activation_end_date' => 'date',
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

    public function pictures(): HasMany
    {
        return $this->hasMany(Picture::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
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

    public function qrImage(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(QrImage::class, 'profile_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('deleted', false);
    }

    public function scopeLegacyVisible(Builder $query): Builder
    {
        return $query->where('deleted', false)
            ->where(function (Builder $q): void {
                $q->whereBetween('type_id', [1, 8])
                    ->orWhere(function (Builder $inner): void {
                        $inner->whereHas('equipmentType', fn (Builder $t) => $t->where('slag', 'code'))
                            ->where('update_or_not', true);
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
        return $this->expired_at !== null && $this->expired_at->isPast();
    }

    public function typeSlug(): ?string
    {
        return $this->equipmentType?->slag;
    }
}
