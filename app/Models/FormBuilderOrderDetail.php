<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormBuilderOrderDetail extends Model
{
    protected $table = 'form_builder_order_detail';

    protected $fillable = ['form_builder_order_id', 'profile_id'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(FormBuilderOrder::class, 'form_builder_order_id');
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
