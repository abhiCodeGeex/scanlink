<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Services\ScanalyticsGraphEngine;
use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScanalyticsGraphController extends Controller
{
    use InteractsWithClientMembership;

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

    protected function authorizedProfileId(Request $request): int
    {
        $profileId = (int) $request->query('pid', 0);
        $member = static::portalMembership();

        abort_unless($member && $profileId > 0, 403);
        abort_unless(
            InteractsWithClientMembership::memberCanAccessAnalytics($member),
            403
        );

        $exists = Profile::query()
            ->where('client_id', $member->client_id)
            ->active()
            ->whereKey($profileId)
            ->exists();

        abort_unless($exists, 404);

        return $profileId;
    }
}
