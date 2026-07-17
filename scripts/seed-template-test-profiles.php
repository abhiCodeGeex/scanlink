<?php

/**
 * Seed one active test profile per portal template type.
 * Prefix: TEMPLATE-TEST-{slug} — safe to delete later.
 *
 * Usage: php scripts/seed-template-test-profiles.php [--client=2] [--dry-run]
 */

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Models\ProfileContact;
use App\Models\Weblink;
use App\Services\ProfileDraftSlotService;
use App\Services\ProfileQrService;
use Illuminate\Support\Facades\DB;

$dryRun = in_array('--dry-run', $argv, true);
$clientId = 2;

foreach ($argv as $arg) {
    if (str_starts_with($arg, '--client=')) {
        $clientId = (int) substr($arg, 9);
    }
}

$member = ClientUser::query()
    ->where('client_id', $clientId)
    ->where('status', true)
    ->whereNotNull('auth_user_id')
    ->orderByDesc('role')
    ->first();

if (! $member) {
    fwrite(STDERR, "No active portal member for client {$clientId}\n");
    exit(1);
}

$slugs = [
    'plant', 'location', 'asset', 'product', 'procedure',
    'misc', 'code', 'survey', 'exhibit', 'voc',
];

$types = EquipmentType::query()->whereIn('slag', $slugs)->get()->keyBy('slag');
$stamp = date('Ymd');
$prefix = "TEMPLATE-TEST-{$stamp}";
$expires = now()->addYear();
$drafts = app(ProfileDraftSlotService::class);

$results = [];

DB::beginTransaction();

try {
    foreach ($slugs as $slug) {
        $type = $types->get($slug);

        if (! $type) {
            $results[] = ['slug' => $slug, 'status' => 'SKIP', 'reason' => 'type missing'];
            continue;
        }

        $codeName = "{$prefix}-".strtoupper($slug);

        $existing = Profile::query()
            ->where('client_id', $clientId)
            ->where('code_profile_name', $codeName)
            ->where('deleted', false)
            ->first();

        if ($existing) {
            if (! $dryRun) {
                attachRelations($existing, $slug);
            }

            $results[] = [
                'slug' => $slug,
                'status' => 'EXISTS',
                'id' => $existing->id,
                'edit' => "/portal/profiles/{$existing->id}/edit",
            ];
            continue;
        }

        $data = basePayload($clientId, $member->id, (int) $type->id, $codeName, $expires);
        $data = array_merge($data, typePayload($slug, $codeName));

        if ($dryRun) {
            $results[] = ['slug' => $slug, 'status' => 'DRY-RUN', 'code' => $codeName];
            continue;
        }

        // Prefer legacy empty code slot when available.
        $slot = $drafts->availableSlotForClient($clientId);

        if ($slot) {
            $profile = $slot;
            $profile->forceFill($data);
            $profile->update_or_not = true;
            $profile->save();
        } else {
            $profile = new Profile;
            $profile->forceFill($data);
            $profile->save();
        }

        attachRelations($profile, $slug);

        try {
            app(ProfileQrService::class)->generateFor($profile);
        } catch (Throwable) {
            // best effort
        }

        $results[] = [
            'slug' => $slug,
            'status' => 'CREATED',
            'id' => $profile->id,
            'code' => $codeName,
            'edit' => "/portal/profiles/{$profile->id}/edit",
        ];
    }

    if ($dryRun) {
        DB::rollBack();
    } else {
        DB::commit();
    }
} catch (Throwable $e) {
    DB::rollBack();
    fwrite(STDERR, 'ERROR '.$e->getMessage()."\n".$e->getFile().':'.$e->getLine()."\n");
    exit(1);
}

echo "client_id={$clientId} member={$member->email} prefix={$prefix}\n\n";

foreach ($results as $row) {
    echo implode(' | ', array_map(
        fn ($k, $v) => $k.'='.$v,
        array_keys($row),
        array_values($row),
    ))."\n";
}

echo "\nOpen Master Code List: /portal/profiles\n";
echo "Filter/search for: {$prefix}\n";

