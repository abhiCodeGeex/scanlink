<?php

namespace App\Models;

use App\Models\Concerns\FillsLegacyNotNullDefaults;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The VOCC card holder's photograph, as stored by the legacy app.
 *
 * New uploads go to the shared `picture` table through the profile editor. This model exists
 * so photos that came across in the live import are still shown on the card — without it a
 * VOCC imported from the old system renders as a list of claims about a person with no
 * picture to check them against.
 */
class VocProfileImage extends Model
{
    use FillsLegacyNotNullDefaults;

    protected $table = 'voc_profile_image';

    protected $primaryKey = 'voc_profile_image_id';

    public $timestamps = false;

    protected $fillable = ['client_id', 'user_id', 'profile_id', 'image_name', 'created_at', 'is_temp'];

    /**
     * @return array<string, mixed>
     */
    protected static function legacyNotNullDefaults(): array
    {
        return [
            'image_name' => '',
            'is_temp' => 0,
        ];
    }

    protected function casts(): array
    {
        return ['is_temp' => 'boolean'];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
