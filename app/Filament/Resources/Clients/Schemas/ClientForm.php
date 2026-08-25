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
                            ->native(false)
                            ->displayFormat('d-m-Y')
                            ->maxDate(now())
                            ->rule('before_or_equal:today'),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->autocomplete('off')
                            // New clients must not reuse an email that already belongs to an
                            // existing client or login user.
                            ->rules(fn (string $operation): array => $operation === 'create'
                                ? [new Unique('clients', 'email'), new Unique('client_users', 'email')]
                                : [])
                            ->validationMessages(['unique' => 'This email address is already registered.']),
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
                            ->helperText('(NOTE : Only characters, numbers, underscore and dash are allowed.)')
                            ->autocomplete('off')
                            ->disabled(fn (string $operation): bool => $operation === 'edit')
                            ->dehydrated(fn (string $operation): bool => $operation === 'create'),
                        // Legacy: Reseller Email only appears on the edit screen.
                        TextInput::make('reseller_email')
                            ->label('Reseller Email')
                            ->email()
                            ->maxLength(255)
                            // Column is NOT NULL in live dump; empty must stay '' not null.
                            ->dehydrateStateUsing(fn (?string $state): string => filled($state) ? $state : '')
                            ->visible(fn (string $operation): bool => $operation === 'edit'),
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
