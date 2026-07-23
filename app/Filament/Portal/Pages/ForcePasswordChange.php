<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Models\ClientUser;
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
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange extends Page
{
    use InteractsWithClientMembership;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?string $navigationLabel = 'Change Password';

    protected static ?string $title = 'Change Your Password';

    protected static ?string $slug = 'force-password-change';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.portal.pages.force-password-change';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('You must set a new password before continuing.')
                    ->schema([
                        TextInput::make('password')
                            ->label('New password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(5)
                            ->maxLength(255),
                        TextInput::make('password_confirmation')
                            ->label('Confirm password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->same('password'),
                    ]),
            ]);
    }

    public function updatePassword(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();

        if (! $user) {
            abort(403);
        }

        $user->update(['password' => $data['password']]);

        // Legacy flag: 0 = must change, 1 = already changed. Mark every membership
        // linked by auth user or email so middleware stops redirecting after save.
        $updated = ClientUser::query()
            ->where(function ($query) use ($user): void {
                $query->where('auth_user_id', $user->id)
                    ->orWhere('email', $user->email);
            })
            ->update(['is_password_change' => true]);

        if ($updated === 0) {
            Notification::make()
                ->title('Could not update password status for this account.')
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title('Password updated')
            ->success()
            ->send();

        $this->redirect(\App\Filament\Portal\Resources\Profiles\ProfileResource::getUrl('index', panel: 'portal'));
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('updatePassword')
                ->label('Save password')
                ->submit('updatePassword'),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title ?? 'Change Your Password';
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
            ->livewireSubmitHandler('updatePassword')
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
