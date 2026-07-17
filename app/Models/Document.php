<?php

namespace App\Models;

use App\Models\Concerns\FillsLegacyNotNullDefaults;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use FillsLegacyNotNullDefaults;

    protected $table = 'documents';

    protected $fillable = [
        'client_id', 'user_id', 'profile_id', 'name', 'btn_color',
        'doc_name', 'txt_align', 'sort_order', 'is_temp',
    ];

    /**
     * @return array<string, mixed>
     */
    protected static function legacyNotNullDefaults(): array
    {
        return [
            'name' => '',
            'btn_color' => '',
            'doc_name' => '',
            'txt_align' => 'left',
            'sort_order' => 0,
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
