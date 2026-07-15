<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\Client;
use App\Models\FormBuilderAnswer;
use App\Models\FormBuilderQuestion;
use App\Models\Profile;
use App\Models\VisitorContact;
use App\Services\AnalyticsApiService;
use App\Services\ProfileQrService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

    public function storeFormAnswer(Request $request, string $clientUrl, int $profileId): RedirectResponse
    {
        $profile = $this->resolveProfile($clientUrl, $profileId);

        $validated = $request->validate([
            'answers' => ['nullable', 'array'],
            'answers.*' => ['nullable'],
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

        $displayOnlyTypes = [2, 13, 14, 20, 21, 23];
        $sessionId = $validated['session_id'] ?? (string) Str::uuid();
        $nextAnswerId = (int) FormBuilderAnswer::query()->max('answer_id');

        $answerMap = $validated['answers'] ?? [];

        foreach ($validated['answers_sig_text'] ?? [] as $questionId => $sigText) {
            if (filled($sigText) && empty($answerMap[$questionId])) {
                $answerMap[$questionId] = $sigText;
            }
        }

        /** @var array<int, UploadedFile|null> $files */
        $files = $request->file('answers_file') ?? [];

        foreach ($files as $questionId => $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $path = $file->store('form-uploads/'.$profile->id, 'public');
                $answerMap[$questionId] = $path;
            }
        }

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

            $nextAnswerId++;
            FormBuilderAnswer::query()->create([
                'answer_id' => $nextAnswerId,
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
        }

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
}
