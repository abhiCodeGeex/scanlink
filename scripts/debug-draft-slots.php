<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ClientUser;
use App\Models\EquipmentType;
use App\Models\Profile;
use App\Services\ProfileDraftSlotService;

$member = ClientUser::query()->where('email', 'portal-test@scanlink.local')->first()
    ?? ClientUser::query()->where('status', true)->orderByDesc('role')->first();

if (! $member) {
    fwrite(STDERR, "No member\n");
    exit(1);
}

$clientId = (int) $member->client_id;
$svc = app(ProfileDraftSlotService::class);
$counts = $svc->slotCounts($clientId);
$open = $svc->availableSlotForClient($clientId);
$locationId = EquipmentType::query()->where('slag', 'location')->value('id');

echo "member={$member->email} client={$clientId}\n";
echo "slots total={$counts['total']} open={$counts['open']}\n";
echo 'open_slot_id='.($open?->id ?? 'NULL')."\n";
echo "location_type_id={$locationId}\n";

$recent = Profile::query()
    ->where('client_id', $clientId)
    ->where('type_id', $locationId)
    ->orderByDesc('id')
    ->limit(5)
    ->get(['id', 'code_profile_name', 'update_or_not', 'deleted', 'form_id']);

foreach ($recent as $p) {
    echo "loc #{$p->id} update_or_not=".((int) $p->update_or_not)." deleted=".((int) $p->deleted)." form_id={$p->form_id} name={$p->code_profile_name}\n";
}
