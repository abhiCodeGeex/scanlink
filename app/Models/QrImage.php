<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class QrImage extends Model
{
    protected $table = 'qrimage';

    public $timestamps = true;

    protected $fillable = ['profile_id', 'qrimg_name'];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function publicUrl(): ?string
    {
        if (! $this->qrimg_name) {
            return null;
        }

        $path = str_starts_with($this->qrimg_name, 'storage/')
            ? $this->qrimg_name
            : 'storage/'.$this->qrimg_name;

        return asset($path);
    }

    public function diskPath(): string
    {
        return str_replace('storage/', '', $this->qrimg_name);
    }
}
