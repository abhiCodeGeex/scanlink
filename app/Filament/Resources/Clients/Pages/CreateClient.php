<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Enums\ClientUserRole;
use App\Filament\Concerns\HandlesDatabaseSaveFailures;
use App\Filament\Resources\Clients\ClientResource;
use App\Mail\ClientWelcomeNotification;
use App\Models\Client;
use App\Support\SystemNotifier;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateClient extends CreateRecord
{
    use HandlesDatabaseSaveFailures {
        create as createWithDatabaseFailureHandling;
    }

    protected static string $resource = ClientResource::class;

    /**
     * @var array{email?: string, password?: string, video_upload?: bool}|null
     */
    protected ?array $addUserData = null;

    protected ?string $plainPassword = null;

    public function create(bool $another = false): void
    {
        $this->capturePlainPasswordFromForm();

        $this->createWithDatabaseFailureHandling($another);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->capturePlainPasswordFromForm($data);

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
        $data['reseller_code_active'] = filled($data['reseller_code']);
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
            'is_password_change' => false,
            'expire_at' => now()->addYear(),
            'first_name' => $client->contact_person ?: '',
            'last_name' => '',
            'company_name' => $client->client_name ?: '',
            'billing_address' => $client->address ?: '',
            'phone' => $client->telephone ?: '',
            'notice' => false,
        ]);

        if ($this->addUserData !== null) {
            $client->users()->create([
                'email' => $this->addUserData['email'],
                'password' => $this->addUserData['password'] ?? 'changeme',
                'role' => ClientUserRole::SubUser,
                'is_sub_user' => true,
                'status' => true,
                'video_upload' => $this->addUserData['video_upload'],
                'checklist_option' => false,
                'customqr_option' => false,
                'is_password_change' => false,
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

        $this->sendWelcomeEmail($client);
    }

    protected function sendWelcomeEmail(Client $client): void
    {
        if (! filled($client->email)) {
            return;
        }

        $plainPassword = $this->plainPassword ?? '';

        if (blank($plainPassword)) {
            Notification::make()
                ->title('Client created, but welcome email was not sent.')
                ->body('The portal password was missing when the client was saved.')
                ->warning()
                ->send();

            return;
        }

        try {
            Mail::to($client->email)->send(new ClientWelcomeNotification($client, $plainPassword));

            SystemNotifier::toClientPrimary(
                $client,
                'Welcome to ScanLink',
                'Your ScanLink portal account has been created — check your email for login details.',
                'heroicon-o-user-plus',
                'success',
            );
        } catch (\Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('Client created, but the welcome email could not be sent.')
                ->body('Check mail settings or resend credentials manually.')
                ->warning()
                ->persistent()
                ->send();

            return;
        }

        Notification::make()
            ->title('Welcome email sent')
            ->body("Login details were emailed to {$client->email}.")
            ->success()
            ->send();
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Client created successfully.');
    }

    protected function getRedirectUrl(): string
    {
        return ClientResource::getUrl('index');
    }

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->hidden();
    }

    /**
     * @param  array<string, mixed>|null  $data
     */
    protected function capturePlainPasswordFromForm(?array $data = null): void
    {
        $password = data_get($data ?? $this->form->getState(), 'password');

        if (filled($password)) {
            $this->plainPassword = (string) $password;
        }
    }
}
