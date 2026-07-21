<?php

namespace App\Filament\Resources\Profiles\Schemas;

use App\Enums\ProfileCodeType;
use App\Models\EquipmentType;
use App\Services\ProfileQrService;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
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
                        ->directory('profiles/logos')
                        ->disk('public'),
                    FileUpload::make('picture_uploads')
                        ->label('Upload pictures')
                        ->image()
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
                TextInput::make('name')->label('Location name:')->required(),
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
                TextInput::make('name')->label('Make / Model')->required(),
                TextInput::make('identification')->label('ID'),
                TextInput::make('serial_no')->label('Serial No.'),
                Textarea::make('description')->label('Description')->columnSpanFull(),
                Textarea::make('notes')->label('Note')->columnSpanFull(),
            ],
            'asset' => [
                TextInput::make('name')->label('Name')->required(),
                Textarea::make('description')->label('Description')->columnSpanFull(),
                Textarea::make('address')->label('Address')->columnSpanFull(),
                TextInput::make('telephone')->label('Telephone'),
                TextInput::make('identification')->label('Email / ID'),
            ],
            'product' => [
                TextInput::make('name')->label('Product name')->required(),
                TextInput::make('identification')->label('Identification'),
                TextInput::make('serial_no')->label('Serial No.'),
                Textarea::make('description')->label('Description')->columnSpanFull(),
                Textarea::make('notes')->label('Notes')->columnSpanFull(),
            ],
            'procedure' => [
                TextInput::make('name')->label('Title')->required(),
                Textarea::make('description')->label('Description')->columnSpanFull(),
                Textarea::make('notes')->label('Notes')->columnSpanFull(),
            ],
            'misc' => [
                TextInput::make('name')->label('Name')->required(),
                Textarea::make('description')->label('Description')->columnSpanFull(),
            ],
            'exhibit' => [
                TextInput::make('name')->label('Exhibit name')->required(),
                TextInput::make('code_profile_name')->label('Code profile name'),
                Textarea::make('description')->label('Description')->columnSpanFull(),
                Textarea::make('notes')->label('Notes')->columnSpanFull(),
            ],
            'voc' => [
                TextInput::make('name')->label('Name')->required(),
                TextInput::make('code_profile_name')->label('Code profile name'),
                Textarea::make('description')->label('Profile information')->columnSpanFull(),
                Textarea::make('notes')->label('Notes')->columnSpanFull(),
            ],
            'people' => [
                TextInput::make('identification')->label('Position')->required(),
                TextInput::make('name')->label('Name')->required(),
                Textarea::make('description')->label('Description')->columnSpanFull(),
                Textarea::make('notes')->label('Notes')->columnSpanFull(),
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
                TextInput::make('application')->label('Application')->required(),
                TextInput::make('url')->label('Destination URL')->required()->url(),
                Toggle::make('activate_bridge_graphic')->label('Activate Bridge Graphic'),
                ColorPicker::make('color_code')
                    ->label('Colour Selector')
                    ->default('#000000'),
                Textarea::make('description')->label('Popup message')->columnSpanFull(),
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
