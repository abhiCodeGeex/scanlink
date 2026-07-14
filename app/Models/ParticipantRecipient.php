<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParticipantRecipient extends Model
{
    protected $table = 'participant_recipient';

    protected $primaryKey = 'participant_recipient_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['participant_recipient_id', 'profile_id', 'email'];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
