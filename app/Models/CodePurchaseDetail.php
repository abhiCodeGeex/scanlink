<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'code_purchase_id',
    'profile_id',
    'amount',
])]
class CodePurchaseDetail extends Model
{
    protected $table = 'code_purchase_detail';

    /** Live dump has created_at only. */
    public const UPDATED_AT = null;

    public function codePurchase(): BelongsTo
    {
        return $this->belongsTo(CodePurchase::class, 'code_purchase_id');
    }
}
