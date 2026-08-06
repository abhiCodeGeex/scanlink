<?php

namespace App\Services;

use App\Models\AnaItemAnalytics;
use App\Models\FormBuilderAnswer;
use App\Models\FormBuilderQuestion;
use App\Models\FormBuilderQuestionOption;
use App\Models\Profile;
use App\Support\FormBuilderMedia;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CumulativeAnalyticsBuilder
{
    private const CHART_COLORS = [
        '#3366CC', '#DC3912', '#FF9900', '#109618', '#990099',
        '#0099C6', '#DD4477', '#66AA00', '#B82E2E', '#316395',
    ];

    /**
     * @param  Collection<int, Profile>  $profiles
     * @return array{
     *     profiles: list<array{id: int, name: string}>,
     *     form_submission_count: int,
     *     form_charts: list<array{title: string, slices: list<array{label: string, value: int, color: string}>}>,
     *     scan_total: int,
     *     country_bars: list<array{label: string, value: int}>,
     *     device_bars: list<array{label: string, value: int}>,
     *     location_rows: list<array{profile_id: int, profile_name: string, date: string, location: string, device: string, scan_type: string}>
     * }
     */
    public function build(Collection $profiles): array
    {
        $profileIds = $profiles->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
        $nameById = $profiles->mapWithKeys(function (Profile $p) {
            $name = filled(trim((string) ($p->code_profile_name ?? '')))
                ? (string) $p->code_profile_name
                : (string) ($p->name ?? '');

            return [$p->id => $name !== '' ? $name : ('Profile #'.$p->id)];
        });

        $profileSummaries = $profiles->map(fn (Profile $p) => [
            'id' => (int) $p->id,
            'name' => (string) $nameById[$p->id],
        ])->values()->all();

        return [
            'profiles' => $profileSummaries,
            'form_submission_count' => $this->formSubmissionCount($profileIds),
            'form_charts' => $this->formCharts($profileIds, $profiles),
            'scan_total' => $this->scanTotal($profileIds),
            'country_bars' => $this->groupBars($profileIds, 'country_name', 12),
            'device_bars' => $this->deviceBars($profileIds, 12),
            'location_rows' => $this->locationRows($profileIds, $nameById, 300),
        ];
    }

    /**
     * Legacy getCumulativeProfile: cumulative analytics can only be generated for
     * profiles that share an identical form structure. Returns which selected profiles
     * are incompatible with the first (reference) profile.
     *
     * @param  Collection<int, Profile>  $profiles
     * @return array{ok: bool, message: ?string, incompatible: list<int>}
     */
    public function validateCompatibility(Collection $profiles): array
    {
        $profiles = $profiles->values();

        if ($profiles->count() < 2) {
            return ['ok' => false, 'message' => 'Please select more than one profile.', 'incompatible' => []];
        }

        if (! Schema::hasTable('form_builder_question')) {
            return ['ok' => true, 'message' => null, 'incompatible' => []];
        }

        $reference = $this->formSignature($profiles->first());
        $incompatible = [];

        foreach ($profiles->slice(1) as $profile) {
            if ($this->formSignature($profile) !== $reference) {
                $incompatible[] = (int) $profile->id;
            }
        }

        if ($incompatible !== []) {
            $ids = implode(',', $incompatible);

            return [
                'ok' => false,
                'message' => "Cumulative analytics cannot be calculated because code profile No. '{$ids}' not compatible with the selected code profile group. Cumulative reports can only be generated for code profiles with identical forms.",
                'incompatible' => $incompatible,
            ];
        }

        return ['ok' => true, 'message' => null, 'incompatible' => []];
    }

    /**
     * Structural fingerprint of a profile's form: the set of type-2 label texts and the
     * multiset of analytics questions (type + sorted options). Identical forms → identical
     * signatures regardless of ordering/whitespace/case.
     *
     * @return array{text: list<string>, analytics: list<string>}
     */
    protected function formSignature(Profile $profile): array
    {
        $query = FormBuilderQuestion::query()->where('profile_id', $profile->id);

        if (Schema::hasColumn('form_builder_question', 'is_deleted')) {
            $query->where('is_deleted', 0);
        }

        $questions = $query->orderBy('question_order')->get();

        $text = $questions
            ->where('question_type_id', 2)
            ->map(fn (FormBuilderQuestion $q): string => $this->normalizeSignatureValue(strip_tags((string) $q->question_text)))
            ->filter(fn (string $v): bool => $v !== '')
            ->sort()
            ->values()
            ->all();

        // Legacy getCumulativeProfile compared option sets for radio(3), checkbox(4),
        // dropdown(5), scale(6) and grid(7) — not just the analytics-pie types [3,5,6].
        $analytics = $questions
            ->whereIn('question_type_id', [3, 4, 5, 6, 7])
            ->map(function (FormBuilderQuestion $q): string {
                $opts = array_map(
                    fn ($o): string => $this->normalizeSignatureValue((string) $o),
                    $this->questionOptions($q),
                );
                sort($opts);

                return (int) $q->question_type_id.'|'.implode('::', $opts);
            })
            ->sort()
            ->values()
            ->all();

        // Legacy also required an identical total field count (get_total_fields_count).
        return ['text' => $text, 'analytics' => $analytics, 'count' => $questions->count()];
    }

    protected function normalizeSignatureValue(string $value): string
    {
        return mb_strtolower(trim((string) preg_replace('/\s+/', ' ', $value)));
    }

    /**
     * @param  list<int>  $profileIds
     */
    protected function formSubmissionCount(array $profileIds): int
    {
        if ($profileIds === [] || ! Schema::hasTable('form_builder_answers')) {
            return 0;
        }

        return (int) FormBuilderAnswer::query()
            ->whereIn('profile_id', $profileIds)
            ->whereNotNull('session_id')
            ->where('session_id', '!=', '')
            ->selectRaw('COUNT(DISTINCT session_id) as aggregate')
            ->value('aggregate');
    }

    /**
     * @param  list<int>  $profileIds
     * @param  Collection<int, Profile>  $profiles
     * @return list<array{title: string, slices: list<array{label: string, value: int, color: string}>}>
     */
    protected function formCharts(array $profileIds, Collection $profiles): array
    {
        if ($profileIds === [] || ! Schema::hasTable('form_builder_question')) {
            return [];
        }

        // Legacy uses the first selected profile's analytics questions as the template,
        // then sums matching option answers across all selected profiles.
        $templateProfile = $profiles->first();
        if (! $templateProfile) {
            return [];
        }

        $questions = FormBuilderQuestion::query()
            ->with('options')
            ->where('profile_id', $templateProfile->id)
            ->whereIn('question_type_id', [3, 5, 6])
            ->orderBy('question_order')
            ->get();

        if ($questions->isEmpty()) {
            // Fall back: any analytics question on any selected profile (grouped by text).
            $questions = FormBuilderQuestion::query()
                ->with('options')
                ->whereIn('profile_id', $profileIds)
                ->whereIn('question_type_id', [3, 5, 6])
                ->orderBy('question_order')
                ->get()
                ->unique(fn (FormBuilderQuestion $q) => $q->question_type_id.'|'.mb_strtolower(trim((string) $q->question_text)))
                ->values();
        }

        $charts = [];

        foreach ($questions as $question) {
            $options = $this->questionOptions($question);
            if ($options === []) {
                continue;
            }

            $title = $this->questionTitle($question, (int) $templateProfile->id);
            $slices = [];

            foreach (array_values($options) as $index => $option) {
                $count = $this->answerCountForOption($profileIds, $question, (string) $option);
                $slices[] = [
                    'label' => (string) $option,
                    'value' => $count,
                    'color' => self::CHART_COLORS[$index % count(self::CHART_COLORS)],
                ];
            }

            if (array_sum(array_column($slices, 'value')) === 0 && count($slices) === 0) {
                continue;
            }

            $charts[] = [
                'title' => $title,
                'slices' => $slices,
            ];
        }

        return $charts;
    }

    /**
     * @return list<string|int>
     */
    protected function questionOptions(FormBuilderQuestion $question): array
    {
        $typeId = (int) $question->question_type_id;

        if ($typeId === 6) {
            $from = FormBuilderQuestionOption::query()
                ->where('question_id', $question->question_id)
                ->where('question_option_type_id', 1)
                ->value('option_name');
            $to = FormBuilderQuestionOption::query()
                ->where('question_id', $question->question_id)
                ->where('question_option_type_id', 2)
                ->value('option_name');

            if (! is_numeric($from) || ! is_numeric($to)) {
                return [];
            }

            $from = (int) $from;
            $to = (int) $to;
            if ($from > $to) {
                [$from, $to] = [$to, $from];
            }

            return range($from, $to);
        }

        // Choice options are often stored in question_text as ::: separated values.
        $text = (string) ($question->question_text ?? '');
        if (str_contains($text, ':::')) {
            return array_values(array_filter(array_map('trim', explode(':::', $text)), fn ($v) => $v !== ''));
        }

        return FormBuilderMedia::choiceOptions($question);
    }

    protected function questionTitle(FormBuilderQuestion $question, int $fallbackProfileId): string
    {
        $order = (int) ($question->question_order ?? 0);
        $profileId = (int) ($question->profile_id ?: $fallbackProfileId);

        // Legacy: preceding type-2 text question is the display title.
        $label = FormBuilderQuestion::query()
            ->where('profile_id', $profileId)
            ->where('question_type_id', 2)
            ->where('question_order', '<', $order)
            ->orderByDesc('question_order')
            ->value('question_text');

        $label = trim(strip_tags((string) $label));
        if ($label !== '') {
            return $label;
        }

        $own = FormBuilderMedia::choiceLabel($question);
        if ($own !== '') {
            return $own;
        }

        return 'Question #'.$question->question_id;
    }

    /**
     * @param  list<int>  $profileIds
     */
    protected function answerCountForOption(array $profileIds, FormBuilderQuestion $template, string $option): int
    {
        if (! Schema::hasTable('form_builder_answers')) {
            return 0;
        }

        // Match answers on any selected profile for questions of the same type + option text.
        // Prefer exact question_id matches when the same question exists on multiple profiles.
        $questionIds = FormBuilderQuestion::query()
            ->whereIn('profile_id', $profileIds)
            ->where('question_type_id', (int) $template->question_type_id)
            ->where(function ($q) use ($template): void {
                $q->where('question_id', $template->question_id)
                    ->orWhere('question_text', (string) $template->question_text)
                    ->orWhere('question_order', (int) $template->question_order);
            })
            ->pluck('question_id')
            ->all();

        if ($questionIds === []) {
            $questionIds = [(int) $template->question_id];
        }

        return (int) FormBuilderAnswer::query()
            ->whereIn('profile_id', $profileIds)
            ->whereIn('question_id', $questionIds)
            ->where('question_answer', $option)
            ->count();
    }

    /**
     * @param  list<int>  $profileIds
     */
    protected function scanTotal(array $profileIds): int
    {
        if ($profileIds === [] || ! Schema::hasTable('ana_item_analytics')) {
            return 0;
        }

        $ids = array_map('strval', $profileIds);

        return (int) AnaItemAnalytics::query()->whereIn('id_item', $ids)->count();
    }

    /**
     * @param  list<int>  $profileIds
     * @return list<array{label: string, value: int}>
     */
    protected function groupBars(array $profileIds, string $column, int $limit): array
    {
        if ($profileIds === [] || ! Schema::hasTable('ana_item_analytics')) {
            return [];
        }

        $ids = array_map('strval', $profileIds);

        return AnaItemAnalytics::query()
            ->select($column.' as label', DB::raw('COUNT(*) as value'))
            ->whereIn('id_item', $ids)
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->groupBy($column)
            ->orderByDesc('value')
            ->limit($limit)
            ->get()
            ->map(fn ($r) => ['label' => (string) $r->label, 'value' => (int) $r->value])
            ->all();
    }

    /**
     * @param  list<int>  $profileIds
     * @return list<array{label: string, value: int}>
     */
    protected function deviceBars(array $profileIds, int $limit): array
    {
        if ($profileIds === [] || ! Schema::hasTable('ana_item_analytics')) {
            return [];
        }

        $ids = array_map('strval', $profileIds);
        $platforms = Schema::hasTable('ana_platforms')
            ? DB::table('ana_platforms')->pluck('platform_name', 'id')
            : collect();

        $rows = AnaItemAnalytics::query()
            ->select('id_platform', DB::raw('COUNT(*) as value'))
            ->whereIn('id_item', $ids)
            ->groupBy('id_platform')
            ->orderByDesc('value')
            ->limit($limit)
            ->get();

        return $rows->map(function ($r) use ($platforms) {
            $id = $r->id_platform;
            $label = 'Unknown';
            if ($id !== null && $id !== '' && is_numeric($id)) {
                $label = (string) ($platforms[(int) $id] ?? ('Platform '.$id));
            } elseif (filled($id)) {
                $label = (string) $id;
            }

            return ['label' => $label, 'value' => (int) $r->value];
        })->all();
    }

    /**
     * @param  list<int>  $profileIds
     * @param  Collection<int, string>  $nameById
     * @return list<array{profile_id: int, profile_name: string, date: string, location: string, device: string, scan_type: string}>
     */
    protected function locationRows(array $profileIds, Collection $nameById, int $limit): array
    {
        if ($profileIds === [] || ! Schema::hasTable('ana_item_analytics')) {
            return [];
        }

        $ids = array_map('strval', $profileIds);
        $platforms = Schema::hasTable('ana_platforms')
            ? DB::table('ana_platforms')->pluck('platform_name', 'id')
            : collect();
        $devices = Schema::hasTable('ana_devices')
            ? DB::table('ana_devices')->pluck('device_name', 'id')
            : collect();

        return AnaItemAnalytics::query()
            ->whereIn('id_item', $ids)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(function (AnaItemAnalytics $row) use ($nameById, $platforms, $devices) {
                $pid = (int) $row->id_item;
                $platform = '';
                if ($row->id_platform !== null && $row->id_platform !== '' && is_numeric($row->id_platform)) {
                    $platform = (string) ($platforms[(int) $row->id_platform] ?? '');
                }
                $device = '';
                if ($row->id_device !== null && $row->id_device !== '' && is_numeric($row->id_device)) {
                    $device = (string) ($devices[(int) $row->id_device] ?? '');
                }

                return [
                    'profile_id' => $pid,
                    'profile_name' => (string) ($nameById[$pid] ?? ('#'.$pid)),
                    'date' => $row->created_at ? $row->created_at->format('Y-m-d H:i') : '',
                    'location' => trim(implode(', ', array_filter([
                        (string) $row->city_name,
                        (string) $row->region_name,
                        (string) $row->country_name,
                    ]))),
                    'device' => trim(implode(' / ', array_filter([$platform, $device]))) ?: 'Unknown',
                    'scan_type' => strtolower((string) ($row->scan_type ?? '')) === 'gps' ? 'GPS' : 'IP',
                ];
            })
            ->all();
    }
}
