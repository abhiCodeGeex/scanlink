<?php

namespace App\Services;

use App\Models\Profile;

class MobileProfileViewResolver
{
    /**
     * Legacy mobile.php type routing.
     */
    public function viewFor(Profile $profile): string
    {
        $slug = $profile->typeSlug();

        return match ($slug) {
            'code', 'customqr' => 'scan.types.code',
            'exhibit' => 'scan.types.exhibit',
            'voc' => 'scan.types.voc',
            'survey' => 'scan.types.survey',
            default => 'scan.types.standard',
        };
    }

    /**
     * Legacy mobile/index.php name heading per equipment type.
     */
    public function nameHeading(Profile $profile): ?string
    {
        if (! filled($profile->name)) {
            return null;
        }

        return match ($profile->typeSlug()) {
            'plant' => 'Make / Model',
            'location' => 'Location name',
            'asset' => 'Name',
            'product' => 'Product name',
            'procedure' => 'Title',
            'misc', 'people' => 'Name',
            'exhibit' => 'Exhibit name',
            'voc' => 'Name',
            'survey' => 'Form',
            default => 'Name',
        };
    }
}
