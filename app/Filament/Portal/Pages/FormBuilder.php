<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Models\FormBuilderQuestion;
use App\Models\FormBuilderQuestionOption;
use App\Models\FormBuilderQuestionType;
use App\Models\Profile;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
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

    public bool $formActive = false;

    /** @var Collection<int, FormBuilderQuestion> */
    public Collection $questions;

    public static function getNavigationGroup(): ?string
    {
        return 'Forms';
    }

    public static function canAccess(): bool
    {
        return static::memberCanAccessFormBuilder(static::portalMembership());
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
            'options' => [],
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
                        Toggle::make('form_active')
                            ->label('Form active on scan page')
                            ->live()
                            ->afterStateUpdated(function (?bool $state): void {
                                $this->toggleFormActive((bool) $state);
                            }),
                        Select::make('question_type_id')
                            ->label('Question type')
                            ->options(fn (): array => $this->questionTypeOptions()->all())
                            ->required()
                            ->live(),
                        Textarea::make('question_text')
                            ->label('Question text')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        Repeater::make('options')
                            ->label('Answer options')
                            ->schema([
                                TextInput::make('option_name')
                                    ->label('Option')
                                    ->required(),
                            ])
                            ->visible(fn (callable $get): bool => in_array((int) $get('question_type_id'), [3, 4, 5], true))
                            ->defaultItems(0)
                            ->addActionLabel('Add option')
                            ->columnSpanFull(),
                        Toggle::make('is_mandatory')
                            ->label('Mandatory'),
                    ])
                    ->columns(2),
            ]);
    }

    public function toggleFormActive(bool $active): void
    {
        $profileId = (int) ($this->data['profile_id'] ?? $this->selectedProfileId ?? 0);

        if (! $profileId) {
            return;
        }

        $client = $this->requireClient();

        $profile = Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->findOrFail($profileId);

        $profile->update([
            'form_active' => $active,
            'form_is_enable' => $active,
        ]);

        $this->formActive = $active;

        Notification::make()
            ->title($active ? 'Form activated' : 'Form deactivated')
            ->success()
            ->send();
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

        if (in_array((int) $data['question_type_id'], [3, 4, 5], true)) {
            $this->syncQuestionOptions($nextId ?: 1, (int) $data['question_type_id'], $data['options'] ?? []);
        }

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
            'form_active' => true,
            'question_text' => null,
            'question_type_id' => $data['question_type_id'],
            'is_mandatory' => false,
            'options' => [],
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
        FormBuilderQuestionOption::query()->where('question_id', $questionId)->delete();
        $question->delete();

        Notification::make()
            ->title('Question removed')
            ->success()
            ->send();

        $this->loadQuestions($profileId);
    }

    public function moveQuestionUp(int $questionId): void
    {
        $this->reorderQuestion($questionId, -1);
    }

    public function moveQuestionDown(int $questionId): void
    {
        $this->reorderQuestion($questionId, 1);
    }

    protected function reorderQuestion(int $questionId, int $direction): void
    {
        $client = $this->requireClient();

        $question = FormBuilderQuestion::query()
            ->whereHas('profile', fn ($q) => $q->where('client_id', $client->id))
            ->where('question_id', $questionId)
            ->firstOrFail();

        $siblings = FormBuilderQuestion::query()
            ->where('profile_id', $question->profile_id)
            ->orderBy('question_order')
            ->get();

        $index = $siblings->search(fn (FormBuilderQuestion $item): bool => $item->question_id === $questionId);
        $swapIndex = $index + $direction;

        if ($index === false || $swapIndex < 0 || $swapIndex >= $siblings->count()) {
            return;
        }

        $other = $siblings[$swapIndex];
        $currentOrder = $question->question_order;
        $question->update(['question_order' => $other->question_order]);
        $other->update(['question_order' => $currentOrder]);

        $this->loadQuestions($question->profile_id);
    }

    /**
     * @param  array<int, array{option_name?: string}>  $options
     */
    protected function syncQuestionOptions(int $questionId, int $questionTypeId, array $options): void
    {
        $nextOptionId = (int) FormBuilderQuestionOption::query()->max('option_id');

        foreach ($options as $option) {
            if (! filled($option['option_name'] ?? null)) {
                continue;
            }

            $nextOptionId++;
            FormBuilderQuestionOption::query()->create([
                'option_id' => $nextOptionId ?: 1,
                'question_id' => $questionId,
                'option_name' => $option['option_name'],
                'question_option_type_id' => $questionTypeId,
            ]);
        }
    }

    protected function loadQuestions(int $profileId): void
    {
        $this->selectedProfileId = $profileId;
        $this->questions = FormBuilderQuestion::query()
            ->with(['questionType', 'options'])
            ->where('profile_id', $profileId)
            ->orderBy('question_order')
            ->get();

        $profile = Profile::query()->find($profileId);
        $this->formActive = (bool) $profile?->form_active;

        $this->form->fill([
            'profile_id' => $profileId,
            'form_active' => $this->formActive,
            'question_text' => $this->data['question_text'] ?? null,
            'question_type_id' => $this->data['question_type_id'] ?? $this->questionTypeOptions()->keys()->first(),
            'is_mandatory' => $this->data['is_mandatory'] ?? false,
            'options' => $this->data['options'] ?? [],
        ]);
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
                2 => 'Textarea',
                3 => 'Radio',
                4 => 'Checkbox',
                5 => 'Select',
                6 => 'Date',
                7 => 'Signature',
                8 => 'File',
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
