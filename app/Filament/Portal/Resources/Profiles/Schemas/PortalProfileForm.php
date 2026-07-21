<?php

namespace App\Filament\Portal\Resources\Profiles\Schemas;

use App\Enums\ProfileCodeType;
use App\Filament\Resources\Profiles\Schemas\ProfileFormSchema;
use App\Models\EquipmentType;
use App\Support\LegacySectionHelp;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

/**
 * Portal profile editor matched to live Location Add
 * (https://scanlink.com.au/location/index — see Location Add.html dump).
 */
class PortalProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                // When ?type= is set, type is fixed (legacy /location/index behaviour).
                Hidden::make('type_id')->required()->dehydrated(),
                Hidden::make('client_id')->required()->dehydrated(),
                Hidden::make('user_id')->dehydrated(),

                Section::make('Profile type')
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Select::make('type_id_display')
                            ->label('Profile Type')
                            ->options(fn (): array => EquipmentType::query()
                                ->where('slag', '!=', 'people')
                                ->pluck('name', 'id')
                                ->all())
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set): void {
                                $set('type_id', $state);
                            })
                            ->dehydrated(false),
                    ])
                    ->visible(fn (string $operation): bool => $operation === 'create' && blank(request()->query('type'))),

                Section::make('Code Profile Name')
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        TextInput::make('code_profile_name')
                            ->hiddenLabel()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Placeholder::make('activation_period_hint')
                            ->hiddenLabel()
                            ->content(new HtmlString(
                                '<span class="set-activation-period" style="color:#e67e22;font-size:12px;">Set activation period (optional)<br>Leave date fields blank if this does not apply.</span>'
                            ))
                            ->columnSpanFull(),
                        DatePicker::make('activation_start_date')
                            ->label('Start Date'),
                        DatePicker::make('activation_end_date')
                            ->label('End Date'),
                        Actions::make([
                            Action::make('clear_activation_dates')
                                ->label('Clear')
                                ->color('warning')
                                ->action(function (Set $set): void {
                                    $set('activation_start_date', null);
                                    $set('activation_end_date', null);
                                }),
                        ])->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make(LegacySectionHelp::heading('Logo'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        FileUpload::make('logo_upload')
                            ->label('Upload Company Logo: (File type JPG, JPEG, PNG, GIF) (Max file size 10 MB)')
                            ->image()
                            ->directory('profiles/logos')
                            ->disk('public')
                            ->maxSize(10240),
                    ]),

                Section::make(LegacySectionHelp::heading('Videos'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Repeater::make('video_titles')
                            ->label('Upload a Video:')
                            ->schema([
                                TextInput::make('title')->label('Video title'),
                                TextInput::make('video_name')
                                    ->label('YouTube URL or video ID'),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Upload Video')
                            ->columnSpanFull(),
                    ]),

                // Live Words section includes CONTACT / TELEPHONE inside it.
                Section::make(LegacySectionHelp::heading('Words'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema(fn (Get $get): array => [
                        ...ProfileFormSchema::typeFields(
                            filled($get('type_id')) ? (int) $get('type_id') : null
                        ),
                        ...self::contactsFields($get),
                    ])
                    ->columns(1)
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['survey', 'code', 'customqr'], true)),

                Section::make(LegacySectionHelp::heading('Pictures'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Repeater::make('pictures')
                            ->relationship()
                            ->schema([
                                FileUpload::make('picture_name')
                                    ->label('Upload a picture: (File type JPG, JPEG, PNG, GIF) (Max file size 10 MB)')
                                    ->image()
                                    ->directory('profiles/pictures')
                                    ->disk('public')
                                    ->maxSize(10240)
                                    ->required(),
                                TextInput::make('txt_footer')
                                    ->label('Text Footer')
                                    ->maxLength(500),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Upload a picture')
                            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data, Get $get): array => self::stampMediaOwner($data, $get))
                            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data, Get $get): array => self::stampMediaOwner($data, $get))
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['survey', 'code'], true)),

                Section::make(LegacySectionHelp::heading('Documents'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Repeater::make('documents')
                            ->relationship()
                            ->schema([
                                FileUpload::make('doc_name')
                                    ->label('Upload a Document: (File type DOC, DOCX, PDF, JPG, GIF, JPEG) (Max file size 10 MB)')
                                    ->directory('profiles/documents')
                                    ->disk('public')
                                    ->maxSize(10240)
                                    ->required(),
                                TextInput::make('name')
                                    ->label('Title')
                                    ->maxLength(255),
                                Radio::make('txt_align')
                                    ->label('Text Alignment')
                                    ->options([
                                        'left' => 'Left',
                                        'center' => 'Center',
                                        'right' => 'Right',
                                    ])
                                    ->default('left')
                                    ->inline(),
                                ColorPicker::make('btn_color')
                                    ->label('Button Color')
                                    ->default('#007A01')
                                    ->formatStateUsing(function ($state): string {
                                        $value = trim((string) ($state ?: '007A01'));

                                        return str_starts_with($value, '#') ? $value : '#'.$value;
                                    })
                                    ->dehydrateStateUsing(fn ($state): string => ltrim((string) ($state ?: '007A01'), '#')),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Upload a Document')
                            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data, Get $get): array => self::stampMediaOwner($data, $get))
                            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data, Get $get): array => self::stampMediaOwner($data, $get))
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['survey', 'code'], true)),

                Section::make(LegacySectionHelp::heading('Web Link'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Repeater::make('weblinks')
                            ->relationship()
                            ->schema([
                                Checkbox::make('link_button')->label('Add Button')->default(false),
                                TextInput::make('link_button_text')->label('Button Text:'),
                                TextInput::make('link_button_url')
                                    ->label('Button Link URL: (Start with http://)')
                                    ->url(),
                                Radio::make('link_button_align')
                                    ->label('Button Text Alignment:')
                                    ->options([
                                        'left' => 'Left',
                                        'center' => 'Center',
                                        'right' => 'Right',
                                    ])
                                    ->default('left')
                                    ->inline()
                                    ->columnSpanFull(),
                                ColorPicker::make('link_button_color')
                                    ->label('Button color:')
                                    ->default('#007A01')
                                    ->formatStateUsing(function ($state): string {
                                        $value = trim((string) ($state ?: '007A01'));

                                        return str_starts_with($value, '#') ? $value : '#'.$value;
                                    })
                                    ->dehydrateStateUsing(fn ($state): string => ltrim((string) ($state ?: '007A01'), '#')),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->addActionLabel('AND ANOTHER WEB LINK')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['survey', 'code', 'voc'], true)),

                // Live: Yes/No radios — dependent fields hidden when No.
                Section::make(LegacySectionHelp::heading('Data Collection'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Radio::make('enable_data_collection')
                            ->label('Enable Data Collection Pop Up Window:')
                            ->options([
                                '1' => 'Yes',
                                '0' => 'No',
                            ])
                            ->default('0')
                            ->inline()
                            ->live()
                            ->formatStateUsing(fn ($state): string => ($state === true || $state === 1 || $state === '1') ? '1' : '0')
                            ->dehydrateStateUsing(fn ($state): bool => $state === '1' || $state === 1 || $state === true)
                            ->columnSpanFull(),
                        Checkbox::make('set_up_compulsory')
                            ->label('Set as compulsory')
                            ->columnSpanFull()
                            ->visible(fn (Get $get): bool => self::dataCollectionEnabled($get)),
                        Checkbox::make('data_collection_mobile')
                            ->label('Mobile')
                            ->visible(fn (Get $get): bool => self::dataCollectionEnabled($get)),
                        Checkbox::make('data_collection_email')
                            ->label('Email')
                            ->visible(fn (Get $get): bool => self::dataCollectionEnabled($get)),
                        Checkbox::make('data_collection_name')
                            ->label('Name')
                            ->visible(fn (Get $get): bool => self::dataCollectionEnabled($get)),
                        Checkbox::make('data_collection_surname')
                            ->label('Surname')
                            ->visible(fn (Get $get): bool => self::dataCollectionEnabled($get)),
                        Textarea::make('data_collection_content')
                            ->label('Content')
                            ->maxLength(150)
                            ->rows(4)
                            ->columnSpanFull()
                            ->visible(fn (Get $get): bool => self::dataCollectionEnabled($get)),
                        // Exhibit-only (not on location form).
                        TextInput::make('data_collection_btn_text')
                            ->label('Button text')
                            ->default('')
                            ->dehydrateStateUsing(fn (?string $state): string => $state ?? '')
                            ->visible(fn (Get $get): bool => self::dataCollectionEnabled($get) && self::slug($get('type_id')) === 'exhibit'),
                        ColorPicker::make('data_collection_btn_color')
                            ->label('Button colour')
                            ->default('#007A01')
                            ->formatStateUsing(function ($state): string {
                                $value = trim((string) ($state ?: '007A01'));

                                return str_starts_with($value, '#') ? $value : '#'.$value;
                            })
                            ->dehydrateStateUsing(fn ($state): string => ltrim((string) ($state ?: ''), '#'))
                            ->visible(fn (Get $get): bool => self::dataCollectionEnabled($get) && self::slug($get('type_id')) === 'exhibit'),
                    ])
                    ->columns(4)
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['survey', 'code'], true)),

                // Sidebar Enable / analytics / format bind to these (always dehydrated).
                Toggle::make('form_is_enable')
                    ->label('Enable form on profile')
                    ->hidden(fn (Get $get): bool => self::slug($get('type_id')) !== 'survey')
                    ->dehydrated(),
                Toggle::make('enable_form_analytics')
                    ->label('Enable Form Analytics')
                    ->hidden()
                    ->dehydrated(),
                Radio::make('form_submission_format')
                    ->label('Form submission format')
                    ->options([
                        0 => 'Email only',
                        1 => 'Email notification with PDF',
                    ])
                    ->default(0)
                    ->hidden()
                    ->dehydrated(),
                TextInput::make('form_title')
                    ->label('Form display title')
                    ->hidden(fn (Get $get): bool => self::slug($get('type_id')) !== 'survey')
                    ->dehydrated(),
                TextInput::make('form_email_tag')
                    ->label('Email Tag')
                    ->hidden()
                    ->dehydrated(),

                Section::make('URL Link')
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema(fn (Get $get): array => ProfileFormSchema::typeFields(
                        filled($get('type_id')) ? (int) $get('type_id') : null
                    ))
                    ->visible(fn (Get $get): bool => in_array(self::slug($get('type_id')), ['code', 'customqr'], true)),

                Section::make('Form / Survey')
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        TextInput::make('code_profile_name')->label('Code Profile Name')->maxLength(255),
                        TextInput::make('name')->label('Form title'),
                        Toggle::make('form_active')->label('Form active'),
                    ])
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'survey'),

                Section::make(LegacySectionHelp::heading('Set Code Type'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Radio::make('code_type')
                            ->label('Code Type:')
                            ->options(collect(ProfileCodeType::cases())->mapWithKeys(
                                fn (ProfileCodeType $t) => [$t->value => $t->label()]
                            ))
                            ->default(ProfileCodeType::QrCode->value)
                            ->inline(),
                    ]),

                Section::make(LegacySectionHelp::heading('Header'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Checkbox::make('show_header')
                            ->label('Display the code number at the top of mobile screen'),
                    ]),

                // Live: Yes/No radios + always-visible Password field.
                Section::make(LegacySectionHelp::heading('User Access Security'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Radio::make('protect')
                            ->label('Password protect?:')
                            ->options([
                                '1' => 'Yes',
                                '0' => 'No',
                            ])
                            ->default('0')
                            ->inline()
                            ->live()
                            ->formatStateUsing(fn ($state): string => ($state === true || $state === 1 || $state === '1') ? '1' : '0')
                            ->dehydrateStateUsing(fn ($state): bool => $state === '1' || $state === 1 || $state === true),
                        TextInput::make('password')
                            ->label('Password:')
                            ->maxLength(255)
                            ->visible(fn (Get $get): bool => in_array($get('protect'), ['1', 1, true], true))
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (?string $state): string => (string) ($state ?? '')),
                    ]),

                Section::make(LegacySectionHelp::heading('Share'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Checkbox::make('display_share_link')->label('Display share links'),
                        Placeholder::make('share_icons')
                            ->hiddenLabel()
                            ->content(new HtmlString(
                                '<div class="shareNav">'
                                .'<a class="shareFB" href="javascript:;" title="Facebook">Facebook</a>'
                                .'<a class="shareTWT" href="javascript:;" title="Twitter">Twitter</a>'
                                .'<a class="shareEML" href="javascript:;" title="Email">Email</a>'
                                .'</div>'
                            )),
                    ]),

                Section::make('Checklist items')
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'plant')
                    ->schema([
                        Repeater::make('checklistItems')
                            ->relationship()
                            ->schema([
                                Textarea::make('checklist_item')->label('Item')->required(),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * @return array<int, mixed>
     */
    private static function contactsFields(Get $get): array
    {
        if (! in_array(self::slug($get('type_id')), [
            'location', 'plant', 'asset', 'product', 'procedure',
        ], true)) {
            return [];
        }

        return [
            Repeater::make('contacts')
                ->relationship()
                ->label('')
                ->schema([
                    TextInput::make('name_company')->label('CONTACT:'),
                    TextInput::make('telephone')->label('TELEPHONE:'),
                ])
                ->columns(2)
                ->defaultItems(1)
                ->addActionLabel('And another')
                ->columnSpanFull(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private static function stampMediaOwner(array $data, Get $get): array
    {
        $data['client_id'] = (int) ($get('client_id') ?: 0);
        $data['user_id'] = (int) ($get('user_id') ?: 0);
        $data['is_temp'] = false;

        return $data;
    }

    private static function dataCollectionEnabled(Get $get): bool
    {
        return in_array($get('enable_data_collection'), ['1', 1, true], true);
    }

    private static function slug(mixed $typeId): ?string
    {
        if (! filled($typeId)) {
            return null;
        }

        return EquipmentType::query()->whereKey((int) $typeId)->value('slag');
    }
}