/**
 * @return array<string, mixed>
 */
function basePayload(int $clientId, int $userId, int $typeId, string $codeName, $expires): array
{
    return [
        'client_id' => $clientId,
        'user_id' => $userId,
        'type_id' => $typeId,
        'code_profile_name' => $codeName,
        'deleted' => false,
        'update_or_not' => true,
        'expired_at' => $expires,
        'show_header' => true,
        'display_share_link' => true,
        'enable_data_collection' => true,
        'set_up_compulsory' => false,
        'data_collection_name' => true,
        'data_collection_email' => true,
        'data_collection_mobile' => false,
        'data_collection_content' => 'Please complete visitor details.',
        'code_type' => 0,
        'protect' => false,
    ];
}

/**
 * @return array<string, mixed>
 */
function typePayload(string $slug, string $codeName): array
{
    return match ($slug) {
        'location' => [
            'name' => 'Demo Location — '.$codeName,
            'address' => '100 Test Street, Melbourne VIC 3000',
            'url' => 'https://maps.google.com/?q=100+Test+Street+Melbourne',
            'description' => 'Template test location description.',
            'notes' => 'Template test notes.',
        ],
        'plant' => [
            'name' => 'Demo Plant Make/Model',
            'identification' => 'PLT-001',
            'serial_no' => 'SN-PLANT-999',
            'description' => 'Plant equipment template test.',
            'notes' => 'Checklist enabled on plant types.',
        ],
        'asset' => [
            'name' => 'Demo Asset Name',
            'description' => 'Asset template test description.',
            'address' => 'Warehouse Bay 3',
            'telephone' => '0399998888',
            'identification' => 'asset@test.local',
        ],
        'product' => [
            'name' => 'Demo Product',
            'identification' => 'SKU-DEMO-01',
            'serial_no' => 'SN-PROD-001',
            'description' => 'Product template test.',
            'notes' => 'Product notes field.',
        ],
        'procedure' => [
            'name' => 'Demo Procedure Title',
            'description' => 'Procedure steps overview.',
            'notes' => 'Procedure notes.',
        ],
        'misc' => [
            'name' => 'Demo Misc Profile',
            'description' => 'Misc template test content.',
        ],
        'code' => [
            'name' => 'Demo URL Link App',
            'url' => 'https://example.com/template-test',
            'description' => 'Popup message for URL link code.',
        ],
        'survey' => [
            'name' => 'Demo Survey',
            'form_title' => 'Visitor Feedback Survey',
            'form_active' => true,
            'form_is_enable' => true,
            'code_profile_name' => $codeName,
        ],
        'exhibit' => [
            'name' => 'Demo Exhibit',
            'description' => 'Exhibit template test.',
            'notes' => 'Exhibit notes.',
        ],
        'voc' => [
            'name' => 'Demo VOC Profile',
            'description' => 'VOC medical info template test.',
            'notes' => 'VOC notes.',
            'voc_first_name' => 'Test',
            'voc_last_name' => 'User',
            'voc_phone' => '0400000001',
        ],
        default => [
            'name' => 'Demo '.$slug,
            'description' => 'Template test profile.',
        ],
    };
}

function attachRelations(Profile $profile, string $slug): void
{
    if (in_array($slug, ['location', 'plant', 'asset', 'product', 'procedure'], true)) {
        $hasContact = ProfileContact::query()->where('profile_id', $profile->id)->exists();

        if (! $hasContact) {
            ProfileContact::query()->create([
                'profile_id' => $profile->id,
                'name_company' => 'Demo Contact Co',
                'telephone' => '0399990000',
                'datestamp' => now(),
            ]);
        }
    }

    if (! in_array($slug, ['survey', 'code', 'voc'], true)) {
        $hasLink = Weblink::query()->where('profile_id', $profile->id)->exists();

        if (! $hasLink) {
            Weblink::query()->create([
                'profile_id' => $profile->id,
                'link_button' => 1,
                'link_button_text' => 'Visit demo site',
                'link_button_url' => 'https://example.com',
                'link_button_color' => '008901',
            ]);
        }
    }
}
