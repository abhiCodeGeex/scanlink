<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Legacy password form (still available at /admin/change-password).
 * Settings sidebar "Profile" is registered in AdminPanelProvider → /admin/profile.
 */
class ChangePassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Change Password';

    protected static ?string $title = 'Change Password';

    protected static ?int $navigationSort = 4;

    /** Replaced in Settings nav by Profile → /admin/profile (2FA + password). */
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.change-password';

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('txtoldpass')
                            ->label('Old Password')
                            ->password()
                            ->revealable()
                            ->required(),
                        TextInput::make('txtnewpass')
                            ->label('New Password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(5)
                            ->maxLength(12),
                        TextInput::make('txtconfirmpass')
                            ->label('Confirm Password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->same('txtnewpass'),
                    ]),
            ])
            ->statePath('data');
    }

    public function changePassword(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();

        if (! Hash::check($data['txtoldpass'], $user->password)) {
            throw ValidationException::withMessages([
                'data.txtoldpass' => 'Old password does not match.',
            ]);
        }

        $user->update(['password' => $data['txtnewpass']]);

        $this->form->fill();

        Notification::make()
            ->title('Password changed successfully.')
            ->success()
            ->send();
    }
}
