<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Models\FormBuilderAnswer;
use App\Models\FormBuilderQuestion;
use App\Models\Profile;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FormSubmissions extends Page
{
    use InteractsWithClientMembership;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static ?string $navigationLabel = 'Form Submissions';

    protected static ?string $title = 'Form Submissions';

    protected static ?string $slug = 'form-submissions';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.portal.pages.form-submissions';

    public ?int $selectedProfileId = null;

    public ?string $viewSessionId = null;

    public int $page = 1;

    public int $perPage = 20;

    /** @var Collection<int, FormBuilderQuestion> */
    public Collection $logQuestions;

    public static function getNavigationGroup(): ?string
    {
        return 'Forms';
    }

    public static function canAccess(): bool
    {
        return static::memberCanAccessFormSubmissions(static::portalMembership());
    }

    public function mount(): void
    {
        $this->logQuestions = collect();

        $requestedProfile = request()->integer('profile');
        $firstProfileId = $requestedProfile ?: $this->clientProfileOptions()->keys()->first();

        if ($firstProfileId) {
            $this->loadProfile((int) $firstProfileId);
        }
    }

    public function updatedSelectedProfileId(?int $profileId): void
    {
        $this->page = 1;
        $this->viewSessionId = null;

        if ($profileId) {
            $this->loadProfile($profileId);
        }
    }

    public function goToPage(int $page): void
    {
        $this->page = max(1, $page);
    }

    public function viewSession(string $sessionId): void
    {
        $this->viewSessionId = $this->viewSessionId === $sessionId ? null : $sessionId;
    }

    /**
     * @return Collection<int, FormBuilderAnswer>
     */
    public function sessionAnswers(string $sessionId): Collection
    {
        if (! $this->selectedProfileId) {
            return collect();
        }

        return FormBuilderAnswer::query()
            ->with('question')
            ->where('profile_id', $this->selectedProfileId)
            ->where('session_id', $sessionId)
            ->orderBy('question_id')
            ->get();
    }

    public function deleteSession(string $sessionId): void
    {
        $client = $this->requireClient();

        FormBuilderAnswer::query()
            ->whereHas('profile', fn ($q) => $q->where('client_id', $client->id))
            ->where('session_id', $sessionId)
            ->delete();

        if ($this->viewSessionId === $sessionId) {
            $this->viewSessionId = null;
        }

        Notification::make()->title('Submission deleted')->success()->send();
    }

    public function exportCsv(): StreamedResponse
    {
        $profile = $this->resolveProfile();
        $logQuestions = $this->logQuestions;
        $filename = 'form-submissions-'.$profile->id.'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($profile, $logQuestions): void {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

            $headers = ['#', 'Date/Time', 'Session ID'];
            foreach ($logQuestions as $question) {
                $headers[] = $question->log_columntitle ?: $question->question_text;
            }
            $headers[] = 'All answers';
            fputcsv($out, $headers);

            $sessions = $this->allSessionsQuery($profile->id)->get();
            $rowNum = 0;

            foreach ($sessions as $session) {
                $rowNum++;
                $answers = FormBuilderAnswer::query()
                    ->with('question')
                    ->where('profile_id', $profile->id)
                    ->where('session_id', $session->session_id)
                    ->get()
                    ->keyBy('question_id');

                $row = [
                    $rowNum,
                    $session->submitted_at,
                    $session->session_id,
                ];

                foreach ($logQuestions as $question) {
                    $row[] = $answers->get($question->question_id)?->question_answer ?? '';
                }

                $allAnswers = $answers->map(fn (FormBuilderAnswer $a): string => ($a->question?->question_text ?? 'Q'.$a->question_id).': '.$a->question_answer)->implode(' | ');
                $row[] = $allAnswers;

                fputcsv($out, $row);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function printSessionUrl(string $sessionId): string
    {
        return route('portal.form-submissions.print', [
            'sessionId' => $sessionId,
            'profile' => $this->selectedProfileId,
        ]);
    }

    public static function downloadSessionHtml(int $profileId, string $sessionId): Response
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $member = $user->clientMemberships()
            ->where('status', true)
            ->orderByDesc('role')
            ->first();

        abort_unless($member, 403);

        $profile = Profile::query()
            ->where('client_id', $member->client_id)
            ->active()
            ->findOrFail($profileId);

        $answers = FormBuilderAnswer::query()
            ->with('question')
            ->where('profile_id', $profile->id)
            ->where('session_id', $sessionId)
            ->orderBy('question_id')
            ->get();

        abort_if($answers->isEmpty(), 404);

        $html = view('filament.portal.pages.form-submission-print', [
            'profile' => $profile,
            'sessionId' => $sessionId,
            'answers' => $answers,
            'submittedAt' => $answers->min('date_time'),
        ])->render();

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    /**
     * @return LengthAwarePaginator<object>|null
     */
    public function paginatedSessions(): ?LengthAwarePaginator
    {
        if (! $this->selectedProfileId) {
            return null;
        }

        $query = $this->allSessionsQuery($this->selectedProfileId);

        return $query->paginate($this->perPage, ['*'], 'page', $this->page);
    }

    /**
     * Profiles that can receive / already have form submissions.
     *
     * @return Collection<int|string, string>
     */
    public function clientProfileOptions(): Collection
    {
        $client = $this->currentClient();

        if (! $client) {
            return collect();
        }

        return Profile::selectOptionsForClient((int) $client->id, function ($query): void {
            $query->where(function ($q): void {
                $q->where('form_active', true)
                    ->orWhere('form_is_enable', true)
                    ->orWhereHas('formQuestions')
                    ->orWhereHas('formAnswers');
            });
        });
    }

    public function answerForSession(object $session, int $questionId): string
    {
        if (! $this->selectedProfileId) {
            return '';
        }

        static $cache = [];

        $key = $session->session_id;

        if (! isset($cache[$key])) {
            $cache[$key] = FormBuilderAnswer::query()
                ->where('profile_id', $this->selectedProfileId)
                ->where('session_id', $session->session_id)
                ->pluck('question_answer', 'question_id');
        }

        return (string) ($cache[$key][$questionId] ?? '');
    }

    protected function loadProfile(int $profileId): void
    {
        $client = $this->requireClient();

        Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->findOrFail($profileId);

        $this->selectedProfileId = $profileId;

        $this->logQuestions = FormBuilderQuestion::query()
            ->where('profile_id', $profileId)
            ->where('is_logchecked', true)
            ->orderBy('question_order')
            ->get();
    }

    protected function resolveProfile(): Profile
    {
        $client = $this->requireClient();

        return Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->findOrFail($this->selectedProfileId);
    }

    protected function allSessionsQuery(int $profileId)
    {
        return FormBuilderAnswer::query()
            ->where('profile_id', $profileId)
            ->whereNotNull('session_id')
            ->selectRaw('session_id, MIN(date_time) as submitted_at, COUNT(*) as answer_count')
            ->groupBy('session_id')
            ->orderByDesc('submitted_at');
    }
}
