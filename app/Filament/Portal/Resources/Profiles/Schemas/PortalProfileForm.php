<?php

namespace App\Filament\Portal\Resources\Profiles\Schemas;

use App\Enums\ProfileCodeType;
use App\Filament\Resources\Profiles\Schemas\ProfileFormSchema;
use App\Models\EquipmentType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class PortalProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        // Legacy location/asset templates: one stacked column of SectionTitleBox cards
        // (left), with iPhone + Form Builder in a separate right column in the blade.
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
                            ->label('Code Profile Name')
                            ->placeholder('Enter code profile name')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        DatePicker::make('activation_start_date')
                            ->label('Start Date')
                            ->helperText('Set activation period (optional). Leave date fields blank if this does not apply.'),
                        DatePicker::make('activation_end_date')
                            ->label('End Date'),
                    ])
                    ->columns(2),

                Section::make('Logo')
                    ->description('Upload Company Logo: (File type JPG, JPEG, PNG, GIF)')
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        FileUpload::make('logo_upload')
                            ->label('Logo')
                            ->image()
                            ->directory('profiles/logos')
                            ->disk('public')
                            ->helperText('Select a file, then save the profile to upload.'),
                    ]),

                Section::make('Videos')
                    ->description('Upload a Video')
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Repeater::make('video_titles')
                            ->label('Videos')
                            ->schema([
                                TextInput::make('title')->label('Video title'),
                                TextInput::make('video_name')
                                    ->label('YouTube URL or video ID'),
                            ])
                            ->dehydrated(false)
                            ->defaultItems(0)
                            ->addActionLabel('Upload Video')
                            ->columnSpanFull(),
                    ]),

                Section::make('Words')
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema(fn (Get $get): array => ProfileFormSchema::typeFields(
                        filled($get('type_id')) ? (int) $get('type_id') : null
                    ))
                    ->columns(1)
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['survey', 'code', 'customqr'], true)),

                Section::make('Pictures')
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        FileUpload::make('picture_uploads')
                            ->label('Upload pictures')
                            ->image()
                            ->multiple()
                            ->directory('profiles/pictures')
                            ->disk('public'),
                    ])
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['survey', 'code'], true)),

                Section::make('Documents')
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        FileUpload::make('document_uploads')
                            ->label('Upload documents')
                            ->multiple()
                            ->directory('profiles/documents')
                            ->disk('public'),
                    ])
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['survey', 'code'], true)),

                Section::make('Web Link')
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Repeater::make('weblinks')
                            ->relationship()
                            ->schema([
                                Toggle::make('link_button')->label('Add Button')->default(true),
                                TextInput::make('link_button_text')->label('Button Text'),
                                TextInput::make('link_button_url')->label('Button Link')->url(),
                                TextInput::make('link_button_color')->label('Colour'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('ADD ANOTHER WEB LINK')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['survey', 'code', 'voc'], true)),

                Section::make('Data Collection')
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Toggle::make('enable_data_collection')->label('Enable data collection (Yes/No)'),
                        Toggle::make('set_up_compulsory')->label('Set as compulsory'),
                        Toggle::make('data_collection_name')->label('Name'),
                        Toggle::make('data_collection_surname')->label('Surname'),
                        Toggle::make('data_collection_email')->label('Email'),
                        Toggle::make('data_collection_mobile')->label('Mobile'),
                        Textarea::make('data_collection_content')->label('Content')->columnSpanFull(),
                        TextInput::make('data_collection_btn_text')
                            ->label('Button text')
                            ->default('')
                            ->dehydrateStateUsing(fn (?string $state): string => $state ?? ''),
                        TextInput::make('data_collection_btn_color')
                            ->label('Button colour')
                            ->default('')
                            ->dehydrateStateUsing(fn (?string $state): string => $state ?? ''),
                    ])
                    ->columns(2)
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['survey', 'code'], true)),

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
                        TextInput::make('form_title')->label('Form display title'),
                        Toggle::make('form_active')->label('Form active'),
                        Toggle::make('form_is_enable')->label('Enable form on profile'),
                    ])
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'survey'),

                Section::make('Set Code Type')
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Select::make('code_type')
                            ->label('Code Type')
                            ->options(collect(ProfileCodeType::cases())->mapWithKeys(
                                fn (ProfileCodeType $t) => [$t->value => $t->label()]
                            )),
                        Toggle::make('show_header')->label('Show header in mobile'),
                        ProfileFormSchema::protectToggle(),
                        ProfileFormSchema::passwordField(),
                        Toggle::make('display_share_link')->label('Display share links'),
                    ])
                    ->columns(2),

                Section::make('Contacts')
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Repeater::make('contacts')
                            ->relationship()
                            ->schema([
                                TextInput::make('name_company')->label('Contact Person / Company'),
                                TextInput::make('telephone')->label('Telephone'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('ADD ANOTHER')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get): bool => in_array(self::slug($get('type_id')), [
                        'location', 'plant', 'asset', 'product', 'procedure',
                    ], true)),

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

    private static function slug(mixed $typeId): ?string
    {
        if (! filled($typeId)) {
            return null;
        }

        return EquipmentType::query()->whereKey((int) $typeId)->value('slag');
    }
}
