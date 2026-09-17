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

    /**
     * A VOCC card holder: someone who signs in through their own card's login rather than
     * as a member of the client. They see their card and nothing else — legacy bounced them
     * out of every dashboard action.
     */
    public function isVocCardHolder(): bool
    {
        return $this->user_type === UserType::Voc
            && ! $this->hasActiveClientMembership();
    }

    /**
     * Standing to use the client portal: an active membership of some client.
     *
     * This is what the client login asks for. Legacy authenticated that door against
     * `client_users`, so an "Additional User Access Login" - which exists only in `voc_users`
     * - could not use it, while an email present in both tables could use either door. This
     * predicate reproduces both halves of that.
     */
    public function hasActiveClientMembership(): bool
    {
        return $this->clientMemberships()->active()->exists();
    }

    /**
     * Standing to use the VOCC login: a `voc_users` row points at this identity.
     *
     * Deliberately not a `user_type` check. VocUserProvisioner::provision() links a voc row to
     * an email that already signs in as a client or admin *without* changing its type, so a
     * real card holder can be user_type = portal; live data already has that shape.
     */
    public function hasVocCardHolderAccess(): bool
    {
        return VocUser::query()->where('auth_user_id', $this->id)->exists();
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

        // The admin panel belongs to admin accounts alone. A client member never gets in —
        // including one with "Enable Admin Access", which in legacy meant full rights inside
        // their own client account, not over ScanLink — and neither does a card holder. Since
        // one email is one login with one type, this also keeps a session that came in at the
        // client or VOCC door from wandering into /admin.
        if ($this->user_type instanceof UserType && ! $this->user_type->canAccessAdminPanel()) {
            return false;
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
