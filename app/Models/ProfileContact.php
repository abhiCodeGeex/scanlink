<?php

namespace App\Models;

use App\Models\Concerns\FillsLegacyNotNullDefaults;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileContact extends Model
{
    use FillsLegacyNotNullDefaults;

    protected $table = 'profile_contact';

    /** Live table uses datestamp, not Laravel timestamps. */
    public $timestamps = false;

    protected $fillable = ['profile_id', 'name_company', 'telephone', 'datestamp'];

    /**
     * @return array<string, mixed>
     */
    protected static function legacyNotNullDefaults(): array
    {
        return [
            'name_company' => '',
            'telephone' => '',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $contact): void {
            if (empty($contact->datestamp)) {
                $contact->datestamp = now();
            }
        });
    }

    protected function casts(): array
    {
        return ['datestamp' => 'datetime'];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
