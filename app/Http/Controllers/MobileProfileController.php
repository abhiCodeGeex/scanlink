<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\Client;
use App\Models\FormBuilderAnswer;
use App\Models\FormBuilderQuestion;
use App\Models\FormBuilderRecipient;
use App\Models\Participant;
use App\Models\Profile;
use App\Models\VisitorContact;
use App\Services\AnalyticsApiService;
use App\Services\FormBuilderService;
use App\Services\ProfileQrService;
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
    public function show(string $clientUrl, int $profileId, ProfileQrService $qrService, AnalyticsApiService $analytics): View|RedirectResponse
    {
        $profile = $this->resolveProfile($clientUrl, $profileId, eager: true);

        if ($profile->isExpired()) {
            return view('scan.expired', compact('profile'));
        }

        if ($redirectUrl = $this->codeRedirectUrl($profile)) {
            return redirect()->away($redirectUrl);
        }

        if ($profile->protect && ! $this->isUnlocked($profile)) {
            return view('scan.password', compact('profile', 'clientUrl'));
        }

        $analytics->registerUrl($qrService->profileUrl($profile));

        $questions = FormBuilderQuestion::query()
            ->with(['questionType', 'options'])
            ->where('profile_id', $profile->id)
            ->orderBy('question_order')
            ->get();

        return view('scan.show', [
            'profile' => $profile,
            'clientUrl' => $clientUrl,
            'questions' => $questions,
            'needsVisitorInfo' => $this->needsVisitorInfo($profile),
            'publicMediaUrl' => fn (?string $path): ?string => \App\Support\PublicMediaPath::url($path),
            'youtubeEmbedUrl' => fn (string $videoName): ?string => $this->youtubeEmbedUrl($videoName),
        ]);
    }

    public function unlock(Request $request, string $clientUrl, int $profileId): RedirectResponse
    {
        $profile = $this->resolveProfile($clientUrl, $profileId);

        $password = (string) $request->input('password', '');

        if ($profile->password && Hash::check($password, $profile->password)) {
            session()->put($this->unlockSessionKey($profile), true);

            return redirect()->route('scan.show', [$clientUrl, $profileId]);
        }

        if ($profile->password === $password) {
            session()->put($this->unlockSessionKey($profile), true);

            return redirect()->route('scan.show', [$clientUrl, $profileId]);
        }

        return back()->withErrors(['password' => 'Incorrect password.']);
    }

    public function storeVisitor(Request $request, string $clientUrl, int $profileId): RedirectResponse
    {
        $profile = $this->resolveProfile($clientUrl, $profileId);

        $validated = $request->validate([
            'user_name' => ['nullable', 'string', 'max:255'],
            'user_email' => ['nullable', 'email', 'max:255'],
            'user_mobile' => ['nullable', 'string', 'max:50'],
        ]);

        VisitorContact::query()->create([
            'profile_id' => $profile->id,
            'user_name' => $validated['user_name'] ?? null,
            'user_email' => $validated['user_email'] ?? null,
            'user_mobile' => $validated['user_mobile'] ?? null,
            'entry_date' => now(),
        ]);

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

        $displayOnlyTypes = [2, 11, 13, 14, 20, 21, 23];
        $sessionId = $validated['session_id'] ?? (string) Str::uuid();

        $answerMap = $validated['answers'] ?? [];

        foreach ($validated['answers_sig_text'] ?? [] as $questionId => $sigText) {
            if (filled($sigText) && empty($answerMap[$questionId])) {
                $answerMap[$questionId] = $sigText;
            }
        }

        foreach ($validated['answers_meta'] ?? [] as $questionId => $meta) {
            if (! is_array($meta)) {
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

        $this->markParticipatedParticipants($profile, $questions, $savedAnswers);
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
     * @param  array<int, string>  $savedAnswers
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
            $name = trim((string) ($savedAnswers[$question->question_id] ?? ''));

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

        foreach ($recipients as $email) {
            try {
                Mail::raw($body, function ($message) use ($email, $subject): void {
                    $message->to($email)->subject($subject);
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
}
