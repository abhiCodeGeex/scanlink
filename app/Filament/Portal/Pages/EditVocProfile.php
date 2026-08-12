<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Resources\Profiles\Schemas\ProfileFormSchema;
use App\Models\Profile;
use App\Models\User;
use App\Models\VocDocument;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;

/**
 * Legacy voc/vocedit.php: the self-service editor a VOC "Additional User Access Login"
 * card-holder gets after logging in. Unlike the full profile editor, it exposes ONLY
 * Profile Information + Document Upload and saves only those (legacy updateVocByUser).
 */
class EditVocProfile extends Page
{
    use InteractsWithClientMembership;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'edit-voc-profile';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static ?string $title = 'Edit VOC Profile';

    protected string $view = 'filament.portal.pages.edit-voc-profile';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public Profile $profile;

    /** Scalar voc "Profile Information" columns this restricted editor may write. */
    protected const PERSONAL_COLUMNS = [
        'voc_first_name', 'voc_last_name', 'voc_address', 'voc_town', 'voc_state', 'voc_phone',
        'voc_dob', 'voc_known_allergies', 'voc_blood_type', 'voc_next_of_kin', 'voc_contact_phone',
        'voc_employer', 'voc_emp_address', 'voc_emp_town', 'voc_emp_state', 'voc_emp_phone',
    ];

    public function mount(): void
    {
        $id = (int) request()->integer('profile');

        $this->profile = Profile::query()->active()->findOrFail($id);

        abort_unless($this->profile->typeSlug() === 'voc', 404);
        abort_unless($this->canEditThisProfile(), 403);

        $this->profile->loadMissing('vocDocuments');

        $this->form->fill($this->profileFormState());
    }

    public function getTitle(): string
    {
        return 'Edit VOC Profile #'.$this->profile->id;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile Information')
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema(ProfileFormSchema::typeFields((int) $this->profile->type_id))
                    ->columns(2),

                Section::make('Document Upload')
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Repeater::make('documents')
                            ->hiddenLabel()
                            ->schema([
                                Hidden::make('voc_document_id'),
                                Hidden::make('name'),
                                Hidden::make('expiry_date'),
                                Hidden::make('file_name'),
                            ])
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->cloneable(false)
                            ->itemLabel(function (array $state): string {
                                $name = trim((string) ($state['name'] ?? '')) ?: 'Document';
                                $exp = $state['expiry_date'] ?? null;

                                if ($exp !== null && ! in_array((string) $exp, ['', '1970-01-01', '0000-00-00'], true)) {
                                    try {
                                        $name .= ' (Expires '.Carbon::parse($exp)->format('d/m/Y').')';
                                    } catch (\Throwable $e) {
                                        // ignore unparseable dates
                                    }
                                }

                                return $name;
                            })
                            ->extraAttributes(['class' => 'sl-contacts-repeater sl-documents-repeater sl-green-add-repeater'])
                            ->addAction(fn (Action $action): Action => $action
                                ->label(fn (Repeater $component): string => count($component->getRawState() ?? []) > 0 ? 'Add Another' : 'Add')
                                ->icon('heroicon-m-plus')
                                ->color('primary')
                                ->button()
                                ->modalHeading(fn (Repeater $component): string => count($component->getRawState() ?? []) > 0 ? 'Add another document' : 'Upload a Document')
                                ->modalSubmitActionLabel('Save document')
                                ->modalCancelActionLabel('Cancel')
                                ->modalWidth('lg')
                                ->form($this->documentModalForm())
                                ->action(fn (array $data, Repeater $component) => $this->appendRepeaterItem($component, $data)))
                            ->extraItemActions([
                                Action::make('editVocDocument')
                                    ->label('Edit')
                                    ->tooltip('Edit document')
                                    ->icon('heroicon-m-pencil-square')
                                    ->color('primary')
                                    ->iconButton()
                                    ->modalHeading('Edit document')
                                    ->modalSubmitActionLabel('Save changes')
                                    ->modalCancelActionLabel('Cancel')
                                    ->modalWidth('lg')
                                    ->fillForm(function (array $arguments, Repeater $component): array {
                                        $item = $component->getRawState()[$arguments['item'] ?? ''] ?? [];
                                        $exp = $item['expiry_date'] ?? null;

                                        if ($exp !== null && in_array((string) $exp, ['1970-01-01', '0000-00-00'], true)) {
                                            $exp = null;
                                        }

                                        return [
                                            'name' => (string) ($item['name'] ?? ''),
                                            'expiry_date' => $exp,
                                            'file_name' => $item['file_name'] ?? null,
                                        ];
                                    })
                                    ->form($this->documentModalForm())
                                    ->action(fn (array $data, array $arguments, Repeater $component) => $this->updateRepeaterItem($component, $arguments, $data)),
                            ])
                            ->deleteAction(fn (Action $action): Action => $action
                                ->label('Delete')
                                ->tooltip('Delete document')
                                ->icon('heroicon-m-trash')
                                ->color('danger')
                                ->iconButton()
                                ->requiresConfirmation()
                                ->modalHeading('Delete document?')
                                ->modalSubmitActionLabel('Delete'))
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data')
            ->model($this->profile);
    }

