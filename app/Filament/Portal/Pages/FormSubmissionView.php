<?php

namespace App\Filament\Portal\Pages;

use App\Filament\Portal\Concerns\InteractsWithClientMembership;
use App\Filament\Portal\Resources\Profiles\ProfileResource;
use App\Models\FormBuilderAnswer;
use App\Models\FormBuilderQuestion;
use App\Models\Logo;
use App\Models\Profile;
use App\Support\LegacyEquipmentTypeLabels;
use App\Support\PublicMediaPath;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class FormSubmissionView extends Page
{
    use InteractsWithClientMembership;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Submission Response';

    protected static ?string $title = 'Form Submission Response';

    protected static ?string $slug = 'form-submission-view';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.portal.pages.form-submission-view';

    public ?int $selectedProfileId = null;

    public ?string $sessionId = null;

    public ?string $profileName = null;

    public ?string $logoUrl = null;

    public ?string $submittedAt = null;

    /** @var Collection<int, FormBuilderQuestion> */
    public Collection $questions;

    /** @var Collection<int, FormBuilderAnswer> */
    public Collection $answers;

    public static function canAccess(): bool
    {
        return static::memberCanAccessFormSubmissions(static::portalMembership());
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function getHeader(): ?View
    {
        return view('filament.portal.profiles.mastercode-toolbar', [
            'types' => LegacyEquipmentTypeLabels::navTypes(),
            'activeTab' => null,
            'addCodeUrl' => ProfileResource::getUrl('index'),
            'canAddCode' => false,
            'hideActionBar' => true,
            'hideLegend' => true,
            'readonlyNav' => true,
        ]);
    }

    public function mount(): void
    {
        $this->questions = collect();
        $this->answers = collect();

        $profileId = request()->integer('profile');
        $sessionId = (string) request()->query('session_id', '');

        abort_unless($profileId > 0 && $sessionId !== '', 404);

        $client = $this->requireClient();
        $profile = Profile::query()
            ->where('client_id', $client->id)
            ->active()
            ->findOrFail($profileId);

        $this->selectedProfileId = $profileId;
        $this->sessionId = $sessionId;
        $this->profileName = filled(trim((string) $profile->code_profile_name))
            ? (string) $profile->code_profile_name
            : $profile->displayLabel();

        $logo = Logo::query()->where('profile_id', $profileId)->orderBy('id')->first();
        $this->logoUrl = $logo?->logo_name
            ? PublicMediaPath::url($logo->logo_name)
            : asset('images/logo.png');

        $this->answers = FormBuilderAnswer::query()
            ->where('profile_id', $profileId)
            ->where('session_id', $sessionId)
            ->orderBy('question_id')
            ->get();

        abort_if($this->answers->isEmpty(), 404);

        $this->submittedAt = optional($this->answers->min('date_time'))
            ? \Illuminate\Support\Carbon::parse($this->answers->min('date_time'))->format('d/m/Y H:i')
            : '—';

        $q = FormBuilderQuestion::query()
            ->where('profile_id', $profileId)
            ->orderBy('question_order');

        if (\Illuminate\Support\Facades\Schema::hasColumn('form_builder_question', 'is_deleted')) {
            $q->where(function ($query): void {
                $query->where('is_deleted', false)
                    ->orWhereNull('is_deleted')
                    ->orWhere('is_deleted', 0);
            });
        }

        $this->questions = $q->get();
    }

    public function backUrl(): string
    {
        $from = (string) request()->query('from_date', '');
        $to = (string) request()->query('to_date', '');

        return FormSubmissions::getUrl().'?profile='.$this->selectedProfileId
            .'&from_date='.urlencode($from)
            .'&to_date='.urlencode($to);
    }

    public function answerFor(int $questionId): ?FormBuilderAnswer
    {
        return $this->answers->firstWhere('question_id', $questionId);
    }
}
