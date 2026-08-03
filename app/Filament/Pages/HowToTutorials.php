<?php

namespace App\Filament\Pages;

use App\Models\HowToTutorial;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema as DbSchema;
use Illuminate\Validation\ValidationException;

class HowToTutorials extends Page
{
    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    protected static ?string $navigationLabel = 'How to';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'How to tutorials';

    protected static ?string $slug = 'how-to-tutorials';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->admin_role?->canManageSettings();
    }

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $items = HowToTutorial::catalog();

        $this->form->fill([
            'tutorials' => array_map(
                fn (array $item): array => [
                    'title' => $item['title'],
                    'url' => $item['url'],
                ],
                $items,
            ),
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
                Section::make('How to menu items')
                    ->description('These titles and URLs appear under the public and portal “How to” menus. Use + to add more, then Save once.')
                    ->aboveContent([
                        Actions::make([
                            Action::make('addHowTo')
                                ->label('Add How to')
                                ->icon('heroicon-o-plus')
                                ->color('gray')
                                ->action('addHowTo'),
                        ]),
                    ])
                    ->schema([
                        Repeater::make('tutorials')
                            ->label('Tutorials')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Title')
                                    ->placeholder('e.g. Upload a logo')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                TextInput::make('url')
                                    ->label('URL')
                                    ->placeholder('https://www.youtube.com/embed/…')
                                    ->required()
                                    ->url()
                                    ->maxLength(500)
                                    ->columnSpan(1),
                            ])
                            ->columns(2)
                            ->addable(false)
                            ->deletable()
                            ->reorderable()
                            ->collapsible(false)
                            ->defaultItems(0)
                            ->required()
                            ->minItems(1),
                    ])
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Save')
                                ->icon('heroicon-o-check')
                                ->action('save'),
                        ]),
                    ]),
            ]);
    }

    public function addHowTo(): void
    {
        $tutorials = array_values($this->data['tutorials'] ?? []);
        $tutorials[] = [
            'title' => '',
            'url' => '',
        ];

        $this->data['tutorials'] = $tutorials;
    }

    public function save(): void
    {
        if (! DbSchema::hasTable('how_to_tutorials')) {
            Notification::make()
                ->danger()
                ->title('How to table is missing. Run migrations first.')
                ->send();

            return;
        }

        $data = $this->form->getState();
        /** @var list<array{title?: string, url?: string}> $rows */
        $rows = array_values($data['tutorials'] ?? []);

        if ($rows === []) {
            throw ValidationException::withMessages([
                'data.tutorials' => 'Add at least one How to item.',
            ]);
        }

        $seen = [];
        $normalized = [];

        foreach ($rows as $index => $row) {
            $title = trim((string) ($row['title'] ?? ''));
            $url = HowToTutorial::normalizeUrl((string) ($row['url'] ?? ''));
            $key = mb_strtolower($title);

            if ($title === '' || $url === '') {
                throw ValidationException::withMessages([
                    "data.tutorials.{$index}.title" => 'Title and URL are required.',
                ]);
            }

            if (isset($seen[$key])) {
                throw ValidationException::withMessages([
                    "data.tutorials.{$index}.title" => 'Duplicate title “'.$title.'”. Each How to title must be unique.',
                ]);
            }

            $seen[$key] = true;
            $normalized[] = [
                'title' => $title,
                'url' => $url,
                'sort_order' => $index,
            ];
        }

        DB::transaction(function () use ($normalized): void {
            HowToTutorial::query()->delete();

            $now = now();
            HowToTutorial::query()->insert(array_map(
                fn (array $row): array => [
                    ...$row,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                $normalized,
            ));
        });

        $this->fillForm();

        Notification::make()
            ->success()
            ->title('How to tutorials saved.')
            ->body(count($normalized).' item(s) updated.')
            ->send();
    }

    public function getTitle(): string|Htmlable
    {
        return 'How to';
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
            ->id('form');
    }
}
