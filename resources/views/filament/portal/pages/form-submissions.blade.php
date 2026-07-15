<x-filament-panels::page>
    <div class="space-y-6">
        <div class="max-w-md">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Profile</label>
            <select
                wire:model.live="selectedProfileId"
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-900"
            >
                <option value="">Select profile</option>
                @foreach ($this->clientProfileOptions() as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        @if ($sessions->isNotEmpty())
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Session</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Submitted</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Answers</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($sessions as $session)
                            <tr wire:key="session-{{ $session->session_id }}">
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $session->session_id }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $session->submitted_at }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $session->answer_count }}</td>
                                <td class="px-4 py-3 text-right">
                                    <x-filament::button
                                        color="gray"
                                        size="sm"
                                        wire:click="toggleSession('{{ $session->session_id }}')"
                                    >
                                        {{ $expandedSessionId === $session->session_id ? 'Hide' : 'View' }}
                                    </x-filament::button>
                                    <x-filament::button
                                        color="danger"
                                        size="sm"
                                        wire:click="deleteSession('{{ $session->session_id }}')"
                                        wire:confirm="Delete this submission?"
                                    >
                                        Delete
                                    </x-filament::button>
                                </td>
                            </tr>
                            @if ($expandedSessionId === $session->session_id)
                                <tr wire:key="session-detail-{{ $session->session_id }}">
                                    <td colspan="4" class="bg-gray-50 px-4 py-4 dark:bg-gray-800">
                                        <table class="min-w-full text-sm">
                                            <thead>
                                                <tr>
                                                    <th class="pb-2 text-left font-medium text-gray-600 dark:text-gray-300">Question</th>
                                                    <th class="pb-2 text-left font-medium text-gray-600 dark:text-gray-300">Answer</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($this->sessionAnswers($session->session_id) as $answer)
                                                    <tr class="border-t border-gray-200 dark:border-gray-700">
                                                        <td class="py-2 pr-4 text-gray-900 dark:text-white">
                                                            {{ $answer->question?->question_text ?? 'Question #'.$answer->question_id }}
                                                        </td>
                                                        <td class="py-2 text-gray-600 dark:text-gray-300">{{ $answer->question_answer }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @elseif ($selectedProfileId)
            <p class="text-sm text-gray-500 dark:text-gray-400">No form submissions yet.</p>
        @endif
    </div>
</x-filament-panels::page>
