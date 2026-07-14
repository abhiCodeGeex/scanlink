<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Models\FormBuilderAnswer;
use App\Models\Profile;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

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

    /** @var Collection<int, object> */
    public Collection $sessions;

    public static function getNavigationGroup(): ?string
    {
        return 'Forms';
    }

    public function mount(): void
    {
        $this->sessions = collect();
        $firstProfileId = $this->clientProfileOptions()->keys()->first();

        if ($firstProfileId) {
            $this->loadSessions((int) $firstProfileId);
        }
    }

    public function updatedSelectedProfileId(?int $profileId): void
    {
        if ($profileId) {
            $this->loadSessions($profileId);
        }
    }

    protected function loadSessions(int $profileId): void
    {
        $client = $this->requireClient();

        Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->findOrFail($profileId);

        $this->selectedProfileId = $profileId;

        $this->sessions = FormBuilderAnswer::query()
            ->where('profile_id', $profileId)
            ->whereNotNull('session_id')
            ->selectRaw('session_id, MIN(date_time) as submitted_at, COUNT(*) as answer_count')
            ->groupBy('session_id')
            ->orderByDesc('submitted_at')
            ->get();
    }

    public function deleteSession(string $sessionId): void
    {
        $client = $this->requireClient();

        FormBuilderAnswer::query()
            ->whereHas('profile', fn ($q) => $q->where('client_id', $client->id))
            ->where('session_id', $sessionId)
            ->delete();

        if ($this->selectedProfileId) {
            $this->loadSessions($this->selectedProfileId);
        }
    }

    /**
     * @return Collection<int|string, string>
     */
    public function clientProfileOptions(): Collection
    {
        $client = $this->currentClient();

        if (! $client) {
            return collect();
        }

        return Profile::query()
            ->where('client_id', $client->id)
            ->where('form_active', true)
            ->active()
            ->orderBy('name')
            ->pluck('name', 'id');
    }
}
