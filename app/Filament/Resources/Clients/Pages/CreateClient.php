<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Enums\ClientUserRole;
use App\Filament\Resources\Clients\ClientResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClient extends CreateRecord
{
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
            'client_reseller_code' => $client->reseller_code,
        ]);
    }
}
