<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;

class GlobalSettings extends Page
{
    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->admin_role?->canManageSettings();
    }

    protected static ?string $navigationLabel = 'Global Settings';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Global Settings';

    /**
     * @return list<string>
     */
    public static function settingKeys(): array
    {
        return [
            'paypal_email',
            'contact_email',
            'youtube_client_id',
            'youtube_client_secret',
            'youtube_refresh_token',
            'youtube_developer_key',
            'youtube_application_id',
            // Legacy keys kept for reference; ClientLogin upload no longer works.
            'youtube_username',
            'youtube_password',
        ];
    }

    public static function settingLabel(string $key): string
    {
        return ucwords(str_replace('_', ' ', $key));
    }

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $data = [];

        foreach (self::settingKeys() as $key) {
            $data[$key] = Setting::valueFor($key) ?? '';
        }

        $this->form->fill($data);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Email & payments')
                    ->schema([
                        $this->settingField('paypal_email'),
                        $this->settingField('contact_email'),
                    ]),
                Section::make('YouTube OAuth (video upload)')
                    ->description('From Google Cloud Console → Credentials → OAuth 2.0 Web client. Do not swap Client ID and Client Secret.')
                    ->schema([
                        $this->settingField('youtube_client_id')
                            ->helperText('Ends with .apps.googleusercontent.com — NOT the GOCSPX secret.'),
                        $this->settingField('youtube_client_secret')
                            ->helperText('Starts with GOCSPX- — the secret key, not the client id.'),
                        $this->settingField('youtube_refresh_token')
                            ->required(false)
                            ->helperText('Leave blank, then run: docker compose exec app php artisan youtube:authorize'),
                        $this->settingField('youtube_developer_key')
                            ->required(false)
                            ->helperText('Optional API key from Credentials → API key.'),
                        $this->settingField('youtube_application_id')
                            ->required(false)
                            ->helperText('Optional display name (legacy field).'),
                    ]),
                Section::make('YouTube legacy (unused)')
                    ->collapsed()
                    ->schema([
                        $this->settingField('youtube_username')->required(false),
                        $this->settingField('youtube_password')->required(false),
                    ]),
            ]);
    }

    protected function settingField(string $key): TextInput
    {
        $field = TextInput::make($key)
            ->label(self::settingLabel($key))
            ->maxLength(255);

        if (! in_array($key, ['youtube_refresh_token', 'youtube_username', 'youtube_password', 'youtube_developer_key', 'youtube_application_id'], true)) {
            $field->required();
        }

        if (in_array($key, ['youtube_password', 'youtube_client_secret', 'youtube_refresh_token'], true)) {
            $field->password()->revealable();
        }

        if (in_array($key, ['youtube_username', 'youtube_password'], true)) {
            $field->helperText('Legacy only — OAuth is used for uploads.');
        }

        return $field;
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $clientId = trim((string) ($data['youtube_client_id'] ?? ''));
        $clientSecret = trim((string) ($data['youtube_client_secret'] ?? ''));
        $refreshToken = trim((string) ($data['youtube_refresh_token'] ?? ''));

        $errors = [];

        if ($clientId === '' || in_array($clientId, ['client-id', 'your-client-id'], true)) {
            $errors['data.youtube_client_id'] = 'Paste your real OAuth Client ID from Google Cloud Console.';
        } elseif (! str_contains($clientId, '.apps.googleusercontent.com')) {
            $errors['data.youtube_client_id'] = 'Client ID should end with .apps.googleusercontent.com';
        }

        if ($clientSecret === '' || $clientSecret === 'client-secret') {
            $errors['data.youtube_client_secret'] = 'Paste your OAuth Client Secret (starts with GOCSPX-).';
        } elseif (str_contains($clientSecret, '.apps.googleusercontent.com')) {
            $errors['data.youtube_client_secret'] = 'This looks like a Client ID — put it in Youtube Client Id instead. Client Secret starts with GOCSPX-.';
        }

        if ($refreshToken !== '' && str_starts_with($refreshToken, 'GOCSPX')) {
            $errors['data.youtube_refresh_token'] = 'This looks like a Client Secret — put it in Youtube Client Secret. Leave refresh token blank and run youtube:authorize.';
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        foreach (self::settingKeys() as $key) {
            $value = $data[$key] ?? null;

            if ($key === 'youtube_refresh_token' && blank($value)) {
                continue;
            }

            Setting::setValue($key, $value);
        }

        Notification::make()
            ->success()
            ->title('Settings updated successfully.')
            ->send();
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Edit Settings')
                ->submit('save'),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title ?? 'Global Settings';
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
            ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make($this->getFormActions())
                    ->alignment($this->getFormActionsAlignment())
                    ->fullWidth($this->hasFullWidthFormActions())
                    ->key('form-actions'),
            ]);
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }
}
