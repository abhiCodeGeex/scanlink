<?php

namespace App\Models;

use App\Observers\VocUserObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([VocUserObserver::class])]
class VocUser extends Model
{
    protected $table = 'voc_users';

    protected $primaryKey = 'voc_user_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['voc_user_id', 'profile_id', 'email', 'password', 'auth_user_id'];

    protected $hidden = ['password'];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function authUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auth_user_id');
    }
}
