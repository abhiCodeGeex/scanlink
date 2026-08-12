<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

/**
 * Central, admin-editable pricing for physical labels and Form Builder activation.
 *
 * Values are stored in the cached key-value `settings` table (edited via the
 * admin "Order Pricing" master) and fall back to the legacy config/constants so
 * behaviour is unchanged until an admin overrides a price.
 */
class PricingSettings
{
    public const KEY_LABEL_SMALL = 'label_price_small';

    public const KEY_LABEL_LARGE = 'label_price_large';

    public const KEY_LABEL_POSTAGE = 'label_postage';

    public const KEY_FORM_BUILDER = 'form_builder_price';

    /** Legacy default postage (dashboard admin order summary). */
    public const DEFAULT_POSTAGE = 2.95;

    /** Legacy Form Builder one-time activation price (AUD). */
    public const DEFAULT_FORM_BUILDER = 5.00;

    public static function labelSmall(): float
    {
        return self::read(self::KEY_LABEL_SMALL, (float) config('scanlink.label_price_small', 3));
    }

    public static function labelLarge(): float
    {
        return self::read(self::KEY_LABEL_LARGE, (float) config('scanlink.label_price_large', 5));
    }

    public static function labelPostage(): float
    {
        return self::read(self::KEY_LABEL_POSTAGE, self::DEFAULT_POSTAGE);
    }

    public static function formBuilder(): float
    {
        return self::read(self::KEY_FORM_BUILDER, self::DEFAULT_FORM_BUILDER);
    }

    /**
     * Convenience for the admin master + display: all managed prices at once.
     *
     * @return array{label_price_small: float, label_price_large: float, label_postage: float, form_builder_price: float}
     */
    public static function all(): array
    {
        return [
            self::KEY_LABEL_SMALL => self::labelSmall(),
            self::KEY_LABEL_LARGE => self::labelLarge(),
            self::KEY_LABEL_POSTAGE => self::labelPostage(),
            self::KEY_FORM_BUILDER => self::formBuilder(),
        ];
    }

    /**
     * Managed pricing keys (used to keep them out of the generic Global Settings editor).
     *
     * @return list<string>
     */
    public static function keys(): array
    {
        return [
            self::KEY_LABEL_SMALL,
            self::KEY_LABEL_LARGE,
            self::KEY_LABEL_POSTAGE,
            self::KEY_FORM_BUILDER,
        ];
    }

    /**
     * Persist a managed price (validated, non-negative). Ignores unknown keys.
     */
    public static function set(string $key, float|string|null $value): void
    {
        if (! in_array($key, self::keys(), true)) {
            return;
        }

        $number = is_numeric($value) ? max(0.0, (float) $value) : 0.0;

        Setting::setValue($key, number_format($number, 2, '.', ''));
    }

    private static function read(string $key, float $default): float
    {
        if (! Schema::hasTable('settings')) {
            return $default;
        }

        $raw = Setting::valueFor($key);

        if ($raw === null || trim($raw) === '' || ! is_numeric($raw)) {
            return $default;
        }

        return max(0.0, (float) $raw);
    }
}
