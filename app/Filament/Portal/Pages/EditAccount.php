<?php

namespace App\Filament\Portal\Pages;

use App\Enums\UserType;
use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Client portal: edit profile + change password only.
 */
class EditAccount extends Page
{
    use InteractsWithClientMembership;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static ?string $navigationLabel = 'Edit profile';

    protected static ?string $title = 'Edit Profile';

    protected static ?string $slug = 'account';

    protected static ?int $navigationSort = 1;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    /**
     * @var array<string, mixed>|null
     */
    public ?array $passwordData = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        if (! in_array($user->user_type, [UserType::Portal, UserType::Voc], true)) {
            return false;
        }

        return $user->clientMemberships()->active()->exists();
    }

    public static function getNavigationGroup(): ?string
    {
        return 'My Account';
    }

    public function mount(): void
    {
        $member = $this->currentClientUser();
        $client = $this->currentClient();

        if (! $member) {
            return;
        }

        if ($this->isPrimaryUser()) {
            $this->form->fill([
                'first_name' => $member->first_name,
                'last_name' => $member->last_name,
                'company_name' => $member->company_name,
                'billing_address' => $member->billing_address,
                'email' => $member->email,
                'town' => $member->town,
                'phone' => ($member->phone && (int) $member->phone > 0) ? (string) $member->phone : '',
                'postal_code' => ($member->postal_code && (int) $member->postal_code > 0) ? (string) $member->postal_code : '',
                'shortcut_title' => $client?->shortcut_title ?: '',
                'shortcut_image1' => $this->existingPublicUpload($client?->shortcut_image1, 'touch_icons'),
                'shortcut_image2' => $this->existingPublicUpload($client?->shortcut_image2, 'touch_icons'),
                'footer_logo' => $this->existingPublicUpload($member->footer_logo, 'logos'),
            ]);
        }

        $this->getSchema('passwordForm')?->fill([
            'txtpass' => '',
            'txtnewpass' => '',
            'txtconfirmpass' => '',
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Edit Profile')
                    ->columns(2)
                    ->visible(fn (): bool => $this->isPrimaryUser())
                    ->schema([
                        TextInput::make('first_name')
                            ->label('First name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->label('Last name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('company_name')
                            ->label('Company name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('billing_address')
                            ->label('Billing address')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email (username)')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('town')
                            ->label('Town')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Telephone number')
                            ->required()
                            ->maxLength(50),
                        TextInput::make('postal_code')
                            ->label('Postal code')
                            ->required()
                            ->maxLength(20),
                        TextInput::make('shortcut_title')
                            ->label('Shortcut title')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        FileUpload::make('shortcut_image1')
                            ->label('Shortcut image 1 (180×180 PNG)')
                            ->image()
                            ->acceptedFileTypes(['image/png'])
                            ->directory('touch_icons')
                            ->disk('public')
                            ->visibility('public')
                            ->imagePreviewHeight('50'),
                        FileUpload::make('shortcut_image2')
                            ->label('Shortcut image 2 (192×192 PNG)')
                            ->image()
                            ->acceptedFileTypes(['image/png'])
                            ->directory('touch_icons')
                            ->disk('public')
                            ->visibility('public')
                            ->imagePreviewHeight('50'),
                        FileUpload::make('footer_logo')
                            ->label('Footer logo')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/jpg'])
                            ->directory('logos')
                            ->disk('public')
                            ->visibility('public')
                            ->imagePreviewHeight('50')
                            ->maxSize(1024)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function passwordForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('passwordData')
            ->components([
                Section::make('Change Password')
                    ->schema([
                        TextInput::make('txtpass')
                            ->label('Old password')
                            ->password()
                            ->revealable()
                            ->required(),
                        TextInput::make('txtnewpass')
                            ->label('New password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(5)
                            ->maxLength(12),
                        TextInput::make('txtconfirmpass')
                            ->label('Confirm new password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->same('txtnewpass'),
                    ]),
            ]);
    }

    public function save(): void
    {
        if (! $this->isPrimaryUser()) {
            Notification::make()
                ->title('Only the primary account holder can edit this profile.')
                ->danger()
                ->send();

            return;
        }

        $member = $this->requireClientUser();
        $client = $this->requireClient();
        $data = $this->form->getState();

        $member->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'company_name' => $data['company_name'],
            'billing_address' => $data['billing_address'],
            'email' => $data['email'],
            'town' => $data['town'],
            'phone' => $data['phone'] ?: 0,
            'postal_code' => $data['postal_code'] ?: 0,
            'footer_logo' => $this->storeBasename($data['footer_logo'] ?? null) ?? ($member->footer_logo ?: ''),
        ]);

        $client->update([
            'shortcut_title' => $data['shortcut_title'] ?? '',
            'shortcut_image1' => $this->storeBasename($data['shortcut_image1'] ?? null) ?? ($client->shortcut_image1 ?: ''),
            'shortcut_image2' => $this->storeBasename($data['shortcut_image2'] ?? null) ?? ($client->shortcut_image2 ?: ''),
        ]);

        Notification::make()
            ->title('Profile updated successfully.')
            ->success()
            ->send();
    }

    public function changePassword(): void
    {
        $data = $this->getSchema('passwordForm')->getState();
        $member = $this->requireClientUser();
        $auth = $member->authUser ?? User::query()->find($member->auth_user_id);

        if (! $auth || ! Hash::check((string) $data['txtpass'], $auth->password)) {
            throw ValidationException::withMessages([
                'passwordData.txtpass' => 'Old password does not match with current password.',
            ]);
        }

        $member->password = $data['txtnewpass'];
        $member->is_password_change = true;
        $member->save();

        $this->getSchema('passwordForm')?->fill([
            'txtpass' => '',
            'txtnewpass' => '',
            'txtconfirmpass' => '',
        ]);

        Notification::make()
            ->title('Password changed successfully.')
            ->success()
            ->send();
    }

    public function getTitle(): string|Htmlable
    {
        return 'Edit Profile';
    }

    public function content(Schema $schema): Schema
    {
        $components = [];

        if ($this->isPrimaryUser()) {
            $components[] = $this->getProfileFormContentComponent();
        }

        $components[] = $this->getPasswordFormContentComponent();

        return $schema->components($components);
    }

    public function getProfileFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('profile-form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make([
                    Action::make('save')
                        ->label('Save profile')
                        ->submit('save'),
                ]),
            ]);
    }

    public function getPasswordFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('passwordForm')])
            ->id('password-form')
            ->livewireSubmitHandler('changePassword')
            ->footer([
                Actions::make([
                    Action::make('changePassword')
                        ->label('Change password')
                        ->submit('changePassword'),
                ]),
            ]);
    }

    protected function existingPublicUpload(?string $filename, string $directory): ?string
    {
        if (! filled($filename)) {
            return null;
        }

        $relative = $directory.'/'.ltrim(basename($filename), '/');

        return Storage::disk('public')->exists($relative) ? $relative : null;
    }

    protected function storeBasename(mixed $path): ?string
    {
        if (! filled($path) || ! is_string($path)) {
            return null;
        }

        return basename($path);
    }
}
