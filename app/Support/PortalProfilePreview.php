<?php

namespace App\Support;

use App\Models\ClientUser;
use App\Models\Profile;
use App\Models\Weblink;
use App\Services\ProfileQrService;
use Illuminate\Support\Facades\Auth;

/**
 * Portal profile editor iframe preview (ask_for_location=no).
 *
 * Supports an unsaved form draft in session so the phone preview updates
 * as the editor changes fields, without waiting for Save.
 */
class PortalProfilePreview
{
    /**
     * Profile columns that are safe to overlay from the Filament form draft.
     *
     * @var list<string>
     */
    private const DRAFT_SCALAR_KEYS = [
        'name', 'code_profile_name', 'identification', 'serial_no', 'address',
        'description', 'notes', 'name_company', 'telephone', 'mobile', 'email',
        'name2', 'description2', 'url', 'show_header', 'show_name', 'show_description',
        'show_address', 'show_telephone', 'show_mobile', 'show_email', 'show_url',
        'display_share_link', 'form_is_enable', 'form_active', 'form_submission_format',
        'enable_form_analytics', 'enable_data_collection',
        // User Access Security — must overlay so phone preview shows the login gate
        // before Save (and after Save the DB values apply the same way).
        'protect', 'password',
        'voc_first_name', 'voc_last_name', 'voc_address', 'voc_town', 'voc_state',
        'voc_phone', 'voc_dob', 'voc_known_allergies', 'voc_blood_type',
        'voc_next_of_kin', 'voc_contact_phone', 'voc_employer', 'voc_emp_address',
        'voc_emp_town', 'voc_emp_state', 'voc_emp_phone',
        'voc_title_bar_enable', 'voc_title_bar_text', 'voc_title_bar_colour',
    ];

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
            ->active()
            ->exists();
    }

    public static function canBypassScanRestrictions(Profile $profile): bool
    {
        return self::isPreviewRequest() && self::authorized($profile);
    }

    /**
     * Cache-bust query string so iframe reloads after save / preview refresh.
     */
    public static function previewUrl(Profile $profile, int $refreshKey = 0): string
    {
        $profile->loadMissing('client');

        $stamp = ($profile->updated_at?->timestamp ?? time()).'-'.$refreshKey;

        return app(ProfileQrService::class)->profileUrl($profile)
            .'?ask_for_location=no&portal_preview=1&_='.$stamp;
    }

    public static function draftSessionKey(int $profileId): string
    {
        return 'portal_phone_preview_draft_'.$profileId;
    }

    /**
     * @param  array<string, mixed>  $formData
     */
    public static function storeDraft(int $profileId, array $formData): void
    {
        $weblinks = $formData['weblinks'] ?? [];

        if (! is_array($weblinks)) {
            $weblinks = [];
        }

        session([
            self::draftSessionKey($profileId) => [
                'scalars' => $formData,
                'weblinks' => array_values($weblinks),
                'stored_at' => now()->getTimestamp(),
            ],
        ]);
    }

    public static function clearDraft(int $profileId): void
    {
        session()->forget(self::draftSessionKey($profileId));
    }

    /**
     * Overlay unsaved editor state onto the profile used by the phone preview.
     * Mutates the in-memory model only — never writes to the database.
     */
    public static function applyDraft(Profile $profile): void
    {
        if (! self::isPreviewRequest() || ! self::authorized($profile)) {
            return;
        }

        $draft = session(self::draftSessionKey((int) $profile->id));

        if (! is_array($draft)) {
            return;
        }

        $scalars = is_array($draft['scalars'] ?? null) ? $draft['scalars'] : $draft;
        $fill = [];

        foreach (self::DRAFT_SCALAR_KEYS as $key) {
            if (array_key_exists($key, $scalars)) {
                $value = $scalars[$key];
                // Filament's RichEditor keeps its LIVE form state as a TipTap document
                // (an array) — it only becomes HTML on save. Convert any array draft value
                // to HTML so the phone preview never hits "Array to string conversion".
                $fill[$key] = is_array($value) ? self::tipTapToHtml($value) : $value;
            }
        }

        if ($fill !== []) {
            $profile->fill($fill);
        }

        $weblinkRows = $draft['weblinks'] ?? ($scalars['weblinks'] ?? null);

        if (! is_array($weblinkRows)) {
            return;
        }

        $links = collect($weblinkRows)
            ->filter(fn ($row): bool => is_array($row))
            ->map(function (array $row): Weblink {
                $enabled = ($row['link_button'] ?? false) === true
                    || ($row['link_button'] ?? null) === 1
                    || ($row['link_button'] ?? null) === '1';

                $link = new Weblink;
                $link->forceFill([
                    'link_button' => $enabled ? 1 : 0,
                    'link_button_text' => (string) ($row['link_button_text'] ?? ''),
                    'link_button_url' => (string) ($row['link_button_url'] ?? ''),
                    'link_button_color' => ltrim((string) ($row['link_button_color'] ?: '007A01'), '#'),
                    'link_button_align' => (string) ($row['link_button_align'] ?: 'left'),
                ]);

                return $link;
            })
            ->values();

        $profile->setRelation('weblinks', $links);
    }

    /**
     * Minimal TipTap-document → HTML converter for phone-preview drafts (the live state of
     * Filament's RichEditor). Covers the node/mark types our toolbars offer; anything
     * unknown falls back to its children's text. Dependency-free on purpose.
     */
    public static function tipTapToHtml(array $doc): string
    {
        $nodes = $doc['content'] ?? $doc;

        return is_array($nodes) ? self::tipTapNodes($nodes) : '';
    }

    /**
     * @param  array<int, mixed>  $nodes
     */
    private static function tipTapNodes(array $nodes): string
    {
        $html = '';

        foreach ($nodes as $node) {
            if (! is_array($node)) {
                continue;
            }

            $children = is_array($node['content'] ?? null) ? self::tipTapNodes($node['content']) : '';

            $html .= match ($node['type'] ?? '') {
                'text' => self::tipTapText($node),
                'paragraph' => '<p>'.$children.'</p>',
                'heading' => '<h3>'.$children.'</h3>',
                'bulletList' => '<ul>'.$children.'</ul>',
                'orderedList' => '<ol>'.$children.'</ol>',
                'listItem' => '<li>'.$children.'</li>',
                'hardBreak' => '<br>',
                default => $children,
            };
        }

        return $html;
    }

    /**
     * @param  array<string, mixed>  $node
     */
    private static function tipTapText(array $node): string
    {
        $text = e((string) ($node['text'] ?? ''));

        foreach (($node['marks'] ?? []) as $mark) {
            if (! is_array($mark)) {
                continue;
            }

            $text = match ($mark['type'] ?? '') {
                'bold' => '<strong>'.$text.'</strong>',
                'italic' => '<em>'.$text.'</em>',
                'underline' => '<u>'.$text.'</u>',
                'link' => '<a href="'.e((string) ($mark['attrs']['href'] ?? '#')).'" target="_blank" rel="noopener">'.$text.'</a>',
                default => $text,
            };
        }

        return $text;
    }
}
