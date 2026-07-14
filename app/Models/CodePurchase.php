<?php

namespace App\Models;

use App\Enums\CodeOrderStatus;
use Database\Factories\CodePurchaseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'client_id',
    'email',
    'town',
    'first_name',
    'last_name',
    'company_name',
    'billing_address',
    'phone',
    'postal_code',
    'no_of_codes',
    'per_code_amount',
    'total_amount',
    'transaction_id',
    'status',
    'enable',
    'exipry_date',
    'is_reseller_pricing_code',
    'reseller_client_id',
    'free_code',
    'ordered_on',
])]
class CodePurchase extends Model
{
    /** @use HasFactory<CodePurchaseFactory> */
    use HasFactory;

    protected $table = 'code_purchase';

    protected function casts(): array
    {
        return [
            'per_code_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'status' => CodeOrderStatus::class,
            'enable' => 'boolean',
            'exipry_date' => 'datetime',
            'is_reseller_pricing_code' => 'boolean',
            'free_code' => 'boolean',
            'ordered_on' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'reseller_client_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(CodePurchaseDetail::class, 'code_purchase_id');
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
