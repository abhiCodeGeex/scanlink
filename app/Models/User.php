<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\AdminRole;
use App\Enums\UserType;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'admin_role', 'user_type'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
    use InteractsWithAppAuthentication;
    use InteractsWithAppAuthenticationRecovery;

    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            if ($user->user_type === null) {
                $user->user_type = UserType::Admin;
            }

            if ($user->user_type === UserType::Admin && $user->admin_role === null) {
                $user->admin_role = AdminRole::Support;
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'admin_role' => AdminRole::class,
            'user_type' => UserType::class,
        ];
    }

    public function clientMemberships(): HasMany
    {
        return $this->hasMany(ClientUser::class, 'auth_user_id');
    }

    public function primaryClientMembership(): HasOne
    {
        return $this->hasOne(ClientUser::class, 'auth_user_id')->where('role', 5);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'portal') {
            if ($this->user_type === UserType::Voc) {
                return true;
            }

            if ($this->user_type !== UserType::Portal) {
                return false;
            }

            return $this->clientMemberships()
                ->active()
                ->exists();
        }

        if ($this->user_type instanceof UserType && ! $this->user_type->canAccessAdminPanel()) {
            // Portal users with legacy enable_admin_access may enter admin.
            $hasAdminAccessColumn = once(
                fn (): bool => \Illuminate\Support\Facades\Schema::hasColumn('client_users', 'enable_admin_access'),
            );

            $portal = $this->clientMemberships()
                ->where(function ($q) use ($hasAdminAccessColumn): void {
                    if ($hasAdminAccessColumn) {
                        $q->where('enable_admin_access', 1);
                    } else {
                        $q->whereRaw('0 = 1');
                    }
                })
                ->active()
                ->exists();

            if (! $portal) {
                return false;
            }
        }

        if ($this->admin_role instanceof AdminRole) {
            return $this->admin_role->canAccessPanel();
        }

        return str_ends_with((string) $this->email, '@scanlink.com')
            || $this->id === 1;
    }

    public function canManageAdmins(): bool
    {
        return $this->admin_role === AdminRole::SuperAdmin;
    }
}
