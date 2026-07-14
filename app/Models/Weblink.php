<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Weblink extends Model
{
    protected $table = 'weblink';

    protected $fillable = [
        'profile_id', 'link_button', 'link_button_text',
        'link_button_url', 'link_button_color', 'link_button_align',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
