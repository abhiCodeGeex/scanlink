<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Str;
use Illuminate\View\View;

class MobileProfileController extends Controller
{
    public function show(string $clientUrl, int $profileId, ProfileQrService $qrService, AnalyticsApiService $analytics): View
    {
        $profile = $this->resolveProfile($clientUrl, $profileId);

        if ($profile->isExpired()) {
            return view('scan.expired', compact('profile'));
        }

        if ($profile->protect && ! $this->isUnlocked($profile)) {
            return view('scan.password', compact('profile', 'clientUrl'));
        }

        $analytics->registerUrl($qrService->profileUrl($profile));

        $questions = FormBuilderQuestion::query()
            ->where('profile_id', $profile->id)
            ->orderBy('question_order')
            ->get();

        return view('scan.show', [
            'profile' => $profile,
            'clientUrl' => $clientUrl,
            'questions' => $questions,
            'needsVisitorInfo' => $this->needsVisitorInfo($profile),
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
            'answers' => ['required', 'array'],
            'answers.*' => ['nullable', 'string'],
            'session_id' => ['nullable', 'string', 'max:100'],
            'app_user_firstname' => ['nullable', 'string', 'max:100'],
            'app_user_lastname' => ['nullable', 'string', 'max:100'],
            'app_user_email' => ['nullable', 'email', 'max:255'],
            'app_user_mobile' => ['nullable', 'string', 'max:50'],
        ]);

        $sessionId = $validated['session_id'] ?? (string) Str::uuid();
        $nextAnswerId = (int) FormBuilderAnswer::query()->max('answer_id');

        foreach ($validated['answers'] as $questionId => $answer) {
            if (! filled($answer)) {
                continue;
            }

            $nextAnswerId++;
            FormBuilderAnswer::query()->create([
                'answer_id' => $nextAnswerId,
                'question_id' => (int) $questionId,
                'profile_id' => $profile->id,
                'question_answer' => $answer,
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

    protected function resolveProfile(string $clientUrl, int $profileId): Profile
    {
        $client = Client::query()->where('url', $clientUrl)->firstOrFail();

        return Profile::query()
            ->where('client_id', $client->id)
            ->where('id', $profileId)
            ->active()
            ->firstOrFail();
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
}
