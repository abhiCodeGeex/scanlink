<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VocDocument extends Model
{
    protected $table = 'voc_documents';

    protected $primaryKey = 'voc_document_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['voc_document_id', 'profile_id', 'name', 'expiry_date', 'file_name'];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
