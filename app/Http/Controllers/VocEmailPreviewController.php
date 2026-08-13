<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use App\Support\PublicMediaPath;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Renders the VOC document-expiry notification email in-browser so the editor's
 * "Preview" links (Email Notification Settings) show the 30-day renewal reminder /
 * expiry notice built from the profile's SAVED data. Legacy voc/email_template_view.
 */
class VocEmailPreviewController extends Controller
{
    public function show(Request $request, Profile $profile, string $kind): Response
    {
        abort_unless(in_array($kind, ['reminder', 'expired'], true), 404);
        abort_unless($this->canPreview($profile), 403);

        $expired = $kind === 'expired';
        $target = $expired ? now() : now()->addDays(30);

        $profile->loadMissing(['vocDocuments', 'logos']);

        $docNames = $profile->vocDocuments->isNotEmpty()
            ? $profile->vocDocuments->map(fn ($doc): string => e((string) ($doc->name ?: 'Document')).'<br>')->implode('')
            : e('Sample Document').'<br>';

        $logoName = (string) ($profile->logos->first()?->logo_name ?? '');

        return response()->view('emails.voc.document-expiry', [
            'expired' => $expired,
            'vocUsername' => trim(((string) $profile->voc_first_name).' '.((string) $profile->voc_last_name)) ?: 'VOCC Holder',
            'docNames' => $docNames,
            'expiryDate' => $target->format('d/m/Y'),
            'emailUrl' => trim((string) $profile->voc_email_url) ?: 'http://www.myskills.gov.au/',
            'emailText' => trim((string) $profile->voc_email_text) ?: 'Click here to find a service provider near you',
            'signature' => (string) $profile->voc_email_sign_line1,
            'logoUrl' => $logoName !== '' ? PublicMediaPath::url($logoName) : null,
        ]);
    }

    protected function canPreview(Profile $profile): bool
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        // A VOC secondary user linked to this profile...
        if ($profile->vocUsers()->where('auth_user_id', $user->id)->exists()) {
            return true;
        }

        // ...or a portal member of the profile's owning client.
        return $user->clientMemberships()
            ->active()
            ->where('client_id', $profile->client_id)
            ->exists();
    }
}
