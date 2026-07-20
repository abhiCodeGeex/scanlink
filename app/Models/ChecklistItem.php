<?php

namespace App\Models;

use App\Models\Concerns\FillsLegacyNotNullDefaults;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistItem extends Model
{
    use FillsLegacyNotNullDefaults;

    protected $table = 'checklist_item';

    /** Live `checklist_item` table has no timestamp columns. */
    public $timestamps = false;

    protected $fillable = ['profile_id', 'checklist_item', 'datetime'];

    /**
     * @return array<string, mixed>
     */
    protected static function legacyNotNullDefaults(): array
    {
        return [
            'checklist_item' => '',
            'datetime' => '1970-01-01 00:00:00',
        ];
    }

    protected function casts(): array
    {
        return ['datetime' => 'datetime'];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
