<?php

namespace App\Models;

use App\Casts\MysqlEnumBoolean;
use App\Enums\CodeOrderStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

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

    /**
     * Legacy form_builder_orders has no total_amount column — the total was always
     * derived from quantity x unit price. Fall back to that when it is absent/empty.
     */
    public function totalAmount(): float
    {
        if (static::hasTotalAmountColumn()) {
            $stored = (float) ($this->getAttribute('total_amount') ?? 0);

            if ($stored > 0) {
                return round($stored, 2);
            }
        }

        return round((float) $this->no_of_codes * (float) $this->per_code_amount, 2);
    }

    public static function hasTotalAmountColumn(): bool
    {
        return once(fn (): bool => Schema::hasColumn((new static)->getTable(), 'total_amount'));
    }

    public function profileId(): ?int
    {
        return $this->details()->value('profile_id');
    }
}
