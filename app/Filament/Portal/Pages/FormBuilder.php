<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Models\FormBuilderQuestion;
use App\Models\FormBuilderQuestionType;
use App\Models\Profile;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
use Illuminate\Support\Collection;

class FormBuilder extends Page
{
    use InteractsWithClientMembership;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Form Builder';

    protected static ?string $title = 'Form Builder';

    protected static ?string $slug = 'form-builder';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.portal.pages.form-builder';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public ?int $selectedProfileId = null;

    /** @var Collection<int, FormBuilderQuestion> */
    public Collection $questions;

    public static function getNavigationGroup(): ?string
    {
        return 'Forms';
    }

    public function mount(): void
    {
        $this->questions = collect();
        $firstProfileId = $this->clientProfileOptions()->keys()->first();

        $this->form->fill([
            'profile_id' => $firstProfileId,
            'question_text' => null,
            'question_type_id' => $this->questionTypeOptions()->keys()->first(),
            'is_mandatory' => false,
        ]);

        if ($firstProfileId) {
            $this->loadQuestions((int) $firstProfileId);
        }
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile form')
                    ->schema([
                        Select::make('profile_id')
                            ->label('Profile')
                            ->options(fn (): array => $this->clientProfileOptions()->all())
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (?string $state): void {
                                $this->loadQuestions((int) $state);
                            }),
                        Select::make('question_type_id')
                            ->label('Question type')
                            ->options(fn (): array => $this->questionTypeOptions()->all())
                            ->required(),
                        Textarea::make('question_text')
                            ->label('Question text')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        Toggle::make('is_mandatory')
                            ->label('Mandatory'),
                    ])
                    ->columns(2),
            ]);
    }

    public function addQuestion(): void
    {
        $data = $this->form->getState();
        $client = $this->requireClient();
        $profile = Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->findOrFail($data['profile_id']);

        $nextId = (int) FormBuilderQuestion::query()->max('question_id') + 1;
        $nextOrder = (int) FormBuilderQuestion::query()
            ->where('profile_id', $profile->id)
            ->max('question_order') + 1;

        FormBuilderQuestion::query()->create([
            'question_id' => $nextId ?: 1,
            'profile_id' => $profile->id,
            'form_id' => $profile->form_id ?: 0,
            'question_type_id' => $data['question_type_id'],
            'question_text' => $data['question_text'],
            'question_order' => $nextOrder ?: 1,
            'is_mandatory' => (bool) ($data['is_mandatory'] ?? false),
        ]);

        $profile->update([
            'form_active' => true,
            'form_is_enable' => true,
        ]);

        Notification::make()
            ->title('Question added')
            ->success()
            ->send();

        $this->loadQuestions($profile->id);
        $this->form->fill([
            'profile_id' => $profile->id,
            'question_text' => null,
            'question_type_id' => $data['question_type_id'],
            'is_mandatory' => false,
        ]);
    }

    public function deleteQuestion(int $questionId): void
    {
        $client = $this->requireClient();

        $question = FormBuilderQuestion::query()
            ->whereHas('profile', fn ($q) => $q->where('client_id', $client->id))
            ->where('question_id', $questionId)
            ->firstOrFail();

        $profileId = $question->profile_id;
        $question->delete();

        Notification::make()
            ->title('Question removed')
            ->success()
            ->send();

        $this->loadQuestions($profileId);
    }

    protected function loadQuestions(int $profileId): void
    {
        $this->selectedProfileId = $profileId;
        $this->questions = FormBuilderQuestion::query()
            ->with('questionType')
            ->where('profile_id', $profileId)
            ->orderBy('question_order')
            ->get();
    }

    /**
     * @return Collection<int|string, string>
     */
    protected function clientProfileOptions(): Collection
    {
        $client = $this->currentClient();

        if (! $client) {
            return collect();
        }

        return Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    /**
     * @return Collection<int|string, string>
     */
    protected function questionTypeOptions(): Collection
    {
        $types = FormBuilderQuestionType::query()
            ->where('is_active', true)
            ->orderBy('type')
            ->get();

        if ($types->isEmpty()) {
            return collect([
                1 => 'Text',
                2 => 'Multiple choice',
                3 => 'Checkbox',
            ]);
        }

        return $types->mapWithKeys(fn (FormBuilderQuestionType $type): array => [
            $type->question_type_id => $type->label ?: ucfirst($type->type),
        ]);
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('addQuestion')
                ->label('Add question')
                ->submit('addQuestion'),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title ?? 'Form Builder';
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
            ->livewireSubmitHandler('addQuestion')
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