    /**
     * The modal form used by the Document Upload add/edit popups.
     *
     * @return array<int, mixed>
     */
    protected function documentModalForm(): array
    {
        return [
            TextInput::make('name')
                ->label('Document Name')
                ->maxLength(200)
                ->autocomplete(false),
            DatePicker::make('expiry_date')
                ->label('Expiry Date')
                ->native(false)
                ->displayFormat('d-m-Y'),
            FileUpload::make('file_name')
                ->label('Upload a Document: (File type DOC, DOCX, PDF, GIF, JPEG, PNG) (Max file size 10 MB)')
                ->directory('profiles/voc-documents')
                ->disk('public')
                ->maxSize(10240)
                ->required()
                ->downloadable()
                ->openable()
                ->columnSpanFull(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function appendRepeaterItem(Repeater $component, array $data): void
    {
        $newUuid = $component->generateUuid();
        $items = $component->getRawState() ?? [];

        if ($newUuid) {
            $items[$newUuid] = $data;
        } else {
            $items[] = $data;
            $newUuid = (string) array_key_last($items);
        }

        $component->rawState($items);
        $component->getChildSchema($newUuid)->fill($data);
        $component->callAfterStateUpdated();
        $component->partiallyRender();
    }

    /**
     * @param  array<string, mixed>  $arguments
     * @param  array<string, mixed>  $data
     */
    public function updateRepeaterItem(Repeater $component, array $arguments, array $data): void
    {
        $itemKey = $arguments['item'] ?? null;

        if ($itemKey === null) {
            return;
        }

        $items = $component->getRawState() ?? [];
        $items[$itemKey] = array_merge($items[$itemKey] ?? [], $data);
        $component->rawState($items);
        $component->getChildSchema($itemKey)->fill($data);
        $component->callAfterStateUpdated();
        $component->partiallyRender();
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // 1) Personal fields only (legacy updateVocByUser — no title bar/security/recipients).
        $update = [];
        foreach (self::PERSONAL_COLUMNS as $column) {
            if (array_key_exists($column, $data)) {
                $update[$column] = $data[$column];
            }
        }
        $this->profile->fill($update)->save();

        // 2) Reconcile documents (create / update / delete).
        $this->syncDocuments($data['documents'] ?? []);

        $this->profile->refresh()->loadMissing('vocDocuments');
        $this->form->fill($this->profileFormState());

        Notification::make()
            ->success()
            ->title('Profile updated')
            ->send();
    }

    /**
     * @return array<string, mixed>
     */
    protected function profileFormState(): array
    {
        $state = [];

        foreach (self::PERSONAL_COLUMNS as $column) {
            $state[$column] = $this->profile->getAttribute($column);
        }

        $state['voc_dob'] = $this->cleanDate($this->profile->getRawOriginal('voc_dob'));

        $state['documents'] = $this->profile->vocDocuments
            ->map(fn (VocDocument $doc): array => [
                'voc_document_id' => $doc->voc_document_id,
                'name' => $doc->name,
                'expiry_date' => $this->cleanDate($doc->getRawOriginal('expiry_date')),
                'file_name' => $doc->file_name,
            ])
            ->values()
            ->all();

        return $state;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    protected function syncDocuments(array $rows): void
    {
        $keepIds = [];

        foreach ($rows as $row) {
            $id = (int) ($row['voc_document_id'] ?? 0);

            $payload = [
                'profile_id' => $this->profile->id,
                'name' => (string) ($row['name'] ?? ''),
                'expiry_date' => $row['expiry_date'] ?: null,
                'file_name' => (string) ($row['file_name'] ?? ''),
            ];

            if ($id > 0 && ($doc = VocDocument::find($id))) {
                $doc->update($payload);
                $keepIds[] = $id;

                continue;
            }

            $payload['voc_document_id'] = ((int) VocDocument::max('voc_document_id')) + 1;
            $doc = VocDocument::create($payload);
            $keepIds[] = (int) $doc->voc_document_id;
        }

        VocDocument::query()
            ->where('profile_id', $this->profile->id)
            ->when($keepIds !== [], fn ($query) => $query->whereNotIn('voc_document_id', $keepIds))
            ->delete();
    }

    protected function cleanDate(mixed $raw): ?string
    {
        $value = trim((string) ($raw ?? ''));

        return in_array($value, ['', '0000-00-00', '1970-01-01'], true) ? null : $value;
    }

    protected function canEditThisProfile(): bool
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        // A VOC secondary user linked to this profile...
        if ($this->profile->vocUsers()->where('auth_user_id', $user->id)->exists()) {
            return true;
        }

        // ...or a portal member of the owning client.
        return $user->clientMemberships()
            ->active()
            ->where('client_id', $this->profile->client_id)
            ->exists();
    }
}
