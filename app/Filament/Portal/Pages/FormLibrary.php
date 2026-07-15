<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Models\ClientUser;
use App\Models\FormBuilderLibrary;
use App\Models\Profile;
use App\Services\FormLibraryService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FormLibrary extends Page implements HasTable
{
    use InteractsWithClientMembership;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = 'Form Library';

    protected static ?string $title = 'Form Library';

    protected static ?string $slug = 'form-library';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.portal.pages.form-library';

    public static function getNavigationGroup(): ?string
    {
        return 'Forms';
    }

    public static function canAccess(): bool
    {
        return static::memberCanAccessFormBuilder(static::portalMembership());
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                $clientId = $this->requireClient()->id;
                $userIds = ClientUser::query()
                    ->where('client_id', $clientId)
                    ->pluck('id');

                return FormBuilderLibrary::query()
                    ->whereIn('user_id', $userIds)
                    ->where('is_deleted_from_library', false)
                    ->orderBy('form_title');
            })
            ->columns([
                TextColumn::make('form_title')
                    ->label('Form title')
                    ->placeholder('Untitled form')
                    ->searchable(),
                TextColumn::make('form_id')
                    ->label('Form ID')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('applyToProfile')
                    ->label('Apply to profile')
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->form([
                        Select::make('profile_id')
                            ->label('Profile')
                            ->options(function (): array {
                                return Profile::selectOptionsForClient((int) $this->requireClient()->id)->all();
                            })
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (FormBuilderLibrary $record, array $data, FormLibraryService $library): void {
                        $clientId = $this->requireClient()->id;

                        $profile = Profile::query()
                            ->where('client_id', $clientId)
                            ->active()
                            ->findOrFail($data['profile_id']);

                        $cloned = $library->applyLibraryFormToProfile((int) $record->form_id, $profile);

                        Notification::make()
                            ->title('Form applied')
                            ->body("{$cloned} question(s) copied to {$profile->name}.")
                            ->success()
                            ->send();
                    }),
                Action::make('deleteFromLibrary')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (FormBuilderLibrary $record, FormLibraryService $library): void {
                        $library->deleteFromLibrary($record);

                        Notification::make()
                            ->title('Form removed from library')
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateHeading('No saved forms')
            ->emptyStateDescription('Save a form from the form builder to add it to your library.');
    }
}
