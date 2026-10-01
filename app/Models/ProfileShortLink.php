<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One short code we issued for one profile.
 *
 * The row is the permanent part. What it redirects to is worked out fresh on every scan
 * rather than stored, so a client renaming their URL cannot strand a code that is already
 * printed on a label somewhere.
 */
#[Fillable(['profile_id', 'code'])]
class ProfileShortLink extends Model
{
    protected $table = 'profile_short_links';

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
