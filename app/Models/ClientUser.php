<?php

namespace App\Models;

use App\Enums\ClientUserRole;
use Database\Factories\ClientUserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'client_id',
    'auth_user_id',
    'email',
    'password',
    'role',
    'status',
    'video_upload',
    'checklist_option',
    'customqr_option',
    'is_password_change',
    'expire_at',
    'notice',
    'client_reseller_code',
    'is_sub_user',
    'access_addcode',
    'access_edit',
    'access_delete',
    'show_code_profile_id_to_acc_user',
    'first_name',
    'last_name',
    'company_name',
    'billing_address',
    'town',
    'phone',
    'postal_code',
])]
#[Hidden(['password'])]
class ClientUser extends Model
{
    /** @use HasFactory<ClientUserFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (ClientUser $member): void {
            $member->syncAuthIdentity();
        });
    }

    protected function casts(): array
    {
        return [
            'role' => ClientUserRole::class,
            'status' => 'boolean',
            'video_upload' => 'boolean',
            'checklist_option' => 'boolean',
            'customqr_option' => 'boolean',
            'is_password_change' => 'boolean',
            'expire_at' => 'datetime',
            'notice' => 'boolean',
            'is_sub_user' => 'boolean',
            'access_addcode' => 'boolean',
            'access_edit' => 'boolean',
            'access_delete' => 'boolean',
            'show_code_profile_id_to_acc_user' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function authUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auth_user_id');
    }

    public function isPrimary(): bool
    {
        return $this->role === ClientUserRole::Primary;
    }

    /**
     * Password is stored only on Laravel users; keep local column empty.
     */
    protected function password(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => null,
            set: function (?string $value): string {
                if (filled($value)) {
                    $this->pendingAuthPassword = $value;
                }

                return '';
            },
        );
    }

    protected ?string $pendingAuthPassword = null;

    protected function syncAuthIdentity(): void
    {
        $email = strtolower(trim((string) ($this->email ?? '')));

        if (! filled($email) || ! str_contains($email, '@')) {
            return;
        }

        $auth = $this->authUser;

        if (! $auth) {
            $auth = User::query()->firstOrNew(['email' => $email]);
        }

        $auth->email = $email;
        $auth->name = trim(implode(' ', array_filter([
            $this->first_name,
            $this->last_name,
        ]))) ?: ($this->company_name ?: $email);
        $auth->user_type = \App\Enums\UserType::Portal;
        $auth->admin_role = null;

        if (filled($this->pendingAuthPassword)) {
            $auth->password = $this->pendingAuthPassword;
            $this->pendingAuthPassword = null;
        } elseif (! $auth->exists) {
            $auth->password = 'changeme';
        }

        $auth->save();
        $this->auth_user_id = $auth->id;
        $this->attributes['password'] = '';
    }
}
