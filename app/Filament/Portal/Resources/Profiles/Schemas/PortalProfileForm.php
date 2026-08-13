<?php

namespace App\Filament\Portal\Resources\Profiles\Schemas;

use App\Enums\ProfileCodeType;
use App\Filament\Resources\Profiles\Schemas\ProfileFormSchema;
use App\Models\EquipmentType;
use App\Services\YouTubeService;
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
use Filament\Schemas\Components\Flex;
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
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box sl-code-profile-name-section'])
                    ->schema([
                        TextInput::make('code_profile_name')
                            ->hiddenLabel()
                            ->placeholder('Enter code profile name')
                            ->maxLength(255)
                            // Legacy: "Code Profile Name" is the one required field on the
                            // standard code editor (jQuery validate `txt_code_profile_name`),
                            // for every standard type. The type "name" field is NOT required.
                            // Excluded: code (no field), customqr & people (legacy requires
                            // their own url/name instead, not code_profile_name).
                            ->required(fn (Get $get, $livewire): bool => empty($livewire->bypassRequiredForCheckout ?? null) && in_array(
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
                            ->native(false)
                            ->displayFormat('d-m-Y')
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
                            ->native(false)
                            ->displayFormat('d-m-Y')
                            ->required(fn (Get $get): bool => filled($get('activation_start_date')))
                            ->afterOrEqual('activation_start_date')
                            ->maxDate(fn (?Model $record): ?string => self::activationMaxDate($record))
                            ->validationMessages([
                                'max_date' => "Date range can't be set until the code is renewed.",
                            ]),
                        Actions::make([
                            Action::make('clear_activation_dates')
                                ->label('Clear')
                                ->color('gray')
                                ->extraAttributes(['class' => 'sl-clear-activation-btn'])
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
                        self::vocUsersField(),
                    ])
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'voc'),

                // Legacy voc/index.php — Email Notification Settings (before Logo).
                Section::make(LegacySectionHelp::heading('Email Notification Settings'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box'])
                    ->schema([
                        self::vocRecipientsField(),
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
                        // Legacy keeps a second signature line but hides it (display:none); the
                        // CKEditor rich-text line 1 supersedes it. Kept hidden for data round-trip.
                        Hidden::make('voc_email_sign_line2')
                            ->dehydrated(),
                        // Legacy "Remember to save… + Preview" — renders the actual notification
                        // email (30-day renewal reminder / expiry notice) from the SAVED profile.
                        Placeholder::make('voc_email_preview_hint')
                            ->hiddenLabel()
                            ->content(function ($record): HtmlString {
                                $canPreview = $record && $record->exists;
                                $reminderUrl = $canPreview
                                    ? route('portal.voc.email-preview', ['profile' => $record->getKey(), 'kind' => 'reminder'])
                                    : null;
                                $expiredUrl = $canPreview
                                    ? route('portal.voc.email-preview', ['profile' => $record->getKey(), 'kind' => 'expired'])
                                    : null;

                                $links = $canPreview
                                    ? '<div class="voc-email-preview-links" style="margin-top:6px;display:flex;gap:16px;flex-wrap:wrap;">'
                                        .'<a href="'.$reminderUrl.'" target="_blank" rel="noopener" class="voc-email-preview-link" style="color:#008C00;font-weight:600;">Preview 30-day renewal reminder</a>'
                                        .'<a href="'.$expiredUrl.'" target="_blank" rel="noopener" class="voc-email-preview-link" style="color:#008C00;font-weight:600;">Preview expiry notification</a>'
                                        .'</div>'
                                    : '';

                                return new HtmlString(
                                    '<div class="voc-email-preview-hint" style="color:#666;font-size:12px;margin-top:4px;display:flex;align-items:center;gap:6px;">'
                                    .'<span style="color:#e08600;font-size:15px;">&#9888;</span>'
                                    .'<span>Remember to save this profile before previewing email notifications</span>'
                                    .'</div>'.$links
                                );
                            })
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'voc'),

                // Exhibit/voc tile ordering (legacy parity): the content sections below are
                // drag-reorderable by the handle in each section header. The order is mirrored
                // into this hidden field (comma-separated tile ids) and saved to `tiles_order`,
                // which the scanned mobile page renders in. See legacy-tile-reorder.blade.php.
                Hidden::make('tiles_order')
                    ->extraAttributes(['class' => 'sl-tiles-order-field'])
                    ->dehydrated()
                    ->visible(fn (Get $get): bool => in_array(self::slug($get('type_id')), ['exhibit', 'voc'], true)),

                Section::make(LegacySectionHelp::heading('Logo'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box sl-logo-section sl-sortable-tile', 'data-tile-id' => 1])
                    ->schema([
                        FileUpload::make('logo_upload')
                            ->label(fn (Get $get): string => self::slug($get('type_id')) === 'voc'
                                ? 'Upload Company Logo: (File type JPEG, PNG, GIF) (Max file size 10 MB)'
                                : 'Upload Company Logo: (File type JPG, JPEG, PNG, GIF) (Max file size 10 MB)')
                            ->image()
                            ->previewable(true)
                            ->panelLayout('compact')
                            ->imagePreviewHeight('76')
                            ->removeUploadedFileButtonPosition('right top')
                            ->extraAttributes(['class' => 'sl-logo-upload'])
                            ->directory('profiles/logos')
                            ->disk('public')
                            ->maxSize(10240),
                    ])
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) !== 'code'),

                Section::make(LegacySectionHelp::heading('Videos'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box sl-sortable-tile', 'data-tile-id' => 2])
                    ->schema(fn (): array => self::videoFields('video_titles'))
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['code', 'survey', 'voc'], true)),

                // Live Words / Profile Information — type-specific fields + contacts where legacy has them.
                Section::make(LegacySectionHelp::heading('Words'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box sl-sortable-tile', 'data-tile-id' => 3])
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
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box sl-sortable-tile', 'data-tile-id' => 2])
                    ->schema([
                        Checkbox::make('voc_title_bar_enable')
                            ->label('Enable')
                            ->default(true),
                        Textarea::make('voc_title_bar_text')
                            ->label('Text:')
                            ->rows(4)
                            // Legacy: title-bar text is required when "Enable" is ticked.
                            ->required(fn (Get $get): bool => (bool) $get('voc_title_bar_enable'))
                            ->validationMessages(['required' => 'Please enter title bar text.'])
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
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box sl-sortable-tile', 'data-tile-id' => 3])
                    ->schema([
                        Repeater::make('pictures')
                            ->relationship()
                            ->hiddenLabel()
                            ->extraAttributes(['class' => 'sl-pictures-repeater'])
                            ->schema([
                                FileUpload::make('picture_name')
                                    ->label('Upload a picture: (File type JPEG, PNG, GIF) (Max file size 10 MB)')
                                    ->image()
                                    ->panelLayout('compact')
                                    ->imagePreviewHeight('72')
                                    ->directory('profiles/pictures')
                                    ->disk('public')
                                    ->maxSize(10240)
                                    ->required(),
                            ])
                            // Legacy voc allows multiple profile pictures (gallery).
                            ->defaultItems(0)
                            ->addActionLabel(fn (Repeater $component): string => count($component->getRawState() ?? []) > 0
                                ? 'Add more'
                                : 'Upload picture')
                            ->addAction(fn (Action $action): Action => $action->color('primary')->icon('heroicon-m-plus'))
                            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data, Get $get): array => self::stampMediaOwner($data, $get))
                            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data, Get $get): array => self::stampMediaOwner($data, $get))
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'voc'),

                Section::make(LegacySectionHelp::heading('Profile Information'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box sl-sortable-tile', 'data-tile-id' => 4])
                    ->schema(fn (Get $get): array => ProfileFormSchema::typeFields(
                        filled($get('type_id')) ? (int) $get('type_id') : null
                    ))
                    ->columns(2)
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'voc'),

                Section::make(LegacySectionHelp::heading('Document Upload'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box sl-sortable-tile', 'data-tile-id' => 5])
                    ->schema([
                        self::vocDocumentsField(),
                    ])
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'voc'),

                Section::make(LegacySectionHelp::heading('Pictures'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box sl-sortable-tile', 'data-tile-id' => 4])
                    ->schema([
                        Repeater::make('pictures_general')
                            ->relationship('pictures')
                            ->hiddenLabel()
                            ->extraAttributes(['class' => 'sl-pictures-repeater'])
                            ->schema([
                                FileUpload::make('picture_name')
                                    ->label('Upload a picture: (File type JPG, JPEG, PNG, GIF) (Max file size 10 MB)')
                                    ->image()
                                    ->panelLayout('compact')
                                    ->imagePreviewHeight('72')
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
                            ->addActionLabel(fn (Repeater $component): string => count($component->getRawState() ?? []) > 0
                                ? 'Add more'
                                : 'Upload picture')
                            ->addAction(fn (Action $action): Action => $action->color('primary')->icon('heroicon-m-plus'))
                            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data, Get $get): array => self::stampMediaOwner($data, $get))
                            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data, Get $get): array => self::stampMediaOwner($data, $get))
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['survey', 'code', 'voc'], true)),

                Section::make(LegacySectionHelp::heading('Documents'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box sl-sortable-tile', 'data-tile-id' => 5])
                    ->schema(fn (Get $get): array => self::documentFields($get))
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['survey', 'code', 'voc'], true)),

                Section::make(LegacySectionHelp::heading('Web Link'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box sl-sortable-tile', 'data-tile-id' => 6])
                    ->schema(fn (): array => self::webLinkFields())
                    // Legacy people/index.php has no Web Link section.
                    ->visible(fn (Get $get): bool => ! in_array(self::slug($get('type_id')), ['survey', 'code', 'voc', 'people'], true)),

                // Legacy exhibit tile order: Share sits before Logo #2 / Words #2 / Data Collection.
                Section::make(LegacySectionHelp::heading('Share'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box sl-sortable-tile', 'data-tile-id' => 11])
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
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box sl-logo-section sl-sortable-tile', 'data-tile-id' => 17])
                    ->schema([
                        FileUpload::make('logo_extra_upload')
                            ->label('Upload Company Logo: (File type JPG, JPEG, PNG, GIF) (Max file size 10 MB)')
                            ->image()
                            ->previewable(true)
                            ->panelLayout('compact')
                            ->imagePreviewHeight('76')
                            ->removeUploadedFileButtonPosition('right top')
                            ->extraAttributes(['class' => 'sl-logo-upload'])
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
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box sl-sortable-tile', 'data-tile-id' => 14])
                    ->schema(fn (): array => self::videoFields('video_extra_titles'))
                    ->visible(fn (Get $get): bool => self::slug($get('type_id')) === 'exhibit'),

                Section::make(LegacySectionHelp::heading('Words #2'))
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box sl-sortable-tile', 'data-tile-id' => 15])
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
                    ->extraAttributes(['class' => 'SectionTitleBox sl-section-box sl-sortable-tile', 'data-tile-id' => 16])
                    ->schema([
                        Repeater::make('picturesExtra')
                            ->relationship()
                            ->hiddenLabel()
                            ->extraAttributes(['class' => 'sl-pictures-repeater'])
                            ->schema([
                                FileUpload::make('picture_name')
                                    ->label('Upload a picture: (File type JPG, JPEG, PNG, GIF) (Max file size 10 MB)')
                                    ->image()
                                    ->panelLayout('compact')
                                    ->imagePreviewHeight('72')
                                    ->directory('profiles/pictures')
                                    ->disk('public')
                                    ->maxSize(10240)
                                    ->required(),
                                TextInput::make('txt_footer')
                                    ->label('Text Footer')
                                    ->maxLength(500),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel(fn (Repeater $component): string => count($component->getRawState() ?? []) > 0
                                ? 'Add more'
                                : 'Upload picture')
                            ->addAction(fn (Action $action): Action => $action->color('primary')->icon('heroicon-m-plus'))
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
                // Use SectionTitleBoxCode (no border/inset shadow) — SectionTitleBox draws the dark inner ring.
                Section::make()
                    ->extraAttributes(['class' => 'SectionTitleBoxCode sl-section-box sl-code-url-form'])
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

                // Live: Yes/No radios; password only when protect = Yes.
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
                            // Legacy people has the protect radio but NO password input.
                            ->visible(fn (Get $get): bool => self::slug($get('type_id')) !== 'people'
                                && ($get('protect') === '1' || $get('protect') === 1 || $get('protect') === true))
                            ->required(fn (Get $get): bool => self::slug($get('type_id')) !== 'people'
                                && ($get('protect') === '1' || $get('protect') === 1 || $get('protect') === true))
                            // Push phone-preview draft when password is entered/blurred.
                            ->live(onBlur: true)
                            // Always persist when protect=Yes (do not skip empty / hidden dehydration).
                            ->dehydrated(fn (Get $get): bool => self::slug($get('type_id')) !== 'people'
                                && ($get('protect') === '1' || $get('protect') === 1 || $get('protect') === true))
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
                ->required(fn ($livewire): bool => empty($livewire->bypassRequiredForCheckout ?? null))
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
            // Legacy parity (create + edit share this schema):
            //   [ ] Set as compulsory          ← own row
            //   [ ] Mobile [ ] Email [ ] Name [ ] Surname  ← one inline row
            // Checkbox::inline() puts the input in the label (labelPrefix) so it
            // renders as a real checkbox+text pair — without it Filament shows the
            // label as a standalone field heading and the box sits in a broken content col.
            Checkbox::make('set_up_compulsory')
                ->label('Set as compulsory')
                ->inline()
                ->extraFieldWrapperAttributes(['class' => 'sl-code-dc-compulsory']),
            Flex::make([
                Checkbox::make('data_collection_mobile')
                    ->label('Mobile')
                    ->inline()
                    ->grow(false),
                Checkbox::make('data_collection_email')
                    ->label('Email')
                    ->inline()
                    ->grow(false),
                Checkbox::make('data_collection_name')
                    ->label('Name')
                    ->inline()
                    ->grow(false),
                Checkbox::make('data_collection_surname')
                    ->label('Surname')
                    ->inline()
                    ->grow(false),
            ])
                ->from('default')
                ->extraAttributes(['class' => 'sl-code-dc-fields']),
            Textarea::make('description')
                ->label('pop up message:')
                ->placeholder('Enter pop up message:')
                ->rows(2)
                ->maxLength(150)
                ->extraAttributes(['class' => 'sl-code-popup-message'])
                ->columnSpanFull(),
            Grid::make(['default' => 2])
                ->schema([
                    ColorPicker::make('color_code')
                        ->label('Colour Selector')
                        ->default('#000000')
                        ->extraAttributes(['class' => 'sl-code-colour-picker'])
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
     * Contacts: modal add/edit + list UI. Same relationship / save path as before.
     *
     * @return array<int, mixed>
     */
    private static function contactsFields(Get $get): array
    {
        if (! in_array(self::slug($get('type_id')), [
            'location', 'plant', 'product', 'procedure',
        ], true)) {
            return [];
        }

        $contactForm = [
            TextInput::make('name_company')
                ->label('Contact')
                ->placeholder('Name or company')
                ->required()
                ->maxLength(255)
                ->autocomplete(false),
            TextInput::make('telephone')
                ->label('Telephone')
                ->placeholder('Phone number')
                ->tel()
                ->maxLength(50)
                ->autocomplete(false),
        ];

        return [
            Placeholder::make('contacts_section_label')
                ->hiddenLabel()
                ->content(new HtmlString(
                    '<div class="sl-contacts-title">Contacts</div>'
                    .'<div class="sl-contacts-subtitle">Add people or companies associated with this code.</div>'
                ))
                ->columnSpanFull(),
            Repeater::make('contacts')
                ->relationship()
                ->hiddenLabel()
                ->schema([
                    // Values are managed via modal actions; hidden inputs keep relationship sync.
                    Hidden::make('name_company'),
                    Hidden::make('telephone'),
                ])
                ->defaultItems(0)
                ->reorderable(false)
                ->cloneable(false)
                ->itemLabel(function (array $state): string {
                    $name = trim((string) ($state['name_company'] ?? ''));
                    $phone = trim((string) ($state['telephone'] ?? ''));

                    if ($name !== '' && $phone !== '') {
                        return $name.' · '.$phone;
                    }

                    return $name !== '' ? $name : ($phone !== '' ? $phone : 'Contact');
                })
                ->extraAttributes(['class' => 'sl-contacts-repeater'])
                ->addAction(function (Action $action) use ($contactForm): Action {
                    return $action
                        ->label(function (Repeater $component): string {
                            $count = count($component->getRawState() ?? []);

                            return $count > 0 ? 'Add more' : 'Add contact';
                        })
                        ->icon('heroicon-m-plus')
                        ->color('primary')
                        ->button()
                        ->modalHeading(fn (Repeater $component): string => count($component->getRawState() ?? []) > 0
                            ? 'Add another contact'
                            : 'Add contact')
                        ->modalDescription('Enter the contact name and telephone number.')
                        ->modalSubmitActionLabel('Save contact')
                        ->modalCancelActionLabel('Cancel')
                        ->modalWidth('md')
                        ->form($contactForm)
                        ->action(function (array $data, Repeater $component): void {
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
                        });
                })
                ->extraItemActions([
                    Action::make('editContact')
                        ->label('Edit')
                        ->tooltip('Edit contact')
                        ->icon('heroicon-m-pencil-square')
                        ->color('primary')
                        ->iconButton()
                        ->modalHeading('Edit contact')
                        ->modalDescription('Update the contact details.')
                        ->modalSubmitActionLabel('Save changes')
                        ->modalCancelActionLabel('Cancel')
                        ->modalWidth('md')
                        ->fillForm(function (array $arguments, Repeater $component): array {
                            $item = $component->getRawState()[$arguments['item'] ?? ''] ?? [];

                            return [
                                'name_company' => (string) ($item['name_company'] ?? ''),
                                'telephone' => (string) ($item['telephone'] ?? ''),
                            ];
                        })
                        ->form($contactForm)
                        ->action(function (array $data, array $arguments, Repeater $component): void {
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
                        }),
                ])
                ->deleteAction(function (Action $action): Action {
                    return $action
                        ->label('Delete')
                        ->tooltip('Delete contact')
                        ->icon('heroicon-m-trash')
                        ->color('danger')
                        ->iconButton()
                        ->requiresConfirmation()
                        ->modalHeading('Delete contact?')
                        ->modalDescription('This contact will be removed when you save the profile.')
                        ->modalSubmitActionLabel('Delete');
                })
                ->columnSpanFull(),
        ];
    }

    /**
     * Additional User Access Login (voc_users): plant-style modal add/edit + list with
     * per-row Edit/Delete. Email + password per login; password persisted only when set.
     */
    private static function vocUsersField(): Repeater
    {
        // Add: password required. Edit: password optional — the stored password is not
        // loaded back into the form (VocUser hides it), so "leave blank to keep" lets the
        // email be edited without forcing a password re-entry. The hidden field's
        // dehydrate-if-filled rule keeps the existing password when this is left blank.
        $addForm = [
            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->maxLength(255)
                ->autocomplete(false),
            TextInput::make('password')
                ->label('Password')
                ->password()
                ->revealable()
                ->required()
                ->minLength(6)
                ->maxLength(255)
                ->autocomplete('new-password'),
        ];

        // Edit: password validation-free (leaving it blank keeps the current one). Any
        // min-length / required rule here would fail the modal submit when the field is
        // blank and silently keep the dialog open. Blank is handled in the edit action.
        $editForm = [
            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->maxLength(255)
                ->autocomplete(false),
            TextInput::make('password')
                ->label('Password')
                ->password()
                ->revealable()
                ->maxLength(255)
                ->autocomplete('new-password')
                ->helperText('Leave blank to keep the current password.'),
        ];

        return Repeater::make('vocUsers')
            ->relationship()
            ->hiddenLabel()
            ->schema([
                Hidden::make('email'),
                Hidden::make('password')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->dehydrateStateUsing(fn (?string $state): string => (string) ($state ?? '')),
            ])
            ->defaultItems(0)
            ->reorderable(false)
            ->cloneable(false)
            ->itemLabel(fn (array $state): string => trim((string) ($state['email'] ?? '')) ?: 'User')
            ->extraAttributes(['class' => 'sl-contacts-repeater sl-green-add-repeater'])
            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                $data['voc_user_id'] = self::nextLegacyId('voc_users', 'voc_user_id');

                return $data;
            })
            ->addAction(fn (Action $action): Action => $action
                ->label(fn (Repeater $component): string => count($component->getRawState() ?? []) > 0 ? 'Add Another' : 'Add')
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->button()
                ->modalHeading(fn (Repeater $component): string => count($component->getRawState() ?? []) > 0 ? 'Add another user' : 'Add user')
                ->modalDescription('Enter the login email and password for this additional user.')
                ->modalSubmitActionLabel('Save user')
                ->modalCancelActionLabel('Cancel')
                ->modalWidth('md')
                ->form($addForm)
                ->action(fn (array $data, Repeater $component) => self::appendRepeaterItem($component, $data)))
            ->extraItemActions([
                Action::make('editVocUser')
                    ->label('Edit')
                    ->tooltip('Edit user')
                    ->icon('heroicon-m-pencil-square')
                    ->color('primary')
                    ->iconButton()
                    ->modalHeading('Edit user')
                    ->modalDescription('Update the login email and password.')
                    ->modalSubmitActionLabel('Save changes')
                    ->modalCancelActionLabel('Cancel')
                    ->modalWidth('md')
                    ->fillForm(function (array $arguments, Repeater $component): array {
                        $item = $component->getRawState()[$arguments['item'] ?? ''] ?? [];

                        return [
                            'email' => (string) ($item['email'] ?? ''),
                            'password' => '',
                        ];
                    })
                    ->form($editForm)
                    ->action(function (array $data, array $arguments, Repeater $component): void {
                        // Blank password on edit = keep the existing one.
                        if (blank($data['password'] ?? null)) {
                            unset($data['password']);
                        }

                        self::updateRepeaterItem($component, $arguments, $data);
                    }),
            ])
            ->deleteAction(fn (Action $action): Action => $action
                ->label('Delete')
                ->tooltip('Delete user')
                ->icon('heroicon-m-trash')
                ->color('danger')
                ->iconButton()
                ->requiresConfirmation()
                ->modalHeading('Delete user?')
                ->modalDescription('This login will be removed when you save the profile.')
                ->modalSubmitActionLabel('Delete'))
            ->columnSpanFull();
    }

    /**
     * Email Notification recipients (voc_recipients): plant-style modal add/edit + list.
     */
    private static function vocRecipientsField(): Repeater
    {
        $recipientForm = [
            TextInput::make('email')
                ->label('Recipient Email')
                ->email()
                ->required()
                ->maxLength(255)
                ->autocomplete(false),
        ];

        return Repeater::make('vocRecipients')
            ->relationship()
            ->hiddenLabel()
            ->schema([
                Hidden::make('email'),
            ])
            ->defaultItems(0)
            // Legacy: at least one notification recipient is required.
            ->minItems(1)
            ->validationMessages(['min' => 'Enter at least one recipient email.'])
            ->reorderable(false)
            ->cloneable(false)
            ->itemLabel(fn (array $state): string => trim((string) ($state['email'] ?? '')) ?: 'Recipient')
            ->extraAttributes(['class' => 'sl-contacts-repeater sl-green-add-repeater'])
            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                $data['voc_recipient_id'] = self::nextLegacyId('voc_recipients', 'voc_recipient_id');

                return $data;
            })
            ->addAction(fn (Action $action): Action => $action
                ->label(fn (Repeater $component): string => count($component->getRawState() ?? []) > 0 ? 'Add Another' : 'Add')
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->button()
                ->modalHeading(fn (Repeater $component): string => count($component->getRawState() ?? []) > 0 ? 'Add another recipient' : 'Add recipient')
                ->modalDescription('Enter the email address to notify about document expiry.')
                ->modalSubmitActionLabel('Save recipient')
                ->modalCancelActionLabel('Cancel')
                ->modalWidth('md')
                ->form($recipientForm)
                ->action(fn (array $data, Repeater $component) => self::appendRepeaterItem($component, $data)))
            ->extraItemActions([
                Action::make('editVocRecipient')
                    ->label('Edit')
                    ->tooltip('Edit recipient')
                    ->icon('heroicon-m-pencil-square')
                    ->color('primary')
                    ->iconButton()
                    ->modalHeading('Edit recipient')
                    ->modalDescription('Update the recipient email address.')
                    ->modalSubmitActionLabel('Save changes')
                    ->modalCancelActionLabel('Cancel')
                    ->modalWidth('md')
                    ->fillForm(function (array $arguments, Repeater $component): array {
                        $item = $component->getRawState()[$arguments['item'] ?? ''] ?? [];

                        return ['email' => (string) ($item['email'] ?? '')];
                    })
                    ->form($recipientForm)
                    ->action(fn (array $data, array $arguments, Repeater $component) => self::updateRepeaterItem($component, $arguments, $data)),
            ])
            ->deleteAction(fn (Action $action): Action => $action
                ->label('Delete')
                ->tooltip('Delete recipient')
                ->icon('heroicon-m-trash')
                ->color('danger')
                ->iconButton()
                ->requiresConfirmation()
                ->modalHeading('Delete recipient?')
                ->modalDescription('This recipient will be removed when you save the profile.')
                ->modalSubmitActionLabel('Delete'))
            ->columnSpanFull();
    }

    /**
     * Document Upload (voc_documents): plant-style modal add/edit + list. The list title
     * links to the uploaded file and shows the expiry date.
     */
    private static function vocDocumentsField(): Repeater
    {
        $documentForm = [
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

        return Repeater::make('vocDocuments')
            ->relationship()
            ->hiddenLabel()
            ->schema([
                Hidden::make('name'),
                Hidden::make('expiry_date'),
                Hidden::make('file_name'),
            ])
            ->defaultItems(0)
            ->reorderable(false)
            ->cloneable(false)
            ->itemLabel(function (array $state): HtmlString {
                $name = trim((string) ($state['name'] ?? '')) ?: 'Document';
                $expLabel = self::vocExpiryLabel($state['expiry_date'] ?? null);
                $url = self::documentPublicUrl($state['file_name'] ?? null);

                if (filled($url)) {
                    return new HtmlString(
                        '<a class="sl-doc-title-link" href="'.e($url).'" target="_blank" rel="noopener noreferrer" '
                        .'onclick="event.stopPropagation()">'.e($name).'</a>'.e($expLabel)
                    );
                }

                return new HtmlString('<span class="sl-doc-title-text">'.e($name).'</span>'.e($expLabel));
            })
            ->extraAttributes(['class' => 'sl-contacts-repeater sl-documents-repeater sl-green-add-repeater'])
            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                $data['voc_document_id'] = self::nextLegacyId('voc_documents', 'voc_document_id');

                return $data;
            })
            ->addAction(fn (Action $action): Action => $action
                ->label(fn (Repeater $component): string => count($component->getRawState() ?? []) > 0 ? 'Add Another' : 'Add')
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->button()
                ->modalHeading(fn (Repeater $component): string => count($component->getRawState() ?? []) > 0 ? 'Add another document' : 'Upload a Document')
                ->modalDescription('Upload a file, set its name and optional expiry date.')
                ->modalSubmitActionLabel('Save document')
                ->modalCancelActionLabel('Cancel')
                ->modalWidth('lg')
                ->form($documentForm)
                ->action(fn (array $data, Repeater $component) => self::appendRepeaterItem($component, $data)))
            ->extraItemActions([
                Action::make('editVocDocument')
                    ->label('Edit')
                    ->tooltip('Edit document')
                    ->icon('heroicon-m-pencil-square')
                    ->color('primary')
                    ->iconButton()
                    ->modalHeading('Edit document')
                    ->modalDescription('Update the file, name, or expiry date.')
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
                    ->form($documentForm)
                    ->action(fn (array $data, array $arguments, Repeater $component) => self::updateRepeaterItem($component, $arguments, $data)),
            ])
            ->deleteAction(fn (Action $action): Action => $action
                ->label('Delete')
                ->tooltip('Delete document')
                ->icon('heroicon-m-trash')
                ->color('danger')
                ->iconButton()
                ->requiresConfirmation()
                ->modalHeading('Delete document?')
                ->modalDescription('This document will be removed when you save the profile.')
                ->modalSubmitActionLabel('Delete'))
            ->columnSpanFull();
    }

    /** Human "(Expires dd/mm/yyyy)" suffix for a voc document expiry value (skips sentinels). */
    private static function vocExpiryLabel(mixed $expiry): string
    {
        if ($expiry === null || in_array((string) $expiry, ['', '1970-01-01', '0000-00-00'], true)) {
            return '';
        }

        try {
            return ' (Expires '.\Illuminate\Support\Carbon::parse($expiry)->format('d/m/Y').')';
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * Shared modal "add" handler: append a new item to the repeater's raw state.
     *
     * @param  array<string, mixed>  $data
     */
    private static function appendRepeaterItem(Repeater $component, array $data): void
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
        // Re-render only this repeater (fast). The modal still closes via Filament's
        // close-modal dispatch; a full form re-render here is slow (CKEditors re-init).
        $component->partiallyRender();
    }

    /**
     * Shared modal "edit" handler: merge changes into the addressed repeater item.
     *
     * @param  array<string, mixed>  $arguments
     * @param  array<string, mixed>  $data
     */
    private static function updateRepeaterItem(Repeater $component, array $arguments, array $data): void
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

    /**
     * Videos: modal add/edit + list UI. Title in the list opens YouTube in a new tab.
     * Keeps the same form keys (`video_titles` / `video_extra_titles`) synced via ProfileMediaService.
     *
     * @return array<int, mixed>
     */
    private static function videoFields(string $statePath): array
    {
        $normalizeVideo = function (array $data): array {
            $data['title'] = (string) ($data['title'] ?? '');
            $data['video_name'] = trim((string) ($data['video_name'] ?? ''));

            return $data;
        };

        $videoForm = [
            TextInput::make('title')
                ->label('Video title')
                ->placeholder('e.g. Product demo')
                ->maxLength(255)
                ->autocomplete(false),
            TextInput::make('video_name')
                ->label('YouTube URL or video ID')
                ->placeholder('https://www.youtube.com/watch?v=… or video ID')
                ->required()
                ->maxLength(255)
                ->autocomplete(false),
        ];

        return [
            Repeater::make($statePath)
                ->hiddenLabel()
                ->schema([
                    Hidden::make('title'),
                    Hidden::make('video_name'),
                ])
                ->defaultItems(0)
                ->reorderable(false)
                ->cloneable(false)
                ->itemLabel(function (array $state): HtmlString {
                    $title = trim((string) ($state['title'] ?? ''));
                    if ($title === '') {
                        $title = 'Video';
                    }

                    $url = self::youtubeWatchUrl($state['video_name'] ?? null);
                    if (filled($url)) {
                        return new HtmlString(
                            '<a class="sl-video-title-link" href="'.e($url).'" target="_blank" rel="noopener noreferrer" '
                            .'onclick="event.stopPropagation()">'.e($title).'</a>'
                        );
                    }

                    return new HtmlString('<span class="sl-video-title-text">'.e($title).'</span>');
                })
                ->extraAttributes(['class' => 'sl-contacts-repeater sl-videos-repeater'])
                ->addAction(function (Action $action) use ($videoForm, $normalizeVideo): Action {
                    return $action
                        ->label(function (Repeater $component): string {
                            $count = count($component->getRawState() ?? []);

                            return $count > 0 ? 'Add more' : 'Upload Video';
                        })
                        ->icon('heroicon-m-plus')
                        ->color('primary')
                        ->button()
                        ->modalHeading(fn (Repeater $component): string => count($component->getRawState() ?? []) > 0
                            ? 'Add another video'
                            : 'Upload a Video')
                        ->modalDescription('Add a YouTube URL or video ID and the title shown on the mobile page.')
                        ->modalSubmitActionLabel('Save video')
                        ->modalCancelActionLabel('Cancel')
                        ->modalWidth('md')
                        ->form($videoForm)
                        ->action(function (array $data, Repeater $component) use ($normalizeVideo): void {
                            $data = $normalizeVideo($data);
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
                        });
                })
                ->extraItemActions([
                    Action::make('editVideo_'.$statePath)
                        ->label('Edit')
                        ->tooltip('Edit video')
                        ->icon('heroicon-m-pencil-square')
                        ->color('primary')
                        ->iconButton()
                        ->modalHeading('Edit video')
                        ->modalDescription('Update the video title or YouTube URL / ID.')
                        ->modalSubmitActionLabel('Save changes')
                        ->modalCancelActionLabel('Cancel')
                        ->modalWidth('md')
                        ->fillForm(function (array $arguments, Repeater $component): array {
                            $item = $component->getRawState()[$arguments['item'] ?? ''] ?? [];

                            return [
                                'title' => (string) ($item['title'] ?? ''),
                                'video_name' => (string) ($item['video_name'] ?? ''),
                            ];
                        })
                        ->form($videoForm)
                        ->action(function (array $data, array $arguments, Repeater $component) use ($normalizeVideo): void {
                            $itemKey = $arguments['item'] ?? null;
                            if ($itemKey === null) {
                                return;
                            }

                            $data = $normalizeVideo($data);
                            $items = $component->getRawState() ?? [];
                            $items[$itemKey] = array_merge($items[$itemKey] ?? [], $data);
                            $component->rawState($items);
                            $component->getChildSchema($itemKey)->fill($data);
                            $component->callAfterStateUpdated();
                            $component->partiallyRender();
                        }),
                ])
                ->deleteAction(function (Action $action): Action {
                    return $action
                        ->label('Delete')
                        ->tooltip('Delete video')
                        ->icon('heroicon-m-trash')
                        ->color('danger')
                        ->iconButton()
                        ->requiresConfirmation()
                        ->modalHeading('Delete video?')
                        ->modalDescription('This video will be removed when you save the profile.')
                        ->modalSubmitActionLabel('Delete');
                })
                ->columnSpanFull(),
        ];
    }

    private static function youtubeWatchUrl(mixed $videoName): ?string
    {
        $input = trim((string) ($videoName ?? ''));
        if ($input === '') {
            return null;
        }

        $youtube = app(YouTubeService::class);

        if (str_starts_with($input, 'http://') || str_starts_with($input, 'https://')) {
            $id = $youtube->parseVideoId($input);

            return $id ? $youtube->watchUrl($id) : $input;
        }

        $id = $youtube->parseVideoId($input) ?? $input;

        return $youtube->watchUrl($id);
    }

    /**
     * Documents: modal add/edit + list UI. Title in the list opens the file in a new tab.
     * Same relationship / save path as before (including sort_order + media owner stamps).
     *
     * @return array<int, mixed>
     */
    private static function documentFields(Get $get): array
    {
        $typeSlag = self::slug($get('type_id')) ?? request()->query('type');
        $showStyleFields = $typeSlag !== 'people';

        $normalizeDocument = function (array $data) use ($showStyleFields): array {
            $data['name'] = (string) ($data['name'] ?? '');
            $data['doc_name'] = is_array($data['doc_name'] ?? null)
                ? (string) (array_values($data['doc_name'])[0] ?? '')
                : (string) ($data['doc_name'] ?? '');

            if ($showStyleFields) {
                $data['txt_align'] = (string) ($data['txt_align'] ?: 'left');
                $data['btn_color'] = ltrim((string) ($data['btn_color'] ?: '007A01'), '#');
            } else {
                $data['txt_align'] = (string) ($data['txt_align'] ?: 'left');
                $data['btn_color'] = ltrim((string) ($data['btn_color'] ?: ''), '#');
            }

            return $data;
        };

        $documentForm = [
            FileUpload::make('doc_name')
                ->label('Upload a Document: (File type DOC, DOCX, PDF, JPG, GIF, JPEG) (Max file size 10 MB)')
                ->directory('profiles/documents')
                ->disk('public')
                ->acceptedFileTypes([
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'image/jpeg',
                    'image/gif',
                    'image/png',
                ])
                ->maxSize(10240)
                ->required()
                ->downloadable()
                ->openable(),
            TextInput::make('name')
                ->label('Title')
                ->placeholder('Document title')
                ->maxLength(255)
                ->autocomplete(false),
            Radio::make('txt_align')
                ->label('Text Alignment')
                ->options([
                    'left' => 'Left',
                    'center' => 'Center',
                    'right' => 'Right',
                ])
                ->default('left')
                ->inline()
                ->visible($showStyleFields),
            ColorPicker::make('btn_color')
                ->label('Button Color')
                ->default('#007A01')
                ->visible($showStyleFields)
                ->formatStateUsing(function ($state): string {
                    $value = trim((string) ($state ?: '007A01'));

                    return str_starts_with($value, '#') ? $value : '#'.$value;
                })
                ->dehydrateStateUsing(fn ($state): string => ltrim((string) ($state ?: '007A01'), '#')),
        ];

        return [
            Repeater::make('documents')
                ->relationship()
                ->hiddenLabel()
                ->schema([
                    Hidden::make('doc_name'),
                    Hidden::make('name'),
                    Hidden::make('txt_align'),
                    Hidden::make('btn_color'),
                    Hidden::make('sort_order'),
                ])
                ->defaultItems(0)
                ->reorderable()
                ->orderColumn('sort_order')
                ->cloneable(false)
                ->itemLabel(function (array $state): HtmlString {
                    $title = trim((string) ($state['name'] ?? ''));
                    if ($title === '') {
                        $title = 'Document';
                    }

                    $url = self::documentPublicUrl($state['doc_name'] ?? null);
                    if (filled($url)) {
                        return new HtmlString(
                            '<a class="sl-doc-title-link" href="'.e($url).'" target="_blank" rel="noopener noreferrer" '
                            .'onclick="event.stopPropagation()">'.e($title).'</a>'
                        );
                    }

                    return new HtmlString('<span class="sl-doc-title-text">'.e($title).'</span>');
                })
                ->extraAttributes(['class' => 'sl-contacts-repeater sl-documents-repeater'])
                ->mutateRelationshipDataBeforeCreateUsing(fn (array $data, Get $get): array => self::stampMediaOwner($data, $get))
                ->mutateRelationshipDataBeforeSaveUsing(fn (array $data, Get $get): array => self::stampMediaOwner($data, $get))
                ->addAction(function (Action $action) use ($documentForm, $normalizeDocument): Action {
                    return $action
                        ->label(function (Repeater $component): string {
                            $count = count($component->getRawState() ?? []);

                            return $count > 0 ? 'Add more' : 'Upload a Document';
                        })
                        ->icon('heroicon-m-plus')
                        ->color('primary')
                        ->button()
                        ->modalHeading(fn (Repeater $component): string => count($component->getRawState() ?? []) > 0
                            ? 'Add another document'
                            : 'Upload a Document')
                        ->modalDescription('Upload a file and set the title shown on the mobile page.')
                        ->modalSubmitActionLabel('Save document')
                        ->modalCancelActionLabel('Cancel')
                        ->modalWidth('lg')
                        ->form($documentForm)
                        ->action(function (array $data, Repeater $component) use ($normalizeDocument): void {
                            $data = $normalizeDocument($data);
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
                        });
                })
                ->extraItemActions([
                    Action::make('editDocument')
                        ->label('Edit')
                        ->tooltip('Edit document')
                        ->icon('heroicon-m-pencil-square')
                        ->color('primary')
                        ->iconButton()
                        ->modalHeading('Edit document')
                        ->modalDescription('Update the file, title, or button styling.')
                        ->modalSubmitActionLabel('Save changes')
                        ->modalCancelActionLabel('Cancel')
                        ->modalWidth('lg')
                        ->fillForm(function (array $arguments, Repeater $component): array {
                            $item = $component->getRawState()[$arguments['item'] ?? ''] ?? [];
                            $color = ltrim((string) ($item['btn_color'] ?? '007A01'), '#');

                            return [
                                'doc_name' => $item['doc_name'] ?? null,
                                'name' => (string) ($item['name'] ?? ''),
                                'txt_align' => (string) ($item['txt_align'] ?: 'left'),
                                'btn_color' => $color !== '' ? '#'.$color : '#007A01',
                            ];
                        })
                        ->form($documentForm)
                        ->action(function (array $data, array $arguments, Repeater $component) use ($normalizeDocument): void {
                            $itemKey = $arguments['item'] ?? null;
                            if ($itemKey === null) {
                                return;
                            }

                            $data = $normalizeDocument($data);
                            $items = $component->getRawState() ?? [];
                            $items[$itemKey] = array_merge($items[$itemKey] ?? [], $data);
                            $component->rawState($items);
                            $component->getChildSchema($itemKey)->fill($data);
                            $component->callAfterStateUpdated();
                            $component->partiallyRender();
                        }),
                ])
                ->deleteAction(function (Action $action): Action {
                    return $action
                        ->label('Delete')
                        ->tooltip('Delete document')
                        ->icon('heroicon-m-trash')
                        ->color('danger')
                        ->iconButton()
                        ->requiresConfirmation()
                        ->modalHeading('Delete document?')
                        ->modalDescription('This document will be removed when you save the profile.')
                        ->modalSubmitActionLabel('Delete');
                })
                ->columnSpanFull(),
        ];
    }

    private static function documentPublicUrl(mixed $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (is_array($path)) {
            $path = array_values($path)[0] ?? null;
        }

        $path = trim((string) $path);
        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $diskPath = ltrim(str_replace(['storage/', 'public/'], '', $path), '/');

        return $diskPath !== '' ? asset('storage/'.$diskPath) : null;
    }

    /**
     * Web links: modal add/edit + list UI. Same relationship / save path as before.
     *
     * @return array<int, mixed>
     */
    private static function webLinkFields(): array
    {
        $refreshPreview = function ($livewire): void {
            if (method_exists($livewire, 'pushPhonePreviewDraft')) {
                $livewire->pushPhonePreviewDraft();
            }
        };

        $normalizeWeblink = function (array $data): array {
            $data['link_button'] = in_array($data['link_button'] ?? false, [true, 1, '1'], true);
            $data['link_button_text'] = (string) ($data['link_button_text'] ?? '');
            $data['link_button_url'] = (string) ($data['link_button_url'] ?? '');
            $data['link_button_align'] = (string) ($data['link_button_align'] ?: 'left');
            $data['link_button_color'] = ltrim((string) ($data['link_button_color'] ?: '007A01'), '#');

            return $data;
        };

        $weblinkForm = [
            Checkbox::make('link_button')
                ->label('Add Button')
                ->default(true),
            TextInput::make('link_button_text')
                ->label('Button Text')
                ->placeholder('e.g. Click me')
                ->maxLength(255)
                ->autocomplete(false),
            TextInput::make('link_button_url')
                ->label('Button Link URL')
                ->helperText('Start with http:// or https://')
                ->placeholder('https://')
                ->url()
                ->maxLength(255)
                ->autocomplete(false),
            Radio::make('link_button_align')
                ->label('Button Text Alignment')
                ->options([
                    'left' => 'Left',
                    'center' => 'Center',
                    'right' => 'Right',
                ])
                ->default('left')
                ->inline(),
            ColorPicker::make('link_button_color')
                ->label('Button color')
                ->default('#007A01')
                ->formatStateUsing(function ($state): string {
                    $value = trim((string) ($state ?: '007A01'));

                    return str_starts_with($value, '#') ? $value : '#'.$value;
                })
                ->dehydrateStateUsing(fn ($state): string => ltrim((string) ($state ?: '007A01'), '#')),
        ];

        return [
            Repeater::make('weblinks')
                ->relationship()
                ->hiddenLabel()
                ->schema([
                    // Values are managed via modal actions; hidden inputs keep relationship sync.
                    Hidden::make('link_button'),
                    Hidden::make('link_button_text'),
                    Hidden::make('link_button_url'),
                    Hidden::make('link_button_align'),
                    Hidden::make('link_button_color'),
                ])
                ->defaultItems(0)
                ->reorderable(false)
                ->cloneable(false)
                ->itemLabel(function (array $state): string {
                    $text = trim((string) ($state['link_button_text'] ?? ''));
                    $url = trim((string) ($state['link_button_url'] ?? ''));
                    $enabled = in_array($state['link_button'] ?? false, [true, 1, '1'], true);

                    if ($text !== '' && $url !== '') {
                        return ($enabled ? '' : '(off) ').$text.' · '.$url;
                    }

                    if ($text !== '') {
                        return ($enabled ? '' : '(off) ').$text;
                    }

                    if ($url !== '') {
                        return ($enabled ? '' : '(off) ').$url;
                    }

                    return 'Web link';
                })
                ->extraAttributes(['class' => 'sl-contacts-repeater sl-weblinks-repeater'])
                ->addAction(function (Action $action) use ($weblinkForm, $normalizeWeblink, $refreshPreview): Action {
                    return $action
                        ->label(function (Repeater $component): string {
                            $count = count($component->getRawState() ?? []);

                            return $count > 0 ? 'Add another link' : 'Add web link';
                        })
                        ->icon('heroicon-m-plus')
                        ->color('primary')
                        ->button()
                        ->modalHeading(fn (Repeater $component): string => count($component->getRawState() ?? []) > 0
                            ? 'Add another web link'
                            : 'Add web link')
                        ->modalDescription('Configure the button text, URL, alignment, and colour.')
                        ->modalSubmitActionLabel('Save link')
                        ->modalCancelActionLabel('Cancel')
                        ->modalWidth('lg')
                        ->form($weblinkForm)
                        ->action(function (array $data, Repeater $component) use ($normalizeWeblink, $refreshPreview): void {
                            $data = $normalizeWeblink($data);
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
                            $refreshPreview($component->getLivewire());
                        });
                })
                ->extraItemActions([
                    Action::make('editWeblink')
                        ->label('Edit')
                        ->tooltip('Edit web link')
                        ->icon('heroicon-m-pencil-square')
                        ->color('primary')
                        ->iconButton()
                        ->modalHeading('Edit web link')
                        ->modalDescription('Update the button details.')
                        ->modalSubmitActionLabel('Save changes')
                        ->modalCancelActionLabel('Cancel')
                        ->modalWidth('lg')
                        ->fillForm(function (array $arguments, Repeater $component): array {
                            $item = $component->getRawState()[$arguments['item'] ?? ''] ?? [];
                            $color = ltrim((string) ($item['link_button_color'] ?? '007A01'), '#');

                            return [
                                'link_button' => in_array($item['link_button'] ?? false, [true, 1, '1'], true),
                                'link_button_text' => (string) ($item['link_button_text'] ?? ''),
                                'link_button_url' => (string) ($item['link_button_url'] ?? ''),
                                'link_button_align' => (string) ($item['link_button_align'] ?: 'left'),
                                'link_button_color' => '#'.$color,
                            ];
                        })
                        ->form($weblinkForm)
                        ->action(function (array $data, array $arguments, Repeater $component) use ($normalizeWeblink, $refreshPreview): void {
                            $itemKey = $arguments['item'] ?? null;
                            if ($itemKey === null) {
                                return;
                            }

                            $data = $normalizeWeblink($data);
                            $items = $component->getRawState() ?? [];
                            $items[$itemKey] = array_merge($items[$itemKey] ?? [], $data);
                            $component->rawState($items);
                            $component->getChildSchema($itemKey)->fill($data);
                            $component->callAfterStateUpdated();
                            $component->partiallyRender();
                            $refreshPreview($component->getLivewire());
                        }),
                ])
                ->deleteAction(function (Action $action) use ($refreshPreview): Action {
                    return $action
                        ->label('Delete')
                        ->tooltip('Delete web link')
                        ->icon('heroicon-m-trash')
                        ->color('danger')
                        ->iconButton()
                        ->requiresConfirmation()
                        ->modalHeading('Delete web link?')
                        ->modalDescription('This web link will be removed when you save the profile.')
                        ->modalSubmitActionLabel('Delete')
                        ->action(function (array $arguments, Repeater $component) use ($refreshPreview): void {
                            $items = $component->getRawState() ?? [];
                            unset($items[$arguments['item'] ?? '']);
                            $component->rawState($items);
                            $component->callAfterStateUpdated();
                            $component->partiallyRender();
                            $refreshPreview($component->getLivewire());
                        });
                })
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
