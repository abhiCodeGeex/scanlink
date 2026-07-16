<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Enums\ClientUserRole;
use App\Filament\Concerns\HandlesDatabaseSaveFailures;
use App\Filament\Resources\Clients\ClientResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClient extends CreateRecord
{
    use HandlesDatabaseSaveFailures;

    protected static string $resource = ClientResource::class;

    /**
     * @var array{email?: string, password?: string, video_upload?: bool}|null
     */
    protected ?array $addUserData = null;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $email = trim((string) ($data['txtUseremail'] ?? ''));

        if (filled($email)) {
            $this->addUserData = [
                'email' => $email,
                'password' => $data['txtUserpassword'] ?? null,
                'video_upload' => (bool) ($data['videopermission'] ?? true),
            ];
        }

        unset($data['txtUseremail'], $data['txtUserpassword'], $data['videopermission']);

        // Live schema: NOT NULL columns with no DB defaults.
        $data['reseller_email'] = filled($data['reseller_email'] ?? null) ? $data['reseller_email'] : '';
        $data['reseller_code'] = filled($data['reseller_code'] ?? null) ? $data['reseller_code'] : '';
        $data['shortcut_title'] = $data['shortcut_title'] ?? '';
        $data['shortcut_image1'] = $data['shortcut_image1'] ?? '';
        $data['shortcut_image2'] = $data['shortcut_image2'] ?? '';

        return $data;
    }

    protected function afterCreate(): void
    {
        $client = $this->record;

        $client->users()->create([
            'email' => $client->email,
            'password' => $client->password ?? 'changeme',
            'role' => ClientUserRole::Primary,
            'status' => true,
            'video_upload' => true,
            'checklist_option' => false,
            'customqr_option' => false,
            'is_password_change' => true,
            'expire_at' => now()->addYear(),
            'first_name' => $client->contact_person ?: '',
            'last_name' => '',
            'company_name' => $client->client_name ?: '',
            'billing_address' => $client->address ?: '',
            'phone' => $client->telephone ?: '',
            'notice' => false,
        ]);

        if ($this->addUserData === null) {
            return;
        }

        $client->users()->create([
            'email' => $this->addUserData['email'],
            'password' => $this->addUserData['password'] ?? 'changeme',
            'role' => ClientUserRole::SubUser,
            'is_sub_user' => true,
            'status' => true,
            'video_upload' => $this->addUserData['video_upload'],
            'checklist_option' => false,
            'customqr_option' => false,
            'is_password_change' => true,
            'expire_at' => now()->addYear(),
            'client_reseller_code' => $client->reseller_code ?: '',
            'first_name' => '',
            'last_name' => '',
            'company_name' => $client->client_name ?: '',
            'billing_address' => $client->address ?: '',
            'phone' => $client->telephone ?: '',
            'notice' => false,
        ]);
    }
}
