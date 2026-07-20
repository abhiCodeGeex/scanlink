<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $table = 'testimonial';

    /** Live dump has created_at only. */
    public const UPDATED_AT = null;

    protected $fillable = ['title', 'video'];
}
