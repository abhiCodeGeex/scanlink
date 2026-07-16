<?php

namespace App\Filament\Support;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class FormPlaceholderDefaults
{
    public static function register(): void
    {
        TextInput::configureUsing(function (TextInput $component): void {
            $component->placeholder(function (TextInput $component): ?string {
                return static::enterPlaceholder($component->getLabel());
            });
        });

        Textarea::configureUsing(function (Textarea $component): void {
            $component->placeholder(function (Textarea $component): ?string {
                return static::enterPlaceholder($component->getLabel());
            });
        });

        Select::configureUsing(function (Select $component): void {
            // TernaryFilter builds a Select named "value" before its container exists.
            if ($component->getName() === 'value') {
                return;
            }

            $component->placeholder(function (Select $component): ?string {
                return static::selectPlaceholder($component->getLabel());
            });
        });

        DatePicker::configureUsing(function (DatePicker $component): void {
            $component->placeholder(function (DatePicker $component): ?string {
                return static::selectPlaceholder($component->getLabel(), 'Select date');
            });
        });

        DateTimePicker::configureUsing(function (DateTimePicker $component): void {
            $component->placeholder(function (DateTimePicker $component): ?string {
                return static::selectPlaceholder($component->getLabel(), 'Select date and time');
            });
        });
    }

    protected static function enterPlaceholder(mixed $label): ?string
    {
        $label = static::labelText($label);

        return filled($label) ? 'Enter '.$label : null;
    }

    protected static function selectPlaceholder(mixed $label, ?string $fallback = null): ?string
    {
        $label = static::labelText($label);

        if (filled($label)) {
            return 'Select '.$label;
        }

        return $fallback;
    }

    protected static function labelText(mixed $label): ?string
    {
        if (blank($label) || ! is_string($label)) {
            return null;
        }

        $normalized = trim(strip_tags($label));
        $normalized = rtrim($normalized, '*');
        $normalized = trim($normalized);

        return $normalized !== '' ? strtolower($normalized) : null;
    }
}
