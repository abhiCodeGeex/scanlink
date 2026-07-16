<?php

namespace App\Models;

use App\Models\Concerns\FillsLegacyNotNullDefaults;
use Database\Factories\ProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Minimal Profile model for Client Sub Divide (transfer profiles between clients).
 */
#[Fillable([
    'client_id', 'user_id', 'type_id', 'name', 'code_profile_name', 'identification',
    'deleted', 'update_or_not',
])]
class Profile extends Model
{
    /** @use HasFactory<ProfileFactory> */
    use FillsLegacyNotNullDefaults;
    use HasFactory;

    /**
     * @return array<string, mixed>
     */
    protected static function legacyNotNullDefaults(): array
    {
        return [
            'name' => '',
            'code_profile_name' => '',
            'identification' => '',
        ];
    }

    protected function casts(): array
    {
        return [
            'deleted' => 'boolean',
            'update_or_not' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class, 'user_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('deleted', false);
    }

    public function scopeLegacyVisible(Builder $query): Builder
    {
        return $query->where('deleted', false)
            ->whereBetween('type_id', [1, 8]);
    }
}
