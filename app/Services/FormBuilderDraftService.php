<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

/**
 * "Draft until Save" support for the legacy Form Builder.
 *
 * The Form Builder edits form_builder_question rows live as the user drags, edits,
 * deletes and reorders elements. To make those changes provisional until the profile's
 * green SAVE, we keep a committed-state snapshot per profile in the session:
 *
 *  - first time the editor opens a profile       -> capture the current state as baseline
 *  - the green SAVE (profile afterSave)          -> re-capture (edits are now committed)
 *  - opening / reloading with uncommitted edits  -> restore the baseline (revert all changes)
 *
 * Snapshots preserve primary keys, so submissions (form_builder_answers, keyed by
 * question_id) stay valid across a restore. These tables carry no FK constraints, so a
 * straight delete + re-insert is safe.
 */
class FormBuilderDraftService
{
    private function key(int $profileId): string
    {
        return 'fb_draft_baseline.'.$profileId;
    }

    public function hasBaseline(int $profileId): bool
    {
        return Session::has($this->key($profileId));
    }

    /**
     * Serialize the committed state (questions + their options) for a profile.
     *
     * @return array{questions: list<array<string, mixed>>, options: list<array<string, mixed>>}
     */
    public function snapshot(int $profileId): array
    {
        $questions = DB::table('form_builder_question')
            ->where('profile_id', $profileId)
            ->get()
            ->map(fn ($row): array => (array) $row)
            ->all();

        $questionIds = array_map(static fn (array $q): int => (int) $q['question_id'], $questions);

        $options = $questionIds === []
            ? []
            : DB::table('form_builder_question_options')
                ->whereIn('question_id', $questionIds)
                ->get()
                ->map(fn ($row): array => (array) $row)
                ->all();

        return ['questions' => $questions, 'options' => $options];
    }

    /** Capture the current DB state as the committed baseline (on open + on Save). */
    public function commit(int $profileId): void
    {
        Session::put($this->key($profileId), $this->snapshot($profileId));
    }

    /** Revert the form to the committed baseline, discarding uncommitted edits. */
    public function restore(int $profileId): void
    {
        $snapshot = Session::get($this->key($profileId));

        if (! is_array($snapshot)) {
            return;
        }

        DB::transaction(function () use ($profileId, $snapshot): void {
            $currentIds = DB::table('form_builder_question')
                ->where('profile_id', $profileId)
                ->pluck('question_id')
                ->all();

            if ($currentIds !== []) {
                DB::table('form_builder_question_options')->whereIn('question_id', $currentIds)->delete();
                DB::table('form_builder_question')->whereIn('question_id', $currentIds)->delete();
            }

            foreach (array_chunk($snapshot['questions'] ?? [], 200) as $chunk) {
                DB::table('form_builder_question')->insert($chunk);
            }

            foreach (array_chunk($snapshot['options'] ?? [], 200) as $chunk) {
                DB::table('form_builder_question_options')->insert($chunk);
            }
        });
    }

    /**
     * On opening the editor: restore if we already hold a baseline (a reload with
     * uncommitted edits), otherwise capture the current state as the baseline.
     */
    public function syncOnOpen(int $profileId): void
    {
        if ($profileId <= 0) {
            return;
        }

        if ($this->hasBaseline($profileId)) {
            $this->restore($profileId);

            return;
        }

        $this->commit($profileId);
    }
}
