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
            'reseller_email' => '',
            'is_password_change' => false,
        ];
    }

    protected function casts(): array
    {
        return [
            'regi_date' => 'date',
            'approve' => MysqlEnumBoolean::class,
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
            ->where('reseller_code', $resellerCode)
            ->value('client_name');
    }
}
