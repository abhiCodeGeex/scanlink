<?php

namespace App\Filament\Resources\Profiles\Schemas;

use App\Enums\ProfileCodeType;
use App\Models\EquipmentType;
use App\Services\ProfileQrService;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\HtmlString;

class ProfileFormSchema
{
    /**
     * @return array<int, mixed>
     */
    public static function components(): array
    {
        return [
            Section::make('Profile type & owner')
                ->columns(2)
                ->schema([
                    Select::make('type_id')
                        ->label('Select Profile Type')
                        // Legacy product/add excludes people (id 6); CustomQR is included so admins can create with live preview.
                        ->relationship('equipmentType', 'name', fn ($query) => $query->where('slag', '!=', 'people'))
                        ->searchable()
                        ->required()
                        ->live()
                        ->disabled(fn (string $operation): bool => $operation === 'edit'),
                    Select::make('client_id')
                        ->label('Select Client')
                        ->relationship('client', 'client_name')
                        ->searchable()
                        ->required()
                        ->live()
                        ->disabled(fn (string $operation): bool => $operation === 'edit'),
                    Select::make('user_id')
                        ->label('Profile owner')
                        ->options(fn (Get $get): array => \App\Models\ClientUser::query()
                            ->where('client_id', $get('client_id'))
                            ->pluck('email', 'id')
                            ->all())
                        ->searchable()
                        ->visible(fn (Get $get): bool => filled($get('client_id'))),
                ]),
            Section::make('Profile details')
                ->columns(2)
                ->schema(fn (Get $get): array => self::typeFields($get('type_id'))),
            Section::make('Data collection')
                ->columns(2)
                ->schema([
                    Toggle::make('enable_data_collection')
                        ->label('Enable data collection'),
                    Toggle::make('set_up_compulsory')
                        ->label('Set as compulsory'),
                    TextInput::make('data_collection_mobile')->label('Mobile'),
                    TextInput::make('data_collection_email')->label('Email')->email(),
                    TextInput::make('data_collection_name')->label('Name'),
                    Textarea::make('data_collection_content')->label('Content')->columnSpanFull(),
                ]),
            Section::make('Code & security')
                ->columns(2)
                ->schema([
                    Select::make('code_type')
                        ->label('Code Type')
                        ->options(collect(ProfileCodeType::cases())->mapWithKeys(fn (ProfileCodeType $t) => [$t->value => $t->label()])),
                    Toggle::make('show_header')->label('Show header in mobile'),
                    self::protectToggle(),
                    self::passwordField(),
                    Toggle::make('display_share_link')->label('Display share links'),
                ]),
            Section::make('Media')
                ->schema([
                    FileUpload::make('logo_upload')
                        ->label('Upload Company logo')
                        ->image()
                        ->imagePreviewHeight('120')
                        ->directory('profiles/logos')
                        ->disk('public'),
                    FileUpload::make('picture_uploads')
                        ->label('Upload pictures')
                        ->image()
                        ->imagePreviewHeight('120')
                        ->multiple()
                        ->directory('profiles/pictures')
                        ->disk('public'),
                    FileUpload::make('document_uploads')
                        ->label('Upload documents')
                        ->multiple()
                        ->directory('profiles/documents')
                        ->disk('public'),
                    Repeater::make('video_titles')
                        ->label('Videos')
                        ->schema([
                            TextInput::make('title')->label('Video title'),
                            TextInput::make('video_name')
                                ->label('YouTube URL or video ID')
                                ->helperText('Paste a watch URL or 11-character YouTube ID.'),
                        ])
                        ->columnSpanFull(),
                ]),
            Section::make('Web links')
                ->schema([
                    Repeater::make('weblinks')
                        ->relationship()
                        ->schema([
                            TextInput::make('link_button_text')->label('Button text'),
                            TextInput::make('link_button_url')->label('URL')->url(),
                            TextInput::make('link_button_color')->label('Color'),
                        ])
                        ->columns(3)
                        ->columnSpanFull(),
                ]),
            Section::make('Contacts')
                ->schema([
                    Repeater::make('contacts')
                        ->relationship()
                        ->schema([
                            TextInput::make('name_company')->label('Contact Name/Company'),
                            TextInput::make('telephone')->label('Telephone'),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ]),
            Section::make('Checklist items')
                ->visible(fn (Get $get): bool => self::slugFor($get('type_id')) === 'plant')
                ->schema([
                    Repeater::make('checklistItems')
                        ->relationship()
                        ->schema([
                            Textarea::make('checklist_item')->label('Item')->required(),
                        ])
                        ->columnSpanFull(),
                ]),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    public static function typeFields(?int $typeId): array
    {
        $slug = self::slugFor($typeId);

        return match ($slug) {
            'location' => [
                // Legacy: Location name (txtname) is NOT required — only code_profile_name is.
                TextInput::make('name')->label('Location name:'),
                TextInput::make('address')
                    ->label('Address:')
                    ->columnSpanFull()
                    ->live(debounce: 400),
                // Legacy: View Map only appears after an address is entered.
                Placeholder::make('view_map_link')
                    ->label('')
                    ->visible(fn (Get $get): bool => trim((string) $get('address')) !== '')
                    ->content(function (Get $get): HtmlString {
                        $address = trim((string) $get('address'));
                        $href = 'https://maps.google.com?q='.rawurlencode($address);

                        return new HtmlString(
                            '<a href="'.e($href).'" target="_blank" rel="noopener" class="view-map text-sm font-semibold text-[#008901]">View Map</a>'
                        );
                    }),
                Textarea::make('description')->label('Description:')->columnSpanFull(),
                Textarea::make('notes')->label('Notes:')->columnSpanFull(),
            ],
            'plant' => [
                // Legacy: only code_profile_name is required — Make/Model is not.
                TextInput::make('name')->label('Make / Model:'),
                // Legacy quirk parity: plant/index.php (create) labels this "ID:",
                // plant/edit.php (edit) labels it "Identification:".
                TextInput::make('identification')
                    ->label(fn (string $operation): string => $operation === 'create' ? 'ID:' : 'Identification:'),
                TextInput::make('serial_no')->label('Serial No.:'),
                Textarea::make('description')->label('Description:')->columnSpanFull(),
                Textarea::make('notes')->label('Note:')->columnSpanFull(),
            ],
            'asset' => [
                // Legacy asset/edit.php Words: checkbox = show heading on mobile.
                Placeholder::make('asset_show_heading_hint')
                    ->hiddenLabel()
                    ->content(new HtmlString(
                        '<div class="cb-main-right" style="color:#555;font-size:12px;margin-bottom:.35rem;">Tick a box to display the heading on mobile</div>'
                    ))
                    ->columnSpanFull(),
                Checkbox::make('show_name')->label('Name:')->inline()->default(false),
                TextInput::make('name')->hiddenLabel()->maxLength(255),
                Checkbox::make('show_description')->label('Description:')->inline()->default(false),
                // Rich text via Filament's native RichEditor (Livewire-first: reliable sync + instant load).
                RichEditor::make('description')
                    ->hiddenLabel()
                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link', 'undo', 'redo'])
                    ->columnSpanFull(),
                Checkbox::make('show_address')->label('Address:')->inline()->default(false),
                TextInput::make('address')
                    ->hiddenLabel()
                    ->columnSpanFull()
                    ->live(debounce: 400),
                Placeholder::make('asset_view_map_link')
                    ->hiddenLabel()
                    ->visible(fn (Get $get): bool => trim((string) $get('address')) !== '')
                    ->content(function (Get $get): HtmlString {
                        $address = trim((string) $get('address'));
                        $href = 'https://maps.google.com?q='.rawurlencode($address);

                        return new HtmlString(
                            '<a href="'.e($href).'" target="_blank" rel="noopener" class="view-map text-sm font-semibold text-[#008901]">View Map</a>'
                        );
                    }),
                Checkbox::make('show_telephone')->label('Telephone:')->inline()->default(false),
                TextInput::make('telephone')->hiddenLabel()->tel(),
                Checkbox::make('show_mobile')->label('Mobile:')->inline()->default(false),
                TextInput::make('mobile')->hiddenLabel()->tel(),
                Checkbox::make('show_email')->label('Email:')->inline()->default(false),
                TextInput::make('email')->hiddenLabel()->email(),
                Checkbox::make('show_url')->label('Website:')->inline()->default(false),
                TextInput::make('url')->hiddenLabel()->url()->placeholder('https://'),
            ],
            'product' => [
                // Legacy product/edit.php — no Identification/Serial in Words.
                // Legacy: only code_profile_name is required — Product name is not.
                TextInput::make('name')->label('Product name:'),
                Textarea::make('description')->label('Description:')->columnSpanFull(),
                Textarea::make('notes')->label('Notes:')->columnSpanFull(),
            ],
            'procedure' => [
                // Legacy: only code_profile_name is required — Title is not.
                TextInput::make('name')->label('Title:'),
                Textarea::make('description')->label('Description:')->columnSpanFull(),
                Textarea::make('notes')->label('Notes:')->columnSpanFull(),
            ],
            'misc' => [
                // Legacy misc/index.php Words: Name + unlabeled description (CKEditor MyToolbar).
                // Legacy: only code_profile_name is required — Name is not.
                TextInput::make('name')->label('Name:'),
                RichEditor::make('description')
                    ->hiddenLabel()
                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link', 'undo', 'redo'])
                    ->columnSpanFull(),
            ],
            'exhibit' => [
                // Legacy exhibit Words tile — unlabeled name + description (rich text on live).
                // Legacy: only code_profile_name is required — name is not.
                TextInput::make('name')->hiddenLabel(),
                RichEditor::make('description')
                    ->hiddenLabel()
                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link', 'undo', 'redo'])
                    ->columnSpanFull(),
            ],
            'voc' => [
                // Legacy voc/edit.php "Profile Information" (not a Words section).
                // Legacy requires First + Last name (submit handler); everything else optional.
                TextInput::make('voc_first_name')->label('First Name')->maxLength(200)->required(),
                TextInput::make('voc_last_name')->label('Last Name')->maxLength(200)->required(),
                TextInput::make('voc_address')->label('Address')->maxLength(200)->columnSpanFull(),
                TextInput::make('voc_town')->label('Town')->maxLength(50),
                TextInput::make('voc_state')->label('State/Territory')->maxLength(50),
                TextInput::make('voc_phone')->label('Telephone No.')->maxLength(30),
                DatePicker::make('voc_dob')
                    ->label('Date of Birth')
                    ->native(false)
                    ->displayFormat('d-m-Y')
                    // Legacy datepicker yearRange 1915:current — no future / implausible DOBs.
                    ->minDate('1915-01-01')
                    ->maxDate(now()),
                Textarea::make('voc_known_allergies')->label('Known Allergies/Medical Conditions')->rows(3)->columnSpanFull(),
                TextInput::make('voc_blood_type')->label('Blood Type')->maxLength(5),
                TextInput::make('voc_next_of_kin')->label('Next of Kin')->maxLength(50),
                TextInput::make('voc_contact_phone')->label('Contact Telephone No.')->maxLength(30),
                TextInput::make('voc_employer')->label('Employer')->maxLength(200)->columnSpanFull(),
                TextInput::make('voc_emp_address')->label('Address')->columnSpanFull(),
                TextInput::make('voc_emp_town')->label('Town')->maxLength(50),
                TextInput::make('voc_emp_state')->label('State/Territory')->maxLength(50),
                TextInput::make('voc_emp_phone')->label('Telephone No.')->maxLength(30),
            ],
            'people' => [
                // Legacy people/index.php: Position ABOVE Name; Name (txtname) is the required
                // profile name; there is NO Description column (people uses `notes`).
                TextInput::make('position')->label('Position:'),
                TextInput::make('name')->label('Name:')->required(fn ($livewire): bool => empty($livewire->bypassRequiredForCheckout ?? null)),
                Textarea::make('notes')->label('Notes:')->columnSpanFull(),
                // Legacy people stores a single Contact + Telephone on the PROFILE row
                // (name_company / telephone), not the profile_contact repeater.
                TextInput::make('name_company')->label('CONTACT:'),
                TextInput::make('telephone')->label('TELEPHONE:'),
                // Legacy people: mobile call-button colours (buttonbackcolor / buttonfontcolor).
                ColorPicker::make('buttonbackcolor')
                    ->label('Mobile button back color:')
                    ->formatStateUsing(fn ($state): ?string => filled($state)
                        ? (str_starts_with((string) $state, '#') ? (string) $state : '#'.$state)
                        : null)
                    ->dehydrateStateUsing(fn ($state): string => ltrim((string) ($state ?? ''), '#')),
                ColorPicker::make('buttonfontcolor')
                    ->label('Mobile button font color:')
                    ->formatStateUsing(fn ($state): ?string => filled($state)
                        ? (str_starts_with((string) $state, '#') ? (string) $state : '#'.$state)
                        : null)
                    ->dehydrateStateUsing(fn ($state): string => ltrim((string) ($state ?? ''), '#')),
            ],
            'customqr' => [
                TextInput::make('name')
                    ->label('Url')
                    ->required()
                    ->url()
                    ->live(debounce: 400),
                Placeholder::make('customqr_preview')
                    ->label('QR preview')
                    ->content(function (Get $get): HtmlString {
                        $url = trim((string) $get('name'));

                        if ($url === '') {
                            return new HtmlString('<span class="text-sm text-gray-500">Enter a URL to preview the QR code.</span>');
                        }

                        $dataUri = app(ProfileQrService::class)->previewDataUri($url);

                        if (! $dataUri) {
                            return new HtmlString('<span class="text-sm text-gray-500">Unable to generate preview.</span>');
                        }

                        return new HtmlString(
                            '<img src="'.e($dataUri).'" alt="CustomQR preview" width="180" height="180" style="image-rendering:pixelated;" />'
                        );
                    }),
            ],
            'code' => [
                // Legacy code/index.php — Application is stored in profiles.name; URL in profiles.url.
                // Portal builds the full form in PortalProfileForm::legacyCodeUrlFields(); keep labels here for parity scripts / admin reuse.
                TextInput::make('name')->label('Application:'),
                TextInput::make('url')
                    ->label('Enter the Web page address(URL) here:')
                    ->default('http://')
                    ->required()
                    ->url(),
                Textarea::make('description')->label('pop up message:')->columnSpanFull(),
                ColorPicker::make('color_code')
                    ->label('Colour Selector')
                    ->default('#000000')
                    ->formatStateUsing(function ($state): string {
                        $value = trim((string) ($state ?: '000000'));

                        return str_starts_with($value, '#') ? $value : '#'.$value;
                    })
                    ->dehydrateStateUsing(fn ($state): string => ltrim((string) ($state ?: '000000'), '#')),
            ],
            default => [
                TextInput::make('name')->label('Name')->required(),
                TextInput::make('code_profile_name')->label('Code profile name'),
                TextInput::make('identification')->label('Identification'),
                Textarea::make('address')->label('Address')->columnSpanFull(),
                Textarea::make('description')->label('Description')->columnSpanFull(),
                Textarea::make('notes')->label('Notes')->columnSpanFull(),
                TextInput::make('shorturl')->label('Short URL'),
                TextInput::make('url')->label('Destination URL'),
                DateTimePicker::make('expired_at')->label('Expiry date'),
                DatePicker::make('activation_start_date'),
                DatePicker::make('activation_end_date'),
                Toggle::make('form_active')->label('Form active'),
                Toggle::make('free_code')->label('Free code'),
            ],
        };
    }

    /** @var array<int, string|null> */
    private static array $slugCache = [];

    public static function protectToggle(): Toggle
    {
        return Toggle::make('protect')
            ->label('Password protect?')
            ->live();
    }

    public static function passwordField(): TextInput
    {
        return TextInput::make('password')
            ->label('Password')
            ->password()
            ->revealable()
            ->visible(fn (Get $get): bool => (bool) $get('protect'))
            ->required(fn (Get $get, string $operation): bool => (bool) $get('protect') && $operation === 'create')
            ->helperText('Leave blank when editing to keep the existing code password.')
            ->dehydrated(fn (?string $state): bool => filled($state))
            ->dehydrateStateUsing(fn (?string $state): string => (string) ($state ?? ''));
    }

    private static function slugFor(?int $typeId): ?string
    {
        if (! $typeId) {
            return null;
        }

        if (array_key_exists($typeId, self::$slugCache)) {
            return self::$slugCache[$typeId];
        }

        return self::$slugCache[$typeId] = EquipmentType::query()->whereKey($typeId)->value('slag');
    }
}
