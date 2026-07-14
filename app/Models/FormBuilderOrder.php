<?php

namespace App\Models;

use App\Enums\CodeOrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormBuilderOrder extends Model
{
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
            'enable' => 'boolean',
            'exipry_date' => 'datetime',
            'is_reseller_pricing_code' => 'boolean',
        ];
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
