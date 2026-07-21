<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormBuilderRecipient extends Model
{
    protected $table = 'form_builder_recipient';

    protected $primaryKey = 'recipient_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['recipient_id', 'form_id', 'recipient_email'];

    public function formProfile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'form_id', 'form_id');
    }
}
