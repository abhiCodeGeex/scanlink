<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormBuilderLibrary extends Model
{
    protected $table = 'form_builder_library';

    protected $primaryKey = 'form_builder_library_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'form_builder_library_id', 'form_id', 'user_id', 'form_title',
        'is_deleted', 'is_deleted_from_library',
    ];

    protected function casts(): array
    {
        return [
            'is_deleted' => 'boolean',
            'is_deleted_from_library' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(ClientUser::class, 'user_id');
    }
}
