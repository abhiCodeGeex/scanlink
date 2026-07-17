<?php

namespace App\Support;

use App\Models\ClientUser;
use App\Models\Profile;
use App\Services\ProfileQrService;
use Illuminate\Support\Facades\Auth;

/**
 * Portal profile editor iframe preview (ask_for_location=no).
 */
class PortalProfilePreview
{
    public static function isPreviewRequest(): bool
    {
        return request()->query('ask_for_location') === 'no'
            && request()->query('portal_preview') === '1';
    }

    public static function authorized(Profile $profile): bool
    {
        if (! Auth::check()) {
            return false;
        }

        $userId = Auth::id();

        if ($userId === null) {
            return false;
        }

        return ClientUser::query()
            ->where('auth_user_id', $userId)
            ->where('client_id', $profile->client_id)
            ->where('status', true)
            ->exists();
    }

    public static function canBypassScanRestrictions(Profile $profile): bool
    {
        return self::isPreviewRequest() && self::authorized($profile);
    }

    /**
     * Cache-bust query string so iframe reloads after save.
     */
    public static function previewUrl(Profile $profile): string
    {
        $profile->loadMissing('client');

        $stamp = $profile->updated_at?->timestamp ?? time();

        return app(ProfileQrService::class)->profileUrl($profile)
            .'?ask_for_location=no&portal_preview=1&_='.$stamp;
    }
}
