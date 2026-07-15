<?php

namespace App\Models;

use App\Casts\MysqlEnumBoolean;
use App\Enums\CodeOrderStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormBuilderOrder extends Model
{
    /** Live dump has created_at only. */
    public const UPDATED_AT = null;

    protected $fillable = [
        'client_id', 'email', 'town', 'first_name', 'last_name', 'company_name',
        'billing_address', 'phone', 'postal_code', 'no_of_codes', 'per_code_amount',
        'total_amount', 'status', 'enable', 'exipry_date', 'is_reseller_pricing_code',
    ];

    protected function casts(): array
    {
        return [
            'per_code_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'status' => CodeOrderStatus::class,
            'enable' => MysqlEnumBoolean::class,
            'exipry_date' => 'datetime',
        ];
    }

    /**
     * Live column is ENUM('0','1','2'). Bare integers are MySQL enum indexes, not values.
     */
    protected function isResellerPricingCode(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value): string => (string) ($value ?? '0'),
            set: fn (mixed $value): string => (string) ($value ?? '0'),
        );
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(FormBuilderOrderDetail::class);
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function profileId(): ?int
    {
        return $this->details()->value('profile_id');
    }
}
