<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Models\Client;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Client details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('client_name')
                            ->label('Client Name')
                            ->required()
                            ->maxLength(255),
                        Placeholder::make('reseller_name')
                            ->label('Reseller Name')
                            ->content(fn (?Client $record): string => $record?->resellerName() ?? '')
                            ->visible(fn (string $operation, ?Client $record): bool => $operation === 'edit'
                                && filled($record?->primaryUser?->client_reseller_code)),
                        TextInput::make('address')
                            ->label('Address')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('telephone')
                            ->label('Telephone')
                            ->required()
                            ->tel()
                            ->maxLength(50),
                        TextInput::make('contact_person')
                            ->label('Contact Person')
                            ->required()
                            ->maxLength(255),
                        DatePicker::make('regi_date')
                            ->label('Registration date')
                            ->required()
                            ->default(now()),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->autocomplete('off'),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create'),
                        TextInput::make('url')
                            ->label('URL')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->regex('/^[a-zA-Z0-9_-]+$/')
                            ->prefix(fn (): string => rtrim(config('scanlink.portal_url'), '/').'/')
                            ->helperText('(NOTE : Only characters, numbers, underscore and dash are allowed.)')
                            ->autocomplete('off')
                            ->disabled(fn (string $operation): bool => $operation === 'edit')
                            ->dehydrated(fn (string $operation): bool => $operation === 'create'),
                        // Legacy: Reseller Email only appears on the edit screen.
                        TextInput::make('reseller_email')
                            ->label('Reseller Email')
                            ->email()
                            ->maxLength(255)
                            ->visible(fn (string $operation): bool => $operation === 'edit'),
                        Toggle::make('checklist_option')
                            ->label('Checklist option')
                            ->default(false)
                            ->visible(fn (string $operation): bool => $operation === 'edit')
                            ->dehydrated(false),
                        Toggle::make('customqr_option')
                            ->label('Custom QR option')
                            ->default(false)
                            ->visible(fn (string $operation): bool => $operation === 'edit')
                            ->dehydrated(false),
                    ]),
                Section::make('Add User')
                    ->columns(2)
                    ->visible(fn (string $operation): bool => $operation === 'create')
                    ->schema([
                        TextInput::make('txtUseremail')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->autocomplete('off')
                            ->rules([
                                'nullable',
                                'email',
                                new Unique('client_users', 'email'),
                            ])
                            ->requiredWith('txtUserpassword'),
                        TextInput::make('txtUserpassword')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->requiredWith('txtUseremail'),
                        Toggle::make('videopermission')
                            ->label('Video Upload Permission')
                            ->default(true),
                    ]),
            ]);
    }
}
