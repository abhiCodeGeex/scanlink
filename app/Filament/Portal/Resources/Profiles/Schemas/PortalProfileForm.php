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
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

/**
 * Portal profile editor matched to live Location / Plant / Person-Business templates
 * (https://scanlink.com.au/location/index, /plant/edit/{id}, /asset/edit/{id}).
 * Shared section shell; type-specific Words come from ProfileFormSchema::typeFields().
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
                            // Legacy: "Code Profile Name" is the one required field on the
                            // standard code editor (jQuery validate `txt_code_profile_name`),
                            // for every standard type. The type "name" field is NOT required.
                            // Excluded: code (no field), customqr & people (legacy requires
                            // their own url/name instead, not code_profile_name).
                            ->required(fn (Get $get): bool => in_array(
                                self::slug($get('type_id')),
                                ['location', 'plant', 'asset', 'product', 'procedure', 'misc', 'survey', 'exhibit', 'voc'],
                                true,
                            ))
                            ->columnSpanFull(),
                        Placeholder::make('activation_period_hint')
                            ->hiddenLabel()
                            ->content(new HtmlString(
                                '<span class="set-activation-period" style="color:#e67e22;font-size:12px;">Set activation period (optional)<br>Leave date fields blank if this does not apply.</span>'
                            ))
                            ->columnSpanFull(),
                        DatePicker::make('activation_start_date')
                            ->label('Start Date')
                            // Legacy: if one activation date is set, the other is required.
                            ->required(fn (Get $get): bool => filled($get('activation_end_date')))
                            ->beforeOrEqual('activation_end_date')
                            // Legacy: range must fall before expiry unless code needs renewal.
                            ->maxDate(fn (?Model $record): ?string => self::activationMaxDate($record))
                            ->validationMessages([
                                'max_date' => "Date range can't be set until the code is renewed.",
                            ]),
                        DatePicker::make('activation_end_date')
                            ->label('End Date')
                            ->required(fn (Get $get): bool => filled($get('activation_start_date')))
                            ->afterOrEqual('activation_start_date')
                            ->maxDate(fn (?Model $record): ?string => self::activationMaxDate($record))
                            ->validationMessages([
                                'max_date' => "Date range can't be set until the code is renewed.",
                            ]),
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
                    ->columns(2)
                    // Legacy code/index.php + people/index.php have no Code Profile Name / activation block.
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['code', 'people'], true)),

                // Legacy voc/index.php — Additional User Access Login (before Logo).
                Section::make(LegacySectionHelp::heading('Additional User Access Login'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Repeater::make('vocUsers')
                            ->relationship()
                            ->hiddenLabel()
                            ->schema([
                                TextInput::make('email')
                                    ->label('Email:')
                                    ->email()
                                    ->maxLength(255),
                                TextInput::make('password')
                                    ->label('Password:')
                                    ->password()
                                    ->revealable()
                                    // Legacy voc: additional-login password must be at least 6 chars (when set).
                                    ->minLength(6)
                                    ->maxLength(255)
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->dehydrateStateUsing(fn (?string $state): string => (string) ($state ?? '')),
                            ])
                            ->columns(2)
                            // Legacy voc skips blank additional-login rows; don't seed a forced empty row.
                            ->defaultItems(0)
                            ->addActionLabel('Add Another')
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                $data['voc_user_id'] = self::nextLegacyId('voc_users', 'voc_user_id');

                                return $data;
                            })
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'voc'),

                // Legacy voc/index.php — Email Notification Settings (before Logo).
                Section::make(LegacySectionHelp::heading('Email Notification Settings'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Repeater::make('vocRecipients')
                            ->relationship()
                            ->label('Recipients Email:')
                            ->schema([
                                TextInput::make('email')
                                    ->label('Recipients Email:')
                                    ->email()
                                    ->maxLength(255)
                                    ->required(),
                            ])
                            // Legacy voc recipients are optional (blanks skipped) — no forced required row.
                            ->defaultItems(0)
                            ->addActionLabel('Add Another')
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                $data['voc_recipient_id'] = self::nextLegacyId('voc_recipients', 'voc_recipient_id');

                                return $data;
                            })
                            ->columnSpanFull(),
                        TextInput::make('voc_email_url')
                            ->label('Email Text hyperlink URL:')
                            ->helperText('The default URL is http://www.myskills.gov.au')
                            ->columnSpanFull(),
                        TextInput::make('voc_email_text')
                            ->label('Email Text:')
                            ->helperText('The default text is "Click here to find a service provider near you"')
                            ->columnSpanFull(),
                        Textarea::make('voc_email_sign_line1')
                            ->label('Email Signature:')
                            ->helperText('The default signature is "ScanLink Support Team"')
                            ->rows(4)
                            ->extraInputAttributes([
                                'class' => 'sl-ckeditor',
                                'data-ck-toolbar' => 'custom',
                            ])
                            ->columnSpanFull(),
                        // Legacy voc has a second signature line (voc_email_sign_line2). Rich text
                        // (CKEditor) supersedes the legacy per-line bold/italic/underline flags.
                        Textarea::make('voc_email_sign_line2')
                            ->label('Email Signature (line 2):')
                            ->rows(3)
                            ->extraInputAttributes([
                                'class' => 'sl-ckeditor',
                                'data-ck-toolbar' => 'custom',
                            ])
                            ->columnSpanFull(),
                        Placeholder::make('voc_email_preview_hint')
                            ->hiddenLabel()
                            ->content(new HtmlString(
                                '<div class="voc-email-preview-hint" style="color:#666;font-size:12px;margin-top:4px;">'
                                .'Remember to save this profile before previewing email notifications'
                                .'</div>'
                            ))
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'voc'),

                // Legacy exhibit/voc tile drag-reordering (tiles_order): the scan page renders
                // content tiles in this order. Reorder-only (no add/remove).
                Section::make(LegacySectionHelp::heading('Display Order'))
                    ->description('Drag to set the order these sections appear on the scanned mobile page.')
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Repeater::make('tile_order')
                            ->hiddenLabel()
                            ->dehydrated()
                            ->schema([
                                Hidden::make('id'),
                                Hidden::make('label'),
                                Placeholder::make('tile_label')
                                    ->hiddenLabel()
                                    ->content(fn (Get $get): string => (string) $get('label')),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->reorderable()
                            ->reorderableWithButtons()
                            ->addable(false)
                            ->deletable(false)
                            ->columns(1)
                            ->default(fn (Get $get): array => \App\Models\Profile::tileOrderFormItems(self::slug($get('type_id')), null)),
                    ])
                    ->visible(fn (Get $get): bool => in_array(self::slug($get('type_id')), ['exhibit', 'voc'], true)),

                Section::make(LegacySectionHelp::heading('Logo'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        FileUpload::make('logo_upload')
                            ->label(fn (Get $get): string => self::slug($get('type_id')) === 'voc'
                                ? 'Upload Company Logo: (File type JPEG, PNG, GIF) (Max file size 10 MB)'
                                : 'Upload Company Logo: (File type JPG, JPEG, PNG, GIF) (Max file size 10 MB)')
                            ->image()
                            ->directory('profiles/logos')
                            ->disk('public')
                            ->maxSize(10240),
                    ])
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) !== 'code'),

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
                    ])
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['code', 'survey', 'voc'], true)),

                // Live Words / Profile Information — type-specific fields + contacts where legacy has them.
                Section::make(LegacySectionHelp::heading('Words'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema(fn (Get $get): array => [
                        ...ProfileFormSchema::typeFields(
                            filled($get('type_id')) ? (int) $get('type_id') : null
                        ),
                        ...self::contactsFields($get),
                    ])
                    ->columns(1)
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['survey', 'code', 'customqr', 'voc'], true)),

                // Legacy voc order: Title Bar before Profile Picture / Profile Information.
                Section::make(LegacySectionHelp::heading('Title Bar'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Checkbox::make('voc_title_bar_enable')
                            ->label('Enable')
                            ->default(true),
                        Textarea::make('voc_title_bar_text')
                            ->label('Text:')
                            ->rows(4)
                            ->extraInputAttributes([
                                'class' => 'sl-ckeditor',
                                'data-ck-toolbar' => 'custom1',
                            ])
                            ->columnSpanFull(),
                        ColorPicker::make('voc_title_bar_colour')
                            ->label('Title Bar Background Colour:')
                            ->default('#777777')
                            ->formatStateUsing(function ($state): string {
                                $value = trim((string) ($state ?: '777777'));

                                return str_starts_with($value, '#') ? $value : '#'.$value;
                            })
                            ->dehydrateStateUsing(fn ($state): string => ltrim((string) ($state ?: '777777'), '#')),
                    ])
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'voc'),

                Section::make(LegacySectionHelp::heading('Profile Picture'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Repeater::make('pictures')
                            ->relationship()
                            ->hiddenLabel()
                            ->schema([
                                FileUpload::make('picture_name')
                                    ->label('Upload a picture: (File type JPEG, PNG, GIF) (Max file size 10 MB)')
                                    ->image()
                                    ->directory('profiles/pictures')
                                    ->disk('public')
                                    ->maxSize(10240)
                                    ->required(),
                            ])
                            // Legacy voc allows multiple profile pictures (gallery).
                            ->defaultItems(0)
                            ->addActionLabel('Upload a picture')
                            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data, Get $get): array => self::stampMediaOwner($data, $get))
                            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data, Get $get): array => self::stampMediaOwner($data, $get))
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'voc'),

                Section::make(LegacySectionHelp::heading('Profile Information'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema(fn (Get $get): array => ProfileFormSchema::typeFields(
                        filled($get('type_id')) ? (int) $get('type_id') : null
                    ))
                    ->columns(2)
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'voc'),

                Section::make(LegacySectionHelp::heading('Document Upload'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Repeater::make('vocDocuments')
                            ->relationship()
                            ->hiddenLabel()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Document Name')
                                    ->maxLength(200),
                                DatePicker::make('expiry_date')
                                    ->label('Expiry Date'),
                                FileUpload::make('file_name')
                                    ->label('Upload a Document: (File type DOC, DOCX, PDF, GIF, JPEG, PNG) (Max file size 10 MB)')
                                    ->directory('profiles/voc-documents')
                                    ->disk('public')
                                    ->maxSize(10240)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            // Legacy voc skips blank document rows; don't seed a forced empty row.
                            ->defaultItems(0)
                            ->addActionLabel('Add Another')
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                $data['voc_document_id'] = self::nextLegacyId('voc_documents', 'voc_document_id');

                                return $data;
                            })
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'voc'),

                Section::make(LegacySectionHelp::heading('Pictures'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Repeater::make('pictures_general')
                            ->relationship('pictures')
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
                                    ->maxLength(500)
                                    // Legacy misc/people Pictures are upload only (no Text Footer).
                                    ->visible(function (Get $get): bool {
                                        $typeId = $get('../../type_id') ?? $get('type_id');
                                        if (filled($typeId)) {
                                            return ! in_array(self::slug($typeId), ['misc', 'people'], true);
                                        }

                                        return ! in_array(request()->query('type'), ['misc', 'people'], true);
                                    }),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Upload a picture')
                            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data, Get $get): array => self::stampMediaOwner($data, $get))
                            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data, Get $get): array => self::stampMediaOwner($data, $get))
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['survey', 'code', 'voc'], true)),

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
                                    ->inline()
                                    // Legacy people Documents are upload + name only (no styling).
                                    ->visible(fn (Get $get): bool => self::slug($get('../../type_id') ?? $get('type_id')) !== 'people'),
                                ColorPicker::make('btn_color')
                                    ->label('Button Color')
                                    ->default('#007A01')
                                    ->visible(fn (Get $get): bool => self::slug($get('../../type_id') ?? $get('type_id')) !== 'people')
                                    ->formatStateUsing(function ($state): string {
                                        $value = trim((string) ($state ?: '007A01'));

                                        return str_starts_with($value, '#') ? $value : '#'.$value;
                                    })
                                    ->dehydrateStateUsing(fn ($state): string => ltrim((string) ($state ?: '007A01'), '#')),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Upload a Document')
                            // Legacy parity: documents are drag-sortable and the mobile page
                            // renders them in that saved order (persisted to `sort_order`).
                            ->reorderable()
                            ->orderColumn('sort_order')
                            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data, Get $get): array => self::stampMediaOwner($data, $get))
                            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data, Get $get): array => self::stampMediaOwner($data, $get))
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['survey', 'code', 'voc'], true)),

                Section::make(LegacySectionHelp::heading('Web Link'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Repeater::make('weblinks')
                            ->relationship()
                            ->schema([
                                Checkbox::make('link_button')
                                    ->label('Add Button')
                                    ->default(false)
                                    ->live(debounce: 400)
                                    ->afterStateUpdated(function ($state, $livewire): void {
                                        if (method_exists($livewire, 'pushPhonePreviewDraft')) {
                                            $livewire->pushPhonePreviewDraft();
                                        }
                                    }),
                                TextInput::make('link_button_text')
                                    ->label('Button Text:')
                                    ->live(debounce: 400)
                                    ->afterStateUpdated(function ($state, $livewire): void {
                                        if (method_exists($livewire, 'pushPhonePreviewDraft')) {
                                            $livewire->pushPhonePreviewDraft();
                                        }
                                    }),
                                TextInput::make('link_button_url')
                                    ->label('Button Link URL: (Start with http://)')
                                    ->url()
                                    ->live(debounce: 400)
                                    ->afterStateUpdated(function ($state, $livewire): void {
                                        if (method_exists($livewire, 'pushPhonePreviewDraft')) {
                                            $livewire->pushPhonePreviewDraft();
                                        }
                                    }),
                                Radio::make('link_button_align')
                                    ->label('Button Text Alignment:')
                                    ->options([
                                        'left' => 'Left',
                                        'center' => 'Center',
                                        'right' => 'Right',
                                    ])
                                    ->default('left')
                                    ->inline()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $livewire): void {
                                        if (method_exists($livewire, 'pushPhonePreviewDraft')) {
                                            $livewire->pushPhonePreviewDraft();
                                        }
                                    })
                                    ->columnSpanFull(),
                                ColorPicker::make('link_button_color')
                                    ->label('Button color:')
                                    ->default('#007A01')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, $livewire): void {
                                        if (method_exists($livewire, 'pushPhonePreviewDraft')) {
                                            $livewire->pushPhonePreviewDraft();
                                        }
                                    })
                                    ->formatStateUsing(function ($state): string {
                                        $value = trim((string) ($state ?: '007A01'));

                                        return str_starts_with($value, '#') ? $value : '#'.$value;
                                    })
                                    ->dehydrateStateUsing(fn ($state): string => ltrim((string) ($state ?: '007A01'), '#')),
                            ])
                            ->columns(2)
                            // Legacy skips blank web-link rows; don't seed a forced empty row.
                            ->defaultItems(0)
                            ->addActionLabel('AND ANOTHER WEB LINK')
                            ->columnSpanFull(),
                    ])
                    // Legacy people/index.php has no Web Link section.
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['survey', 'code', 'voc', 'people'], true)),

                // Legacy exhibit tile order: Share sits before Logo #2 / Words #2 / Data Collection.
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
                    ])
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'exhibit'),

                Section::make(LegacySectionHelp::heading('Logo #2'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        FileUpload::make('logo_extra_upload')
                            ->label('Upload Company Logo: (File type JPG, JPEG, PNG, GIF) (Max file size 10 MB)')
                            ->image()
                            ->directory('profiles/logos')
                            ->disk('public')
                            ->maxSize(10240),
                        // Legacy exhibit Logo #2 has a click-through URL saved to logo_extra.logo_url.
                        TextInput::make('logo_extra_url')
                            ->label('URL (start with http://)')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'exhibit'),

                Section::make(LegacySectionHelp::heading('Videos #2'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Repeater::make('video_extra_titles')
                            ->label('Upload a Video:')
                            ->schema([
                                TextInput::make('title')->label('Video title'),
                                TextInput::make('video_name')
                                    ->label('YouTube URL or video ID'),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Upload Video')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'exhibit'),

                Section::make(LegacySectionHelp::heading('Words #2'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        TextInput::make('name2')->hiddenLabel()->maxLength(255),
                        Textarea::make('description2')
                            ->hiddenLabel()
                            ->rows(4)
                            ->extraInputAttributes([
                                'class' => 'sl-ckeditor',
                                'data-ck-toolbar' => 'MyToolbar',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'exhibit'),

                Section::make(LegacySectionHelp::heading('Pictures #2'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Repeater::make('picturesExtra')
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
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'exhibit'),

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
                            // Legacy people Data Collection is Mobile/Email/Content only.
                            ->visible(fn (Get $get): bool => self::dataCollectionEnabled($get) && self::slug($get('type_id')) !== 'people'),
                        Checkbox::make('data_collection_mobile')
                            ->label('Mobile')
                            ->visible(fn (Get $get): bool => self::dataCollectionEnabled($get)),
                        Checkbox::make('data_collection_email')
                            ->label('Email')
                            ->visible(fn (Get $get): bool => self::dataCollectionEnabled($get)),
                        Checkbox::make('data_collection_name')
                            ->label('Name')
                            ->visible(fn (Get $get): bool => self::dataCollectionEnabled($get) && self::slug($get('type_id')) !== 'people'),
                        Checkbox::make('data_collection_surname')
                            ->label('Surname')
                            ->visible(fn (Get $get): bool => self::dataCollectionEnabled($get) && self::slug($get('type_id')) !== 'people'),
                        Textarea::make('data_collection_content')
                            ->label('Content')
                            ->maxLength(150)
                            ->rows(4)
                            ->columnSpanFull()
                            ->visible(fn (Get $get): bool => self::dataCollectionEnabled($get)),
                        // Exhibit-only (not on location form).
                        TextInput::make('data_collection_btn_text')
                            ->label('Button Text')
                            ->default('')
                            ->dehydrateStateUsing(fn (?string $state): string => $state ?? '')
                            ->visible(fn (Get $get): bool => self::dataCollectionEnabled($get) && self::slug($get('type_id')) === 'exhibit'),
                        ColorPicker::make('data_collection_btn_color')
                            ->label('Button Color')
                            ->default('#007A01')
                            ->formatStateUsing(function ($state): string {
                                $value = trim((string) ($state ?: '007A01'));

                                return str_starts_with($value, '#') ? $value : '#'.$value;
                            })
                            ->dehydrateStateUsing(fn ($state): string => ltrim((string) ($state ?: ''), '#'))
                            ->visible(fn (Get $get): bool => self::dataCollectionEnabled($get) && self::slug($get('type_id')) === 'exhibit'),
                    ])
                    ->columns(4)
                    // Legacy code/survey/voc: no separate Data Collection section.
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['survey', 'code', 'voc'], true)),

                // Sidebar Enable / analytics / format bind to these (always dehydrated).
                Toggle::make('form_is_enable')
                    ->label('Enable form on profile')
                    ->hidden()
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
                TextInput::make('form_email_tag')
                    ->label('Email Tag')
                    ->hidden()
                    ->dehydrated(),

                // Legacy code/index.php — single "Code no {id}" form (Application, URL, DC, colour, QR/DM).
                Section::make()
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box sl-code-url-form'])
                    ->schema(fn (): array => self::legacyCodeUrlFields())
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'code'),

                Section::make('URL Link')
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema(fn (Get $get): array => ProfileFormSchema::typeFields(
                        filled($get('type_id')) ? (int) $get('type_id') : null
                    ))
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'customqr'),

                // Legacy survey/index.php has no separate "Form Name" block — title comes from Form Builder.

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
                    ])
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['survey', 'code'], true)),

                Section::make(LegacySectionHelp::heading('Header'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Checkbox::make('show_header')
                            ->label('Display the code number at the top of mobile screen'),
                    ])
                    // Legacy people/index.php has no Header section.
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['code', 'survey', 'voc', 'people'], true)),

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
                            // Legacy plant/location: password field stays visible even when protect = No.
                            // Legacy people has the protect radio but NO password input — hide it for people.
                            ->visible(fn (Get $get): bool => self::slug($get('type_id')) !== 'people')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (?string $state): string => (string) ($state ?? '')),
                    ])
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['code', 'survey'], true)),

                Section::make(LegacySectionHelp::heading('Share'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        Checkbox::make('display_share_link')->label('Display share links'),
                        Placeholder::make('share_icons_default')
                            ->hiddenLabel()
                            ->content(new HtmlString(
                                '<div class="shareNav">'
                                .'<a class="shareFB" href="javascript:;" title="Facebook">Facebook</a>'
                                .'<a class="shareTWT" href="javascript:;" title="Twitter">Twitter</a>'
                                .'<a class="shareEML" href="javascript:;" title="Email">Email</a>'
                                .'</div>'
                            )),
                    ])
                    // Legacy people/index.php has no Share section.
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['code', 'survey', 'exhibit', 'voc', 'people'], true)),

                // Legacy plant/edit.php checklist UI is commented out — omit for plant parity.
            ]);
    }

    private static function nextLegacyId(string $table, string $primaryKey): int
    {
        $max = (int) \Illuminate\Support\Facades\DB::table($table)->max($primaryKey);

        return max(1, $max + 1);
    }

    /**
     * Legacy code/index.php field order in one section.
     *
     * @return array<int, mixed>
     */
    private static function legacyCodeUrlFields(): array
    {
        return [
            Placeholder::make('legacy_code_no_heading')
                ->hiddenLabel()
                ->content(function ($component): HtmlString {
                    $record = $component->getLivewire()->getRecord();
                    $id = $record?->getKey();

                    return new HtmlString(
                        '<div class="SectionTitleCode">Code no '.e($id !== null ? (string) $id : '').'</div>'
                    );
                })
                ->columnSpanFull(),
            TextInput::make('name')
                ->label(new HtmlString(
                    '<span class="codelabel" title="This is for you to reference where you have applied you QR Code">Application:</span>'
                ))
                ->placeholder('Enter application:')
                ->maxLength(255)
                ->extraInputAttributes(['class' => 'codeTextApplication'])
                ->columnSpanFull(),
            TextInput::make('url')
                ->label(new HtmlString(
                    '<span class="codelabel" title="This is where you enter the URL (web address) To where you want to redirect mobile users when they they scan your QR code.">Enter the Web page address(URL) here:</span>'
                ))
                ->placeholder('http://')
                ->default('http://')
                ->required()
                ->url()
                ->columnSpanFull(),
            Radio::make('enable_data_collection')
                ->label(new HtmlString(
                    '<span class="codelabel" title="The Data Collection option enables you to feature a pop up window with a customised message to collect mobile user information which is stored in your Visitor Log.">Enable Data Collection Pop Up Window:</span>'
                ))
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
            // Legacy shows these controls always (not gated behind Yes).
            Checkbox::make('set_up_compulsory')
                ->label('Set as compulsory')
                ->extraAttributes(['class' => 'sl-code-dc-compulsory'])
                ->columnSpanFull(),
            Grid::make(4)
                ->schema([
                    Checkbox::make('data_collection_mobile')->label('Mobile'),
                    Checkbox::make('data_collection_email')->label('Email'),
                    Checkbox::make('data_collection_name')->label('Name'),
                    Checkbox::make('data_collection_surname')->label('Surname'),
                ])
                ->extraAttributes(['class' => 'sl-code-dc-fields']),
            Textarea::make('description')
                ->label('pop up message:')
                ->placeholder('Enter pop up message:')
                ->rows(2)
                ->maxLength(150)
                ->columnSpanFull(),
            Grid::make(2)
                ->schema([
                    ColorPicker::make('color_code')
                        ->label('Colour Selector')
                        ->default('#000000')
                        ->formatStateUsing(function ($state): string {
                            $value = trim((string) ($state ?: '000000'));

                            return str_starts_with($value, '#') ? $value : '#'.$value;
                        })
                        ->dehydrateStateUsing(fn ($state): string => ltrim((string) ($state ?: '000000'), '#')),
                    Radio::make('code_type')
                        ->label('Select Code Type')
                        ->options([
                            ProfileCodeType::QrCode->value => 'QR',
                            ProfileCodeType::DataMatrix->value => 'DM',
                        ])
                        ->default(ProfileCodeType::QrCode->value)
                        ->inline()
                        ->extraAttributes(['class' => 'sl-code-type-radios']),
                ])
                ->extraAttributes(['class' => 'sl-code-colour-type-row']),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    private static function contactsFields(Get $get): array
    {
        if (! in_array(self::slug($get('type_id')), [
            'location', 'plant', 'product', 'procedure',
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
                // Legacy skips blank contact rows; don't seed a forced empty row.
                ->defaultItems(0)
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

    /**
     * Legacy: the activation date range must fall before the code's expiry, unless the
     * code is a free/non-expiring code that has not been flagged for renewal. Returns the
     * ceiling date (expired_at) or null when no ceiling applies.
     */
    private static function activationMaxDate(?Model $record): ?string
    {
        if (! $record instanceof \App\Models\Profile || $record->expired_at === null) {
            return null;
        }

        $applies = ! $record->free_code
            || ($record->free_code && (bool) ($record->getAttribute('renewal_required') ?? false));

        return $applies ? $record->expired_at->toDateString() : null;
    }
}
