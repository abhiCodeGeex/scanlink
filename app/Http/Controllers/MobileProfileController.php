<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\Client;
use App\Models\AnaItemAnalytics;
use App\Models\FormBuilderAnswer;
use App\Models\FormBuilderQuestion;
use App\Models\FormBuilderRecipient;
use App\Models\Participant;
use App\Models\Profile;
use App\Models\CollectedContact;
use App\Services\AnalyticsApiService;
use App\Services\FormBuilderService;
use App\Services\MobileProfileViewResolver;
use App\Services\ProfileQrService;
use App\Support\PortalProfilePreview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MobileProfileController extends Controller
{
    public function show(Request $request, string $clientUrl, int $profileId, ProfileQrService $qrService, AnalyticsApiService $analytics, MobileProfileViewResolver $views): View|RedirectResponse
    {
        $profile = $this->resolveProfile($clientUrl, $profileId, eager: true);
        $portalPreview = PortalProfilePreview::canBypassScanRestrictions($profile);

        if ($portalPreview) {
            PortalProfilePreview::applyDraft($profile);
        }

        if ($profile->isExpired() && ! $portalPreview) {
            return view('scan.expired', compact('profile'));
        }

        if ($redirectUrl = $this->codeRedirectUrl($profile)) {
            if (! $portalPreview) {
                // Legacy records the scan for URL-link codes too, and (when data collection is
                // enabled) captures the visitor BEFORE redirecting to the destination URL.
                $analytics->ensureAnalyticKey($profile, $qrService->profileUrl($profile));
                $this->recordScanHit($request, $profile);

                if ($profile->enable_data_collection && $this->needsVisitorInfo($profile)) {
                    return view('scan.code-redirect', [
                        'profile' => $profile,
                        'clientUrl' => $clientUrl,
                        'redirectUrl' => $redirectUrl,
                        'publicMediaUrl' => fn (?string $path): ?string => \App\Support\PublicMediaPath::url($path),
                    ]);
                }

                return redirect()->away($redirectUrl);
            }
        }

        // Password gate applies to live scans and portal preview (legacy mobile behaviour).
        if ($profile->protect && ! $this->isUnlocked($profile)) {
            return view('scan.password', [
                'profile' => $profile,
                'clientUrl' => $clientUrl,
                'portalPreview' => $portalPreview || PortalProfilePreview::isPreviewRequest(),
            ]);
        }

        $scanHitId = null;

        if (! $portalPreview) {
            // Legacy registers the URL on create; backfill analytic_key here for any
            // profile that still lacks one (idempotent once stored).
            $analytics->ensureAnalyticKey($profile, $qrService->profileUrl($profile));
            $scanHitId = $this->recordScanHit($request, $profile);
        }

        $questions = FormBuilderQuestion::query()
            ->with(['questionType', 'options'])
            ->where('profile_id', $profile->id)
            ->orderBy('question_order')
            ->get();

        $payload = [
            'profile' => $profile,
            'clientUrl' => $clientUrl,
            'questions' => $questions,
            'nameHeading' => $views->nameHeading($profile),
            'portalPreview' => $portalPreview,
            'needsVisitorInfo' => $portalPreview ? false : $this->needsVisitorInfo($profile),
            // Legacy blanks the page content when scanned outside the activation window.
            'withinActivationWindow' => $portalPreview ? true : $profile->isWithinActivationWindow(),
            'scanHitId' => $scanHitId,
            'publicMediaUrl' => fn (?string $path): ?string => \App\Support\PublicMediaPath::url($path),
            'youtubeEmbedUrl' => fn (string $videoName): ?string => $this->youtubeEmbedUrl($videoName),
        ];

        return view($views->viewFor($profile), $payload);
    }

    public function unlock(Request $request, string $clientUrl, int $profileId): RedirectResponse
    {
        $profile = $this->resolveProfile($clientUrl, $profileId);

        $password = (string) $request->input('password', '');

        if ($this->profilePasswordMatches($profile, $password)) {
            session()->put($this->unlockSessionKey($profile), true);

            // Keep portal iframe preview query so unlock returns to the phone preview.
            if (
                $request->boolean('portal_preview')
                || $request->input('ask_for_location') === 'no'
            ) {
                return redirect()->to(
                    route('scan.show', [$clientUrl, $profileId])
                    .'?ask_for_location=no&portal_preview=1&_='.time()
                );
            }

            return redirect()->route('scan.show', [$clientUrl, $profileId]);
        }

        return back()->withErrors(['password' => 'Incorrect password.']);
    }

    /**
     * Live Kohana stores profile unlock passwords as plain text.
     * Newer saves may hash them — accept both.
     */
    protected function profilePasswordMatches(Profile $profile, string $password): bool
    {
        $stored = (string) ($profile->password ?? '');

        if ($stored === '' || $password === '') {
            return false;
        }

        if (Hash::isHashed($stored)) {
            return Hash::check($password, $stored);
        }

        return hash_equals($stored, $password);
    }

    public function storeVisitor(Request $request, string $clientUrl, int $profileId): RedirectResponse
    {
        $profile = $this->resolveProfile($clientUrl, $profileId);

        // Legacy JS validation: a supplied mobile must be exactly 10 digits.
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'surname' => ['nullable', 'string', 'max:255'],
            'mobile' => ['nullable', 'digits:10'],
            'email' => ['nullable', 'email', 'max:255'],
            // Legacy field aliases still accepted from older scan markup.
            'user_name' => ['nullable', 'string', 'max:255'],
            'user_email' => ['nullable', 'email', 'max:255'],
            'user_mobile' => ['nullable', 'digits:10'],
        ]);

        $name = trim((string) ($validated['name'] ?? $validated['user_name'] ?? ''));
        $surname = trim((string) ($validated['surname'] ?? ''));
        $mobile = trim((string) ($validated['mobile'] ?? $validated['user_mobile'] ?? ''));
        $email = trim((string) ($validated['email'] ?? $validated['user_email'] ?? ''));

        // Legacy addUserInfo stores a row only when name OR mobile OR email is non-empty.
        if ($name !== '' || $mobile !== '' || $email !== '') {
            CollectedContact::query()->create([
                'id_profile' => $profile->id,
                'name' => $name,
                'surname' => $surname,
                'mobile' => $mobile,
                'email' => $email,
                'created_at' => now(),
            ]);
        }

        session()->put('user_info', (string) $profile->id);

        // Legacy suppresses the popup across sessions via the isContactDialog cookie.
        \Illuminate\Support\Facades\Cookie::queue('visitor_info_'.$profile->id, '1', 60 * 24 * 365);

        // Legacy: a URL-link code redirects to its destination after capturing the visitor.
        if ($profile->typeSlug() === 'code' && ($destination = $this->codeRedirectUrl($profile))) {
            return redirect()->away($destination);
        }

        return redirect()->route('scan.show', [$clientUrl, $profileId]);
    }

    public function storeFormAnswer(Request $request, string $clientUrl, int $profileId, FormBuilderService $formBuilder): RedirectResponse
    {
        $profile = $this->resolveProfile($clientUrl, $profileId);

        $validated = $request->validate([
            'answers' => ['nullable', 'array'],
            'answers.*' => ['nullable'],
            'answers_meta' => ['nullable', 'array'],
            'answers_sig_text' => ['nullable', 'array'],
            'answers_sig_text.*' => ['nullable', 'string', 'max:5000'],
            // answers_file[qid] is a single file (SWMS) OR answers_file[qid][] an array (multi-upload);
            // validity + size are checked per file in the loop below.
            'answers_file' => ['nullable', 'array'],
            'session_id' => ['nullable', 'string', 'max:100'],
            'app_user_firstname' => ['nullable', 'string', 'max:100'],
            'app_user_lastname' => ['nullable', 'string', 'max:100'],
            'app_user_email' => ['nullable', 'email', 'max:255'],
            'app_user_mobile' => ['nullable', 'string', 'max:50'],
        ]);

        $questions = FormBuilderQuestion::query()
            ->where('profile_id', $profile->id)
            ->get()
            ->keyBy('question_id');

        $displayOnlyTypes = [2, 10, 11, 12, 13, 14, 20, 21];
        $sessionId = $validated['session_id'] ?? (string) Str::uuid();

        $answerMap = $validated['answers'] ?? [];
        $rawAnswers = $answerMap;

        foreach ($validated['answers_sig_text'] ?? [] as $questionId => $sigText) {
            if (filled($sigText) && empty($answerMap[$questionId])) {
                $answerMap[$questionId] = $sigText;
            }
        }

        foreach ($validated['answers_meta'] ?? [] as $questionId => $meta) {
            if (! is_array($meta)) {
                continue;
            }

            $question = $questions->get((int) $questionId);

            // Legacy Covid check-in stores fields joined with ::: in fixed order.
            if ($question && (int) $question->question_type_id === 25) {
                $parts = [
                    (string) ($meta['visitor_name'] ?? ''),
                    (string) ($meta['visitor_phone'] ?? ''),
                    (string) ($meta['checkin_date'] ?? ''),
                    (string) ($meta['checkin_time'] ?? ''),
                    (string) ($meta['venue_name'] ?? ''),
                    (string) ($meta['venue_address'] ?? ''),
                    (string) ($meta['location_type'] ?? ''),
                ];

                $extra = (string) ($meta['vehicle_or_other'] ?? '');
                if ($extra !== '' || in_array($parts[6], ['Vehicle', 'Other'], true)) {
                    $parts[] = $extra;
                }

                $answerMap[$questionId] = implode(':::', $parts);

                continue;
            }

            $parts = [];
            foreach ($meta as $key => $value) {
                if (is_array($value)) {
                    // Repeatable sub-fields (e.g. participant employer[]) arrive as arrays.
                    $joined = implode(', ', array_filter(
                        array_map(fn ($v): string => trim((string) $v), $value),
                        fn (string $v): bool => $v !== '',
                    ));
                    if ($joined !== '') {
                        $parts[] = ucfirst(str_replace('_', ' ', (string) $key)).': '.$joined;
                    }
                } elseif (filled($value) && is_scalar($value)) {
                    $parts[] = ucfirst(str_replace('_', ' ', (string) $key)).': '.$value;
                }
            }

            if ($parts === []) {
                continue;
            }

            $metaText = implode(' | ', $parts);
            $existing = $answerMap[$questionId] ?? null;

            if (is_array($existing)) {
                $answerMap[$questionId] = array_merge($existing, ['_meta' => $metaText]);
            } elseif (filled($existing)) {
                $answerMap[$questionId] = trim((string) $existing.' | '.$metaText);
            } else {
                $answerMap[$questionId] = $metaText;
            }
        }

        $files = $request->file('answers_file') ?? [];

        foreach ($files as $questionId => $file) {
            // "Add another" upload sends an array of files; SWMS sends a single file.
            $fileList = is_array($file) ? $file : [$file];
            $storedPaths = [];

            foreach ($fileList as $singleFile) {
                if ($singleFile instanceof UploadedFile
                    && $singleFile->isValid()
                    && $singleFile->getSize() <= 10240 * 1024) {
                    $storedPaths[] = $singleFile->store('form-uploads/'.$profile->id, 'public');
                }
            }

            if ($storedPaths === []) {
                continue;
            }

            $pathStr = implode(', ', $storedPaths);
            $existing = $answerMap[$questionId] ?? null;
            $answerMap[$questionId] = filled($existing)
                ? trim((string) $existing.' | File: '.$pathStr)
                : $pathStr;
        }

        foreach ($questions as $question) {
            if (! $question->is_mandatory) {
                continue;
            }

            if (in_array((int) $question->question_type_id, $displayOnlyTypes, true)) {
                continue;
            }

            $questionId = $question->question_id;
            $answer = $answerMap[$questionId] ?? null;

            if (is_array($answer)) {
                $hasValue = false;

                foreach ($answer as $value) {
                    if (is_array($value)) {
                        if (array_filter($value, fn ($v) => filled($v)) !== []) {
                            $hasValue = true;
                            break;
                        }
                    } elseif (filled($value)) {
                        $hasValue = true;
                        break;
                    }
                }

                if (! $hasValue) {
                    return back()->withErrors(['form' => 'Please complete all mandatory fields.'])->withInput();
                }

                continue;
            }

            if (! filled($answer)) {
                return back()->withErrors(['form' => 'Please complete all mandatory fields.'])->withInput();
            }
        }

        $savedAnswers = [];

        foreach ($answerMap as $questionId => $answer) {
            $question = $questions->get((int) $questionId);

            if ($question && in_array((int) $question->question_type_id, $displayOnlyTypes, true)) {
                continue;
            }

            if (is_array($answer)) {
                $parts = [];
                foreach ($answer as $row => $col) {
                    if (is_array($col)) {
                        $col = implode(': ', array_filter($col, fn ($v) => filled($v)));
                    }
                    if (filled($col)) {
                        $parts[] = is_string($row) ? "{$row} → {$col}" : (string) $col;
                    }
                }
                $answer = implode('; ', array_filter($parts, fn ($v) => filled($v)));
            }

            if (! filled($answer)) {
                continue;
            }

            $formBuilder->createAnswer([
                'question_id' => (int) $questionId,
                'profile_id' => $profile->id,
                'question_answer' => (string) $answer,
                'session_id' => $sessionId,
                'date_time' => now(),
                'app_user_firstname' => $validated['app_user_firstname'] ?? null,
                'app_user_lastname' => $validated['app_user_lastname'] ?? null,
                'app_user_email' => $validated['app_user_email'] ?? null,
                'app_user_mobile' => $validated['app_user_mobile'] ?? null,
            ]);

            $savedAnswers[(int) $questionId] = (string) $answer;
        }

        // Legacy type-24 "Add recipient": every entered email also gets the submission.
        $additionalRecipients = [];
        foreach ($questions as $question) {
            if ((int) $question->question_type_id !== 24) {
                continue;
            }
            $raw = $rawAnswers[$question->question_id] ?? $rawAnswers[(string) $question->question_id] ?? null;
            foreach ((is_array($raw) ? $raw : [$raw]) as $candidate) {
                $email = strtolower(trim((string) $candidate));
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $additionalRecipients[] = $email;
                }
            }
        }

        $this->markParticipatedParticipants($profile, $questions, $rawAnswers);
        $this->notifyFormSubmissionRecipients($profile, $sessionId, $questions, $savedAnswers, $additionalRecipients);

        return redirect()
            ->route('scan.show', [$clientUrl, $profileId])
            ->with('form_submitted', true);
    }

    public function checkChecklistItem(Request $request, string $clientUrl, int $profileId, int $itemId): RedirectResponse
    {
        $profile = $this->resolveProfile($clientUrl, $profileId);

        ChecklistItem::query()
            ->where('profile_id', $profile->id)
            ->where('id', $itemId)
            ->firstOrFail()
            ->update(['datetime' => now()]);

        return redirect()->route('scan.show', [$clientUrl, $profileId]);
    }

    public function uncheckChecklistItem(Request $request, string $clientUrl, int $profileId, int $itemId): RedirectResponse
    {
        $profile = $this->resolveProfile($clientUrl, $profileId);

        ChecklistItem::query()
            ->where('profile_id', $profile->id)
            ->where('id', $itemId)
            ->firstOrFail()
            ->update(['datetime' => null]);

        return redirect()->route('scan.show', [$clientUrl, $profileId]);
    }

    protected function resolveProfile(string $clientUrl, int $profileId, bool $eager = false): Profile
    {
        $client = Client::query()->where('url', $clientUrl)->firstOrFail();

        $query = Profile::query()
            ->where('client_id', $client->id)
            ->where('id', $profileId)
            ->active();

        if ($eager) {
            $query->with([
                'logos',
                'logosExtra',
                'pictures',
                'picturesExtra',
                'documents',
                'videos',
                'weblinks',
                'checklistItems',
                'vocDocuments',
                'equipmentType',
                'client',
                'owner',
            ]);
        }

        return $query->firstOrFail();
    }

    protected function codeRedirectUrl(Profile $profile): ?string
    {
        if ($profile->typeSlug() !== 'code') {
            return null;
        }

        foreach ([$profile->shorturl, $profile->url, $profile->name] as $candidate) {
            if (! filled($candidate)) {
                continue;
            }

            $candidate = trim((string) $candidate);

            if (filter_var($candidate, FILTER_VALIDATE_URL)) {
                return $candidate;
            }

            if (str_starts_with($candidate, 'http://') || str_starts_with($candidate, 'https://')) {
                return $candidate;
            }
        }

        return null;
    }

    protected function isUnlocked(Profile $profile): bool
    {
        return (bool) session()->get($this->unlockSessionKey($profile), false);
    }

    protected function unlockSessionKey(Profile $profile): string
    {
        return 'scan_unlock_'.$profile->id;
    }

    protected function needsVisitorInfo(Profile $profile): bool
    {
        if (! $profile->enable_data_collection) {
            return false;
        }

        // Legacy: already-signed-in visitors aren't re-prompted (session flag or cross-session cookie).
        if (request()->cookie('visitor_info_'.$profile->id)) {
            return false;
        }

        return session()->get('user_info') !== (string) $profile->id;
    }

    protected function youtubeEmbedUrl(string $videoName): ?string
    {
        $videoId = app(\App\Services\YouTubeService::class)->parseVideoId($videoName);

        return $videoId ? 'https://www.youtube.com/embed/'.$videoId : null;
    }

    /**
     * @param  Collection<int, FormBuilderQuestion>  $questions
     * @param  array<int|string, mixed>  $savedAnswers
     */
    protected function markParticipatedParticipants(Profile $profile, Collection $questions, array $savedAnswers): void
    {
        $participantQuestions = $questions->filter(
            fn (FormBuilderQuestion $question): bool => (int) $question->question_type_id === 18
        );

        if ($participantQuestions->isEmpty()) {
            return;
        }

        foreach ($participantQuestions as $question) {
            $raw = $savedAnswers[$question->question_id] ?? $savedAnswers[(string) $question->question_id] ?? '';
            // "Add another" submits multiple participant names as an array — mark every one.
            $names = is_array($raw) ? $raw : [$raw];

            foreach ($names as $rawName) {
                $name = trim((string) $rawName);

                if ($name === '') {
                    continue;
                }

                Participant::query()
                    ->where('profile_id', $profile->id)
                    ->whereRaw('LOWER(name) = ?', [strtolower($name)])
                    ->where('is_participated', false)
                    ->update([
                        'is_participated' => true,
                        'participated_date' => now()->toDateString(),
                    ]);

                // Legacy update_participant_status: a walk-up participant whose name is not yet
                // on the list is inserted as an already-participated row.
                $exists = Participant::query()
                    ->where('profile_id', $profile->id)
                    ->whereRaw('LOWER(name) = ?', [strtolower($name)])
                    ->exists();

                if (! $exists) {
                    // participant columns are all NOT NULL (no usable defaults) — set every one.
                    Participant::query()->create([
                        'profile_id' => $profile->id,
                        'name' => $name,
                        'employer_cmp' => '',
                        'due_date' => now()->toDateString(),
                        'participated_date' => now()->toDateString(),
                        'is_participated' => true,
                    ]);
                }
            }
        }
    }

    /**
     * @param  Collection<int, FormBuilderQuestion>  $questions
     * @param  array<int, string>  $savedAnswers
     */
    protected function notifyFormSubmissionRecipients(
        Profile $profile,
        string $sessionId,
        Collection $questions,
        array $savedAnswers,
        array $additionalRecipients = [],
    ): void {
        $formId = (int) ($profile->form_id ?: 0);

        if ($formId <= 0 && $savedAnswers === []) {
            return;
        }

        $recipients = collect();

        if ($formId > 0) {
            $recipients = FormBuilderRecipient::query()
                ->where('form_id', $formId)
                ->pluck('recipient_email');
        }

        // Legacy type-24 "Add recipient" emails (already extracted from the raw answers).
        foreach ($additionalRecipients as $extra) {
            $recipients->push($extra);
        }

        $recipients = $recipients->map(fn (string $email): string => strtolower(trim($email)))
            ->filter(fn (string $email): bool => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values();

        if ($recipients->isEmpty()) {
            return;
        }

        // Legacy: subject is the form's email tag alone, else a default profile-number line.
        $emailTag = trim((string) ($profile->form_email_tag ?? ''));
        $subject = $emailTag !== '' ? $emailTag : 'User response for profile No. '.$profile->id;

        // Legacy builds an HTML email: each answer prefixed with its question label
        // (falls back to "User Input" for label-less questions, matching legacy).
        $rows = [];
        foreach ($savedAnswers as $questionId => $answer) {
            $question = $questions->get((int) $questionId);
            $rows[] = [
                'label' => strip_tags((string) ($question?->question_text ?: 'User Input')),
                'answer' => (string) $answer,
            ];
        }

        $html = view('emails.form-submission', [
            'profile' => $profile,
            'profileName' => trim((string) ($profile->code_profile_name ?: $profile->name)),
            'submittedAt' => now()->format('d/m/Y H:i'),
            'sessionId' => $sessionId,
            'rows' => $rows,
        ])->render();

        $attachPdf = (int) ($profile->form_submission_format ?? 0) === 1;
        $pdfContent = $attachPdf ? $this->buildFormSubmissionPdf($profile, $sessionId, $questions, $savedAnswers) : null;

        foreach ($recipients as $email) {
            try {
                Mail::html($html, function ($message) use ($email, $subject, $pdfContent): void {
                    $message->to($email)->subject($subject);

                    if ($pdfContent !== null) {
                        $message->attachData($pdfContent, 'form-submission.pdf', ['mime' => 'application/pdf']);
                    }
                });
            } catch (\Throwable $exception) {
                Log::warning('Form submission email failed', [
                    'profile_id' => $profile->id,
                    'email' => $email,
                    'error' => $exception->getMessage(),
                ]);
            }
        }
    }

    /**
     * @param  Collection<int, FormBuilderQuestion>  $questions
     * @param  array<int, string>  $savedAnswers
     */
    protected function buildFormSubmissionPdf(
        Profile $profile,
        string $sessionId,
        Collection $questions,
        array $savedAnswers,
    ): ?string {
        try {
            $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetCreator('ScanLink');
            $pdf->SetAuthor($profile->name);
            $pdf->SetTitle('Form submission — '.$profile->name);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetAutoPageBreak(true, 15);
            $pdf->AddPage();
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->Write(0, 'Form submission — '.$profile->name, '', 0, 'L', true);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Write(0, 'Session: '.$sessionId, '', 0, 'L', true);
            $pdf->Ln(4);

            foreach ($savedAnswers as $questionId => $answer) {
                $question = $questions->get((int) $questionId);
                $label = strip_tags((string) ($question?->question_text ?: "Question #{$questionId}"));
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->MultiCell(0, 5, $label.':', 0, 'L', false, 1);
                $pdf->SetFont('helvetica', '', 10);
                $pdf->MultiCell(0, 5, (string) $answer, 0, 'L', false, 1);
                $pdf->Ln(2);
            }

            return $pdf->Output('', 'S');
        } catch (\Throwable $exception) {
            Log::warning('Form submission PDF generation failed', [
                'profile_id' => $profile->id,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    protected function recordScanHit(Request $request, Profile $profile): ?int
    {
        if (! Schema::hasTable('ana_item_analytics')) {
            return null;
        }

        $rawUa = (string) $request->userAgent();

        // Legacy is_bot(): skip crawlers / link-preview bots so they don't inflate scans.
        if ($this->isBot($rawUa)) {
            return null;
        }

        $ua = strtolower($rawUa);
        [$platformId, $deviceId, $scanTypeDefault] = $this->resolveScanPlatformDevice($ua);
        [$browserId, $browserVersion] = $this->resolveScanBrowser($ua, $rawUa);

        $scanType = strtolower((string) $request->query('scan_type', $scanTypeDefault)) === 'gps' ? 'gps' : 'ip';
        $lat = (string) $request->query('latitude', '');
        $lng = (string) $request->query('longitude', '');

        try {
            // Geo (country/region/city) is filled asynchronously by recordScanGeo() once the
            // browser reports its position; it also does the IP lookup off the page-load path.
            $row = AnaItemAnalytics::query()->create([
                'event_id' => 'LARAVEL-'.(string) $profile->id.'-'.Str::uuid(),
                'id_item' => (string) $profile->id,
                'country_code' => null,
                'country_name' => null,
                'region_name' => null,
                'city_name' => null,
                'zipcode' => null,
                'latitude' => $lat !== '' ? $lat : null,
                'longitude' => $lng !== '' ? $lng : null,
                'ip_add' => (string) ($request->ip() ?? ''),
                'timezone' => null,
                'id_browser' => (string) $browserId,
                'browser_version' => $browserVersion,
                'id_platform' => $platformId,
                'id_device' => $deviceId,
                'scan_type' => $scanType,
                'screen_size' => (string) $request->query('screensize', ''),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return (int) $row->getKey();
        } catch (\Throwable $exception) {
            Log::warning('Failed to record local scan hit', [
                'profile_id' => $profile->id,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Client geolocation ping — attaches GPS + IP-derived country/region/city to the scan row
     * created on page load. Fire-and-forget; never fails the visitor's experience.
     */
    public function recordScanGeo(Request $request, string $clientUrl, int $profileId): \Illuminate\Http\Response
    {
        $validated = $request->validate([
            'scan_hit_id' => ['required', 'integer'],
            'lat' => ['nullable', 'string', 'max:32'],
            'lng' => ['nullable', 'string', 'max:32'],
            'screensize' => ['nullable', 'string', 'max:32'],
        ]);

        if (! Schema::hasTable('ana_item_analytics')) {
            return response('OK');
        }

        $row = AnaItemAnalytics::query()
            ->where('id', (int) $validated['scan_hit_id'])
            ->where('id_item', (string) $profileId)
            ->first();

        if (! $row) {
            return response('OK');
        }

        $geo = app(\App\Services\IpGeolocationService::class)->locate((string) ($request->ip() ?? ''));

        $lat = trim((string) ($validated['lat'] ?? ''));
        $lng = trim((string) ($validated['lng'] ?? ''));
        $hasGps = $lat !== '' && $lng !== '';

        $update = ['scan_type' => $hasGps ? 'gps' : ($row->scan_type ?: 'ip')];

        if (filled($validated['screensize'] ?? null)) {
            $update['screen_size'] = $validated['screensize'];
        }

        foreach (['country_code', 'country_name', 'region_name', 'city_name', 'zipcode', 'timezone'] as $key) {
            if (filled($geo[$key] ?? null)) {
                $update[$key] = $geo[$key];
            }
        }

        if ($hasGps) {
            $update['latitude'] = $lat;
            $update['longitude'] = $lng;
        } elseif (filled($geo['latitude'] ?? null)) {
            $update['latitude'] = $geo['latitude'];
            $update['longitude'] = $geo['longitude'];
        }

        try {
            $row->forceFill($update)->save();
        } catch (\Throwable $exception) {
            Log::warning('Failed to attach scan geolocation', [
                'scan_hit_id' => $row->getKey(),
                'message' => $exception->getMessage(),
            ]);
        }

        return response('OK');
    }

    protected function isBot(string $ua): bool
    {
        $ua = trim($ua);

        if ($ua === '') {
            return true;
        }

        return (bool) preg_match(
            '/bot|crawl|spider|slurp|mediapartners|facebookexternalhit|facebot|whatsapp|telegram|preview|monitor|curl|wget|headless|python|axios|okhttp|java\/|semrush|ahrefs|bingpreview|embedly|discord|skype|linkedinbot|pinterest|google(?:bot| favicon)|yandex|baidu|duckduck|applebot/i',
            $ua,
        );
    }

    /**
     * Legacy match_data.php browser IDs (ana_browsers):
     * 1 Firefox, 2 Safari, 3 IE, 4 Chrome, 5 Opera, 6 Netscape.
     *
     * @return array{0: int, 1: string}
     */
    protected function resolveScanBrowser(string $uaLower, string $uaRaw): array
    {
        $version = '';

        if (str_contains($uaLower, 'edg/') || str_contains($uaLower, 'edge/')) {
            if (preg_match('/(?:edg|edge)\/([\d.]+)/i', $uaRaw, $m)) {
                $version = $m[1];
            }

            return [4, $version]; // closest legacy bucket = Chrome
        }

        if (str_contains($uaLower, 'opr/') || str_contains($uaLower, 'opera')) {
            if (preg_match('/(?:opr|opera)\/([\d.]+)/i', $uaRaw, $m)) {
                $version = $m[1];
            }

            return [5, $version];
        }

        if (str_contains($uaLower, 'firefox/')) {
            if (preg_match('/firefox\/([\d.]+)/i', $uaRaw, $m)) {
                $version = $m[1];
            }

            return [1, $version];
        }

        if (str_contains($uaLower, 'chrome/') && ! str_contains($uaLower, 'chromium')) {
            if (preg_match('/chrome\/([\d.]+)/i', $uaRaw, $m)) {
                $version = $m[1];
            }

            return [4, $version];
        }

        if (str_contains($uaLower, 'safari/') && ! str_contains($uaLower, 'chrome') && ! str_contains($uaLower, 'chromium')) {
            if (preg_match('/version\/([\d.]+)/i', $uaRaw, $m)) {
                $version = $m[1];
            }

            return [2, $version];
        }

        if (str_contains($uaLower, 'msie') || str_contains($uaLower, 'trident/')) {
            if (preg_match('/(?:msie |rv:)([\d.]+)/i', $uaRaw, $m)) {
                $version = $m[1];
            }

            return [3, $version];
        }

        if (str_contains($uaLower, 'netscape')) {
            return [6, $version];
        }

        return [4, $version]; // default Chrome (most common modern UA)
    }

    /**
     * Legacy match_data.php platform/device mapping:
     * platforms: 1 Desktop, 2 Mobile, 3 Tablet
     * devices: 3 iphone, 4 Windows, 7 blackberry, 8 ipad, 9 android, 11 Linux, 12 Mac
     *
     * @return array{0: int, 1: int, 2: string}
     */
    protected function resolveScanPlatformDevice(string $uaLower): array
    {
        $isIpad = str_contains($uaLower, 'ipad')
            || (str_contains($uaLower, 'macintosh') && str_contains($uaLower, 'mobile'));
        $isIphone = str_contains($uaLower, 'iphone') || str_contains($uaLower, 'ipod');
        $isAndroid = str_contains($uaLower, 'android');
        $isTablet = $isIpad
            || ($isAndroid && ! str_contains($uaLower, 'mobile'))
            || str_contains($uaLower, 'tablet');

        if ($isIpad) {
            return [3, 8, 'gps']; // Tablet / iPad
        }

        if ($isTablet && $isAndroid) {
            return [3, 9, 'gps']; // Tablet / Android
        }

        if ($isIphone) {
            return [2, 3, 'gps']; // Mobile / iPhone
        }

        if ($isAndroid) {
            return [2, 9, 'gps']; // Mobile / Android
        }

        if (str_contains($uaLower, 'blackberry')) {
            return [2, 7, 'gps']; // Mobile / Blackberry
        }

        if (str_contains($uaLower, 'mac os') || str_contains($uaLower, 'macintosh')) {
            return [1, 12, 'ip']; // Desktop / Mac
        }

        if (str_contains($uaLower, 'linux')) {
            return [1, 11, 'ip']; // Desktop / Linux
        }

        // Windows / unknown desktop
        return [1, 4, 'ip'];
    }
}
