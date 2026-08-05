<?php

namespace App\Models;

use App\Casts\MysqlEnumBoolean;
use App\Models\Concerns\FillsLegacyNotNullDefaults;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'client_name',
    'address',
    'telephone',
    'contact_person',
    'regi_date',
    'email',
    'password',
    'url',
    'approve',
    'shortcut_title',
    'shortcut_image1',
    'shortcut_image2',
    'reseller_code',
    'reseller_code_active',
    'reseller_email',
    'is_password_change',
])]
class Client extends Model
{
    /** @use HasFactory<ClientFactory> */
    use FillsLegacyNotNullDefaults;
    use HasFactory;
    use SoftDeletes;

    /**
     * @return array<string, mixed>
     */
    protected static function legacyNotNullDefaults(): array
    {
        return [
            'shortcut_title' => '',
            'shortcut_image1' => '',
            'shortcut_image2' => '',
            'reseller_code' => '',
            'reseller_code_active' => true,
            'reseller_email' => '',
            'is_password_change' => false,
        ];
    }

    protected function casts(): array
    {
        return [
            'regi_date' => 'date',
            'approve' => MysqlEnumBoolean::class,
            'reseller_code_active' => MysqlEnumBoolean::class,
            'is_password_change' => 'boolean',
        ];
    }

    /**
     * Live schema stores empty reseller emails as '' (column is NOT NULL).
     */
    protected function resellerEmail(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value): string => $value ?? '',
        );
    }

    public function users(): HasMany
    {
        return $this->hasMany(ClientUser::class);
    }

    public function primaryUser(): HasOne
    {
        return $this->hasOne(ClientUser::class)->where('role', 5);
    }

    public function subUsers(): HasMany
    {
        return $this->hasMany(ClientUser::class)->where('role', 1);
    }

    public function codePurchases(): HasMany
    {
        return $this->hasMany(CodePurchase::class);
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    public function resellerName(): ?string
    {
        $resellerCode = $this->primaryUser?->client_reseller_code;

        if (! filled($resellerCode)) {
            return null;
        }

        return static::query()
            ->activeResellerCode()
            ->where('reseller_code', $resellerCode)
            ->value('client_name');
    }

    /**
     * Clients that own a reseller code (assigned, non-empty).
     */
    public function scopeHasResellerCode($query)
    {
        return $query
            ->whereNotNull('reseller_code')
            ->where('reseller_code', '!=', '');
    }

    /**
     * Reseller code is assigned and currently active (usable in purchase / register).
     */
    public function scopeActiveResellerCode($query)
    {
        $query = $query->hasResellerCode();

        if (\Illuminate\Support\Facades\Schema::hasColumn('clients', 'reseller_code_active')) {
            $query->where('reseller_code_active', '1');
        }

        return $query;
    }

    public static function findByResellerCode(string $code, bool $activeOnly = true): ?self
    {
        $code = trim($code);

        if ($code === '') {
            return null;
        }

        $query = static::query()->where('reseller_code', $code);

        if ($activeOnly) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('clients', 'reseller_code_active')) {
                $query->where('reseller_code_active', '1');
            }
        }

        return $query->first();
    }

    public function isResellerCodeActive(): bool
    {
        if (! filled($this->reseller_code)) {
            return false;
        }

        if (! \Illuminate\Support\Facades\Schema::hasColumn('clients', 'reseller_code_active')) {
            return true;
        }

        return (bool) $this->reseller_code_active;
    }
}
