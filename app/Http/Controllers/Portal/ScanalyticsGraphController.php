<?php

namespace App\Http\Controllers\Portal;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Http\Controllers\Controller;
use App\Models\AnaItemAnalytics;
use App\Models\FormBuilderAnswer;
use App\Models\Profile;
use App\Services\ScanalyticsGraphEngine;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ScanalyticsGraphController extends Controller
{
    use InteractsWithClientMembership;

    /**
     * Standalone legacy scanalytics page (heading + buttons + theme switcher + stats +
     * bar charts) embedded in the Filament Scanalytics screen via an iframe, so the
     * jQuery-UI theme, green buttons, drill-down and hover are isolated from Filament's
     * Tailwind reset / Livewire.
     */
    public function charts(Request $request): View
    {
        $profileId = $this->authorizedProfileId($request);
        $profile = Profile::query()->with('equipmentType')->find($profileId);

        $name = filled(trim((string) $profile?->code_profile_name))
            ? (string) $profile->code_profile_name
            : (string) ($profile?->name ?? '');

        $base = url('/portal');

        // A ddBarChart with an empty dataset renders a degenerate blank "1" bar. Only
        // render a chart when it actually has data (e.g. country is empty when scans have
        // no geolocation), and show a friendly note if there's nothing to plot at all.
        $engine = app(\App\Services\ScanalyticsGraphEngine::class);
        $hasCountry = ! empty($engine->country($profileId)['DATA'] ?? []);
        $hasDevice = ! empty($engine->device($profileId)['DATA'] ?? []);
        $hasBrowser = ! empty($engine->browser($profileId)['DATA'] ?? []);

        return view('portal.scanalytics-charts', [
            'profileId' => $profileId,
            'profileName' => $name,
            'hasCountry' => $hasCountry,
            'hasDevice' => $hasDevice,
            'hasBrowser' => $hasBrowser,
            'analyticsCount' => Schema::hasTable('ana_item_analytics')
                ? AnaItemAnalytics::query()->where('id_item', (string) $profileId)->count()
                : 0,
            'formSubmissionCount' => $this->formSubmissionCount($profileId),
            'mapUrl' => $base.'/scan-analytics?profile='.$profileId.'&view=map',
            'locationsUrl' => $base.'/scan-analytics?profile='.$profileId.'&view=locations',
            'exportUrl' => $base.'/scanalytics-export?pid='.$profileId,
            'returnUrl' => $base.'/profiles',
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $profileId = $this->authorizedProfileId($request);
        $rows = AnaItemAnalytics::query()->where('id_item', (string) $profileId)->orderByDesc('id')->get();
        $platforms = Schema::hasTable('ana_platforms') ? DB::table('ana_platforms')->pluck('platform_name', 'id') : collect();
        $devices = Schema::hasTable('ana_devices') ? DB::table('ana_devices')->pluck('device_name', 'id') : collect();
        $browsers = Schema::hasTable('ana_browsers') ? DB::table('ana_browsers')->pluck('browser_name', 'id') : collect();

        $label = fn ($id, $names, $fallback): string => ($id === null || $id === '')
            ? 'Unknown'
            : (is_numeric($id) ? (string) ($names[(int) $id] ?? ($fallback.' '.$id)) : (string) $id);

        return response()->streamDownload(function () use ($rows, $platforms, $devices, $browsers, $label): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['#', 'Date Time', 'IP', 'Location', 'Latitude', 'Longitude', 'Device', 'Platform', 'Screen Size', 'Browser', 'Browser Version', 'Scan Type']);

            foreach ($rows as $index => $row) {
                fputcsv($handle, [
                    $index + 1,
                    $row->created_at?->format('Y-m-d H:i:s') ?? '',
                    (string) $row->ip_add,
                    trim(implode(', ', array_filter([(string) $row->city_name, (string) $row->region_name, (string) $row->country_name]))),
                    (string) $row->latitude,
                    (string) $row->longitude,
                    $label($row->id_platform, $platforms, 'Platform'),
                    $label($row->id_device, $devices, 'Device'),
                    (string) ($row->screen_size ?? ''),
                    $label($row->id_browser, $browsers, 'Browser'),
                    (string) ($row->browser_version ?? ''),
                    strtolower((string) ($row->scan_type ?? '')) === 'gps' ? 'GPS' : 'IP',
                ]);
            }

            fclose($handle);
        }, 'excelreport-'.$profileId.'.csv', ['Content-Type' => 'text/csv']);
    }

    public function country(Request $request, ScanalyticsGraphEngine $engine): JsonResponse
    {
        return response()->json($engine->country($this->authorizedProfileId($request)));
    }

    public function device(Request $request, ScanalyticsGraphEngine $engine): JsonResponse
    {
        return response()->json($engine->device($this->authorizedProfileId($request)));
    }

    public function browser(Request $request, ScanalyticsGraphEngine $engine): JsonResponse
    {
        return response()->json($engine->browser($this->authorizedProfileId($request)));
    }

    protected function formSubmissionCount(int $profileId): int
    {
        if (! Schema::hasTable('form_builder_answers')) {
            return 0;
        }

        return (int) FormBuilderAnswer::query()
            ->where('profile_id', $profileId)
            ->whereNotNull('session_id')
            ->where('session_id', '!=', '')
            ->selectRaw('COUNT(DISTINCT session_id) as aggregate')
            ->value('aggregate');
    }

    protected function authorizedProfileId(Request $request): int
    {
        $profileId = (int) $request->query('pid', 0);
        $member = static::portalMembership();

        abort_unless($member && $profileId > 0, 403);
        abort_unless(InteractsWithClientMembership::memberCanAccessAnalytics($member), 403);

        $exists = Profile::query()
            ->where('client_id', $member->client_id)
            ->active()
            ->whereKey($profileId)
            ->exists();

        abort_unless($exists, 404);

        return $profileId;
    }
}
