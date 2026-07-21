<?php

namespace App\Support;

use App\Models\EquipmentType;
use Illuminate\Support\Collection;

/**
 * Legacy mastercode innermenu labels + order (Kohana innermenu.php).
 */
class LegacyEquipmentTypeLabels
{
    /**
     * Visible type tabs in legacy order (people / customqr hidden).
     *
     * @var list<string>
     */
    public const TAB_ORDER = [
        'plant',
        'location',
        'asset',
        'product',
        'procedure',
        'misc',
        'code',
        'survey',
        'exhibit',
        'voc',
    ];

    /**
     * Exact legacy display names.
     *
     * @var array<string, string>
     */
    public const LABELS = [
        'plant' => 'Plant & Equipment',
        'location' => 'Location',
        'asset' => 'Person/Business',
        'product' => 'Product',
        'procedure' => 'Procedure',
        'misc' => 'Misc',
        'code' => 'URL Link',
        'survey' => 'Form/Survey/Checklist',
        'exhibit' => 'Exhibit',
        'voc' => 'VOCC',
        'people' => 'People',
        'customqr' => 'Custom QR',
    ];

    public static function label(string $slag, ?string $fallback = null): string
    {
        return self::LABELS[$slag] ?? ($fallback ?: $slag);
    }

    public static function labelFor(EquipmentType $type): string
    {
        return self::label((string) $type->slag, (string) $type->name);
    }

    public static function sortIndex(string $slag): int
    {
        $index = array_search($slag, self::TAB_ORDER, true);

        return $index === false ? 999 : $index;
    }

    /**
     * @return Collection<int, EquipmentType>
     */
    public static function navTypes(): Collection
    {
        return EquipmentType::query()
            ->whereIn('slag', self::TAB_ORDER)
            ->get()
            ->sortBy(fn (EquipmentType $type): int => self::sortIndex((string) $type->slag))
            ->values();
    }

    /**
     * Ensure DB has all legacy nav types with correct names.
     */
    public static function ensureLegacyTypesExist(): void
    {
        foreach (self::LABELS as $slag => $name) {
            if (in_array($slag, ['people', 'customqr'], true)) {
                EquipmentType::query()->updateOrCreate(
                    ['slag' => $slag],
                    ['name' => $name],
                );

                continue;
            }

            EquipmentType::query()->updateOrCreate(
                ['slag' => $slag],
                ['name' => $name],
            );
        }
    }
}
