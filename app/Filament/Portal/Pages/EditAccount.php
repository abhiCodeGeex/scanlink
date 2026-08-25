<?php

namespace App\Filament\Portal\Pages;

use App\Enums\ClientUserRole;
use App\Enums\UserType;
use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Models\ClientUser;
use App\Models\Profile;
use App\Models\User;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

/**
 * Legacy profile/index — Edit Profile, Change Password, Add sub user, Manage User list.
 */
class EditAccount extends Page implements HasTable
{
    use InteractsWithClientMembership;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static ?string $navigationLabel = 'Edit user profile';

    protected static ?string $title = 'Edit Profile';

    protected static ?string $slug = 'account';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.portal.pages.edit-account';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    /**
     * @var array<string, mixed>|null
     */
    public ?array $passwordData = [];

    /**
     * @var array<string, mixed>|null
     */
    public ?array $subUserData = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user instanceof User || $user->user_type !== UserType::Portal) {
            return false;
        }

        return $user->clientMemberships()
            ->active()
            ->exists();
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

            $this->getSchema('subUserForm')?->fill([
                'txtemail' => '',
                'txtpass1' => '',
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
                    ->schema([
                        TextInput::make('first_name')
                            ->label('First name:')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->label('Last name:')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('company_name')
                            ->label('Company name:')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('billing_address')
                            ->label('Billing address:')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email(this will also be your username):')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('town')
                            ->label('Town:')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Telephone number:')
                            ->required()
                            ->numeric()
                            ->maxLength(50),
                        TextInput::make('postal_code')
                            ->label('Postal code:')
                            ->required()
                            ->numeric()
                            ->maxLength(20),
                        TextInput::make('shortcut_title')
                            ->label('Shortcut Title:')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        FileUpload::make('shortcut_image1')
                            ->label('Shortcut Image 1 (Please use a 180x180px dimension PNG image)')
                            ->image()
                            ->acceptedFileTypes(['image/png'])
                            ->directory('touch_icons')
                            ->disk('public')
                            ->visibility('public')
                            ->imagePreviewHeight('50'),
                        FileUpload::make('shortcut_image2')
                            ->label('Shortcut Image 2 (Please use a 192x192px dimension PNG image)')
                            ->image()
                            ->acceptedFileTypes(['image/png'])
                            ->directory('touch_icons')
                            ->disk('public')
                            ->visibility('public')
                            ->imagePreviewHeight('50'),
                        FileUpload::make('footer_logo')
                            ->label('Footer Logo')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/jpg'])
                            ->directory('logos')
                            ->disk('public')
                            ->visibility('public')
                            ->imagePreviewHeight('50')
                            ->helperText('Upload Company Logo: (File type JPG, JPEG, PNG, GIF) (Max file size 1MB)')
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
                    ->columns(1)
                    ->schema([
                        TextInput::make('txtpass')
                            ->label('Old password:')
                            ->password()
                            ->revealable()
                            ->required(),
                        TextInput::make('txtnewpass')
                            ->label('New password:')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(5)
                            ->maxLength(12),
                        TextInput::make('txtconfirmpass')
                            ->label('Confirm new password:')
                            ->password()
                            ->revealable()
                            ->required()
                            ->same('txtnewpass'),
                    ]),
            ]);
    }

    public function subUserForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('subUserData')
            ->components([
                Section::make('Add new sub user')
                    ->columns(1)
                    ->schema([
                        TextInput::make('txtemail')
                            ->label('Email:')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('txtpass1')
                            ->label('Password:')
                            ->password()
                            ->revealable()
                            ->required()
                            ->maxLength(255),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ClientUser::query()
                ->where('client_id', $this->requireClient()->id)
                ->whereKeyNot($this->requireClientUser()->id)
                ->orderBy('email'))
            ->heading('Manage User')
            ->columns([
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable(),
            ])
            ->actionsColumnLabel('Tools')
            ->recordActions([
                EditAction::make()
                    ->label('')
                    ->tooltip('Edit user')
                    ->icon(new HtmlString(
                        '<img src="'.e(asset('images/edit.png')).'" alt="Edit" width="20" height="20" class="sl-manage-user-edit-icon" />'
                    ))
                    ->modalHeading('Edit User')
                    ->modalWidth(Width::SevenExtraLarge)
                    ->modalSubmitActionLabel('SAVE')
                    ->extraModalWindowAttributes(['class' => 'sl-edit-user-modal'])
                    ->mutateRecordDataUsing(fn (array $data, ClientUser $record): array => $this->fillEditUserForm($record, $data))
                    ->using(fn (ClientUser $record, array $data): ClientUser => $this->saveEditedUser($record, $data))
                    ->successNotificationTitle('User details updated successfully.')
                    ->schema($this->editUserFormSchema()),
                Action::make('toggleBlock')
                    ->label('')
                    ->tooltip(fn (ClientUser $record): string => $record->status
                        ? 'Click to Block'
                        : 'Click to Reactivate')
                    ->icon(fn (ClientUser $record): string => $record->status
                        ? 'heroicon-o-check-circle'
                        : 'heroicon-o-no-symbol')
                    ->color(fn (ClientUser $record): string => $record->status ? 'success' : 'warning')
                    ->requiresConfirmation()
                    ->modalHeading(fn (ClientUser $record): string => $record->status
                        ? 'Block this user?'
                        : 'Reactivate this user?')
                    ->action(function (ClientUser $record): void {
                        $record->update(['status' => ! $record->status]);

                        Notification::make()
                            ->title('User status updated successfully')
                            ->success()
                            ->send();
                    }),
                DeleteAction::make()
                    ->label('')
                    ->tooltip('Delete user')
                    ->icon('heroicon-o-x-circle')
                    ->modalHeading('Delete user')
                    ->modalDescription('Are you sure you want to delete this user?')
                    ->successNotificationTitle('User deleted successfully'),
            ])
            ->paginated(false)
            ->emptyStateHeading('No users found')
            ->emptyStateDescription(null)
            ->emptyStateIcon(null)
            ->searchable(false);
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

    public function addSubUser(): void
    {
        if (! $this->isPrimaryUser()) {
            return;
        }

        $data = $this->getSchema('subUserForm')->getState();
        $email = strtolower(trim((string) $data['txtemail']));

        if (ClientUser::query()->where('email', $email)->exists()
            || User::query()->where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'subUserData.txtemail' => 'This email already exists.',
            ]);
        }

        $member = new ClientUser;
        $member->forceFill([
            'client_id' => $this->requireClient()->id,
            'email' => $email,
            'password' => $data['txtpass1'],
            'role' => ClientUserRole::SubUser,
            'is_sub_user' => true,
            'status' => true,
            'is_password_change' => true,
            'video_upload' => true,
            'expire_at' => now()->addYear(),
            'first_name' => '',
            'last_name' => '',
            'company_name' => '',
            'billing_address' => '',
            'town' => '',
            'phone' => 0,
            'postal_code' => 0,
            'footer_logo' => '',
            'access_addcode' => false,
            'access_edit' => false,
            'access_delete' => false,
            'access_analytics' => false,
            'access_form_submission' => false,
            'access_download' => false,
            'access_label' => false,
            'access_log' => false,
        ])->save();

        $this->getSchema('subUserForm')?->fill([
            'txtemail' => '',
            'txtpass1' => '',
        ]);

        Notification::make()
            ->title('Sub user added successfully.')
            ->success()
            ->send();
    }

    public function getTitle(): string|Htmlable
    {
        return 'Edit Profile';
    }

    public function content(Schema $schema): Schema
    {
        // Layout is rendered by edit-account.blade.php (legacy profile/index structure).
        return $schema->components([]);
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
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

    /**
     * @return array<int, mixed>
     */
    protected function editUserFormSchema(): array
    {
        return [
            TextInput::make('email')
                ->label('Email:')
                ->email()
                ->required()
                ->maxLength(255),
            TextInput::make('password')
                ->label('Password:')
                ->password()
                ->revealable()
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->helperText('Leave blank to keep the current password.'),
            Checkbox::make('enable_admin_access')
                ->label('Enable Admin Access:')
                ->live(),
            Grid::make(8)
                ->extraAttributes(['class' => 'sl-edit-user-perms'])
                ->schema([
                    $this->permissionCheckbox('access_addcode', 'register_code.jpg', 'Register Codes'),
                    $this->permissionCheckbox('access_edit', 'edit_profile.png', 'Edit Profile'),
                    $this->permissionCheckbox('access_analytics', 'analytics.png', 'Scanalytics'),
                    $this->permissionCheckbox('access_form_submission', 'form_submission.png', 'Form Submission'),
                    $this->permissionCheckbox('access_download', 'download.png', 'Download QR/DM code'),
                    $this->permissionCheckbox('access_label', 'order-label.png', 'Order Labels'),
                    $this->permissionCheckbox('access_log', 'visitor_log.png', 'View Visitor Log'),
                    // Legacy edit_user.php shows all 8 permissions unconditionally — Delete
                    // Profile included (it was wrongly gated behind Enable Admin Access here).
                    $this->permissionCheckbox('access_delete', 'delete_profile.png', 'Delete Profile'),
                ]),
            ViewField::make('code_profile_ids')
                ->label('')
                ->view('filament.portal.forms.edit-user-code-profiles')
                ->viewData(fn (): array => [
                    'profiles' => $this->editUserCodeProfiles(),
                ])
                ->default([])
                ->disabled(fn (Get $get): bool => (bool) $get('enable_admin_access')),
        ];
    }

    protected function permissionCheckbox(string $name, string $image, string $title): Checkbox
    {
        $src = e(asset('images/'.$image));
        $titleEsc = e($title);

        return Checkbox::make($name)
            ->label(new HtmlString(
                '<span class="sl-edit-user-perm" title="'.$titleEsc.'">'
                .'<img src="'.$src.'" alt="'.$titleEsc.'" title="'.$titleEsc.'" />'
                .'</span>'
            ))
            ->inline(false)
            ->disabled(fn (Get $get): bool => (bool) $get('enable_admin_access'));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function fillEditUserForm(ClientUser $record, array $data): array
    {
        $selected = array_values(array_filter(array_map(
            static fn (string $id): int => (int) $id,
            explode(',', (string) $record->show_code_profile_id_to_acc_user)
        )));

        return [
            ...$data,
            'email' => $record->email,
            'password' => '',
            'enable_admin_access' => (bool) $record->enable_admin_access,
            'access_addcode' => (bool) $record->access_addcode,
            'access_edit' => (bool) $record->access_edit,
            'access_analytics' => (bool) $record->access_analytics,
            'access_form_submission' => (bool) $record->access_form_submission,
            'access_download' => (bool) $record->access_download,
            'access_label' => (bool) $record->access_label,
            'access_log' => (bool) $record->access_log,
            'access_delete' => (bool) $record->access_delete,
            'code_profile_ids' => $selected,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function saveEditedUser(ClientUser $record, array $data): ClientUser
    {
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $adminAccess = (bool) ($data['enable_admin_access'] ?? false);

        $payload = [
            'email' => $email,
            'enable_admin_access' => $adminAccess,
        ];

        if (filled($data['password'] ?? null)) {
            $payload['password'] = $data['password'];
        }

        if ($adminAccess) {
            $allProfileIds = Profile::query()
                ->where('client_id', $record->client_id)
                ->orderBy('id')
                ->pluck('id')
                ->all();

            $payload = [
                ...$payload,
                'access_addcode' => true,
                'access_edit' => true,
                'access_analytics' => true,
                'access_form_submission' => true,
                'access_download' => true,
                'access_label' => true,
                'access_log' => true,
                'access_delete' => true,
                'show_code_profile_id_to_acc_user' => implode(',', $allProfileIds),
                'is_sub_user' => false,
            ];
        } else {
            $selectedIds = collect($data['code_profile_ids'] ?? [])
                ->map(fn ($id): int => (int) $id)
                ->filter()
                ->unique()
                ->values()
                ->all();

            $payload = [
                ...$payload,
                'access_addcode' => (bool) ($data['access_addcode'] ?? false),
                'access_edit' => (bool) ($data['access_edit'] ?? false),
                'access_analytics' => (bool) ($data['access_analytics'] ?? false),
                'access_form_submission' => (bool) ($data['access_form_submission'] ?? false),
                'access_download' => (bool) ($data['access_download'] ?? false),
                'access_label' => (bool) ($data['access_label'] ?? false),
                'access_log' => (bool) ($data['access_log'] ?? false),
                'access_delete' => false,
                'show_code_profile_id_to_acc_user' => implode(',', $selectedIds),
                'is_sub_user' => true,
            ];
        }

        $record->fill($payload)->save();

        return $record;
    }

    /**
     * Profiles listed in the legacy Edit User colorbox.
     *
     * @return array<int, array{id: int, name: string, expiry: string, expiry_class: string, inactive: bool}>
     */
    protected function editUserCodeProfiles(): array
    {
        $clientId = $this->requireClient()->id;
        $now = now();

        return Profile::query()
            ->where('client_id', $clientId)
            ->claimedSlot()
            ->where('type_id', '>', 0)
            ->orderByRaw("COALESCE(NULLIF(code_profile_name, ''), name)")
            ->get([
                'id',
                'name',
                'code_profile_name',
                'expired_at',
                'activation_start_date',
                'activation_end_date',
            ])
            ->map(function (Profile $profile) use ($now): array {
                $expiredAt = $profile->expired_at;
                $expiryClass = 'red';

                if ($expiredAt && $expiredAt->gt($now)) {
                    $expiryClass = $expiredAt->gt($now->copy()->addDays(30)) ? 'green' : 'orange';
                }

                $inactive = false;
                $start = $profile->activation_start_date;
                $end = $profile->activation_end_date;

                if ($this->isRealLegacyDate($start) && $this->isRealLegacyDate($end)) {
                    if (Carbon::parse($start)->startOfDay()->gt($now->copy()->startOfDay())) {
                        $inactive = true;
                    } elseif (Carbon::parse($end)->startOfDay()->lt($now->copy()->startOfDay())) {
                        $inactive = true;
                    }
                }

                $name = trim((string) ($profile->code_profile_name ?: $profile->name));

                return [
                    'id' => (int) $profile->id,
                    'name' => $name !== '' ? $name : ('Profile #'.$profile->id),
                    'expiry' => $expiredAt ? $expiredAt->format('d/m/Y') : '',
                    'expiry_class' => $expiryClass,
                    'inactive' => $inactive,
                ];
            })
            ->all();
    }

    protected function isRealLegacyDate(mixed $value): bool
    {
        if (! filled($value) || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            return false;
        }

        try {
            Carbon::parse($value);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}

