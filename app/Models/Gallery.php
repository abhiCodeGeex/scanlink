<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $table = 'gallery';

    protected $fillable = ['name', 'approve'];

    protected function casts(): array
    {
        return [
            'approve' => 'boolean',
        ];
    }
}
