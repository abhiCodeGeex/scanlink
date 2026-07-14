<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->content }}

        @if ($selectedProfileId && $questions->isNotEmpty())
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Questions</h3>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($questions as $question)
                        <div class="flex items-start justify-between gap-4 py-4" wire:key="question-{{ $question->question_id }}">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ $question->question_order }}. {{ $question->question_text }}
                                </p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $question->questionType?->label ?? $question->questionType?->type ?? 'Question' }}
                                    @if ($question->is_mandatory)
                                        <span class="text-primary-600">(required)</span>
                                    @endif
                                </p>
                            </div>
                            <x-filament::button
                                color="danger"
                                size="sm"
                                wire:click="deleteQuestion({{ $question->question_id }})"
                                wire:confirm="Remove this question?"
                            >
                                Remove
                            </x-filament::button>
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif ($selectedProfileId)
            <p class="text-sm text-gray-500 dark:text-gray-400">No questions yet for this profile.</p>
        @endif
    </div>
</x-filament-panels::page>
