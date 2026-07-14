<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VocRecipient extends Model
{
    protected $table = 'voc_recipients';

    protected $primaryKey = 'voc_recipient_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['voc_recipient_id', 'profile_id', 'email'];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
