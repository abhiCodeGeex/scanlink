<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\Client;
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
use Illuminate\Support\Str;
use Illuminate\View\View;

class MobileProfileController extends Controller
{
    public function show(string $clientUrl, int $profileId, ProfileQrService $qrService, AnalyticsApiService $analytics, MobileProfileViewResolver $views): View|RedirectResponse
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

        if (! $portalPreview) {
            $analytics->registerUrl($qrService->profileUrl($profile));
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

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'surname' => ['nullable', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            // Legacy field aliases still accepted from older scan markup.
            'user_name' => ['nullable', 'string', 'max:255'],
            'user_email' => ['nullable', 'email', 'max:255'],
            'user_mobile' => ['nullable', 'string', 'max:50'],
        ]);

        $name = trim((string) ($validated['name'] ?? $validated['user_name'] ?? ''));
        $surname = trim((string) ($validated['surname'] ?? ''));
        $mobile = trim((string) ($validated['mobile'] ?? $validated['user_mobile'] ?? ''));
        $email = trim((string) ($validated['email'] ?? $validated['user_email'] ?? ''));

        // Legacy dashboard/visitorlog reads from `contacts` (name/surname/mobile/email).
        if ($name !== '' || $surname !== '' || $mobile !== '' || $email !== '') {
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
            'answers_file' => ['nullable', 'array'],
            'answers_file.*' => ['nullable', 'file', 'max:10240'],
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
                if (filled($value) && is_scalar($value)) {
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

        /** @var array<int, UploadedFile|null> $files */
        $files = $request->file('answers_file') ?? [];

        foreach ($files as $questionId => $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $path = $file->store('form-uploads/'.$profile->id, 'public');
                $existing = $answerMap[$questionId] ?? null;
                $answerMap[$questionId] = filled($existing)
                    ? trim((string) $existing.' | File: '.$path)
                    : $path;
            }
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

        $this->markParticipatedParticipants($profile, $questions, $rawAnswers);
        $this->notifyFormSubmissionRecipients($profile, $sessionId, $questions, $savedAnswers);

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
                'pictures',
                'documents',
                'videos',
                'weblinks',
                'checklistItems',
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
            $name = is_array($raw) ? trim((string) ($raw[0] ?? '')) : trim((string) $raw);

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

        foreach ($savedAnswers as $questionId => $answer) {
            $question = $questions->get((int) $questionId);

            if ($question && (int) $question->question_type_id === 24) {
                $email = strtolower(trim($answer));

                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $recipients->push($email);
                }
            }
        }

        $recipients = $recipients->map(fn (string $email): string => strtolower(trim($email)))
            ->filter(fn (string $email): bool => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values();

        if ($recipients->isEmpty()) {
            return;
        }

        $subjectParts = array_filter([
            trim((string) ($profile->form_email_tag ?? '')),
            trim((string) ($profile->form_title ?? '')),
        ]);
        $subject = $subjectParts !== [] ? implode(' — ', $subjectParts) : 'Form submission — '.$profile->name;

        $lines = ["Form submission for {$profile->name}", "Session: {$sessionId}", ''];

        foreach ($savedAnswers as $questionId => $answer) {
            $question = $questions->get((int) $questionId);
            $label = strip_tags((string) ($question?->question_text ?: "Question #{$questionId}"));
            $lines[] = "{$label}: {$answer}";
        }

        $body = implode("\n", $lines);
        $attachPdf = (int) ($profile->form_submission_format ?? 0) === 1;
        $pdfContent = $attachPdf ? $this->buildFormSubmissionPdf($profile, $sessionId, $questions, $savedAnswers) : null;

        foreach ($recipients as $email) {
            try {
                if ($pdfContent !== null) {
                    Mail::send([], [], function ($message) use ($email, $subject, $body, $pdfContent): void {
                        $message->to($email)
                            ->subject($subject)
                            ->text($body)
                            ->attachData($pdfContent, 'form-submission.pdf', ['mime' => 'application/pdf']);
                    });
                } else {
                    Mail::raw($body, function ($message) use ($email, $subject): void {
                        $message->to($email)->subject($subject);
                    });
                }
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
}
