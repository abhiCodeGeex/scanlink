<?php

namespace App\Filament\Portal\Auth;

use App\Enums\ClientUserRole;
use App\Enums\UserType;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\User;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SensitiveParameter;

class Register extends BaseRegister
{
    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                TextInput::make('company_name')
                    ->label('Company name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Phone')
                    ->tel()
                    ->maxLength(50),
                TextInput::make('client_reseller_code')
                    ->label('Reseller code')
                    ->maxLength(255)
                    ->helperText('Optional — enter your reseller code if you were referred by a partner.'),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRegistration(#[SensitiveParameter] array $data): Model
    {
        return DB::transaction(function () use ($data): User {
            $baseUrl = Str::slug((string) $data['company_name']) ?: 'client';
            $url = $baseUrl;
            $suffix = 1;

            while (Client::query()->where('url', $url)->exists()) {
                $url = $baseUrl.'-'.$suffix;
                $suffix++;
            }

            $resellerCode = trim((string) ($data['client_reseller_code'] ?? ''));

            if (filled($resellerCode)) {
                $resellerExists = Client::query()
                    ->where('reseller_code', $resellerCode)
                    ->exists();

                if (! $resellerExists) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'client_reseller_code' => 'Enter a valid reseller code.',
                    ]);
                }
            }

            $client = Client::query()->create([
                'client_name' => $data['company_name'],
                'contact_person' => $data['name'],
                'email' => $data['email'],
                'telephone' => $data['phone'] ?? null,
                'url' => $url,
                'approve' => true,
                'regi_date' => now(),
            ]);

            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'user_type' => UserType::Portal,
                'admin_role' => null,
            ]);

            $nameParts = preg_split('/\s+/', trim((string) $data['name']), 2) ?: [];

            ClientUser::query()->create([
                'client_id' => $client->id,
                'auth_user_id' => $user->id,
                'email' => $data['email'],
                'password' => '',
                'role' => ClientUserRole::Primary,
                'status' => true,
                'is_sub_user' => false,
                'first_name' => $nameParts[0] ?? $data['name'],
                'last_name' => $nameParts[1] ?? null,
                'company_name' => $data['company_name'],
                'phone' => $data['phone'] ?? null,
                'client_reseller_code' => filled($resellerCode) ? $resellerCode : null,
                'is_password_change' => true,
                'expire_at' => now()->addYear(),
            ]);

            return $user->fresh();
        });
    }
}
