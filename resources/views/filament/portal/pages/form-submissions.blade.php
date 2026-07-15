<x-filament-panels::page>
    @php
        $sessions = $this->paginatedSessions();
    @endphp

    <style>
        .fs-root { --fs-green: #008C00; }
        .fs-card { border-radius: 12px; border: 1px solid rgb(229 231 235); background: #fff; padding: 1.25rem; box-shadow: 0 1px 3px rgb(0 0 0 / 0.06); }
        .dark .fs-card { border-color: rgb(55 65 81); background: rgb(17 24 39); }
        .fs-toolbar { display: flex; flex-wrap: wrap; gap: .75rem; align-items: flex-end; justify-content: space-between; margin-bottom: 1rem; }
        .fs-label { display: block; font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: rgb(107 114 128); margin-bottom: .35rem; }
        .fs-select { border-radius: 8px; border: 1px solid rgb(209 213 219); padding: .5rem .75rem; font-size: .875rem; min-width: 240px; }
        .dark .fs-select { border-color: rgb(75 85 99); background: rgb(31 41 55); color: #fff; }
        .fs-btn { display: inline-flex; align-items: center; border-radius: 8px; padding: .45rem .85rem; font-size: .8125rem; font-weight: 600; cursor: pointer; border: none; }
        .fs-btn-primary { background: var(--fs-green); color: #fff; }
        .fs-btn-secondary { background: rgb(243 244 246); color: rgb(55 65 81); }
        .dark .fs-btn-secondary { background: rgb(55 65 81); color: #fff; }
        .fs-btn-danger { background: rgb(220 38 38); color: #fff; }
        .fs-btn-sm { padding: .3rem .55rem; font-size: .75rem; }
        .fs-table-wrap { overflow-x: auto; border-radius: 10px; border: 1px solid rgb(229 231 235); }
        .dark .fs-table-wrap { border-color: rgb(55 65 81); }
        .fs-table { width: 100%; border-collapse: collapse; font-size: .8125rem; }
        .fs-table thead { background: rgb(0 140 0 / .08); }
        .dark .fs-table thead { background: rgb(0 140 0 / .15); }
        .fs-table th { padding: .65rem .75rem; text-align: left; font-weight: 700; font-size: .6875rem; text-transform: uppercase; letter-spacing: .04em; color: var(--fs-green); white-space: nowrap; }
        .fs-table td { padding: .65rem .75rem; border-top: 1px solid rgb(229 231 235); vertical-align: top; }
        .dark .fs-table td { border-color: rgb(55 65 81); }
        .fs-table tbody tr:hover { background: rgb(0 140 0 / .03); }
        .fs-detail { background: rgb(249 250 251); padding: 1rem; }
        .dark .fs-detail { background: rgb(31 41 55); }
        .fs-detail-grid { display: grid; grid-template-columns: 1fr 2fr; gap: .35rem .75rem; font-size: .8125rem; }
        .fs-detail-grid dt { font-weight: 600; color: rgb(75 85 99); }
        .fs-pagination { display: flex; gap: .5rem; align-items: center; justify-content: flex-end; margin-top: 1rem; font-size: .8125rem; }
        .fs-log-col { max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    </style>

    <div class="fs-root space-y-4">
        <div class="fs-card">
            <div class="fs-toolbar">
                <div>
                    <label class="fs-label">Profile</label>
                    <select wire:model.live="selectedProfileId" class="fs-select">
                        <option value="">Select profile</option>
                        @foreach ($this->clientProfileOptions() as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                @if ($selectedProfileId)
                    <button type="button" class="fs-btn fs-btn-primary" wire:click="exportCsv">
                        Export CSV
                    </button>
                @endif
            </div>
        </div>

        @if ($sessions && $sessions->count() > 0)
            <div class="fs-card" style="padding:0;">
                <div class="fs-table-wrap">
                    <table class="fs-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date / Time</th>
                                @foreach ($logQuestions as $logQ)
                                    <th class="fs-log-col" title="{{ $logQ->log_columntitle ?: $logQ->question_text }}">
                                        {{ \Illuminate\Support\Str::limit($logQ->log_columntitle ?: $logQ->question_text, 24) }}
                                    </th>
                                @endforeach
                                <th>Answers</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sessions as $index => $session)
                                @php
                                    $rowNum = ($sessions->currentPage() - 1) * $sessions->perPage() + $index + 1;
                                @endphp
                                <tr wire:key="session-{{ $session->session_id }}">
                                    <td>{{ $rowNum }}</td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($session->submitted_at)->format('d M Y H:i') }}</td>
                                    @foreach ($logQuestions as $logQ)
                                        <td class="fs-log-col" title="{{ $this->answerForSession($session, $logQ->question_id) }}">
                                            {{ \Illuminate\Support\Str::limit($this->answerForSession($session, $logQ->question_id), 40) }}
                                        </td>
                                    @endforeach
                                    <td>{{ $session->answer_count }}</td>
                                    <td style="text-align:right; white-space:nowrap;">
                                        <button type="button" class="fs-btn fs-btn-secondary fs-btn-sm" wire:click="viewSession('{{ $session->session_id }}')">
                                            {{ $viewSessionId === $session->session_id ? 'Hide' : 'View' }}
                                        </button>
                                        <button type="button" class="fs-btn fs-btn-danger fs-btn-sm" wire:click="deleteSession('{{ $session->session_id }}')" wire:confirm="Delete this submission?">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                @if ($viewSessionId === $session->session_id)
                                    <tr wire:key="detail-{{ $session->session_id }}">
                                        <td colspan="{{ 4 + $logQuestions->count() }}" class="fs-detail">
                                            <strong style="color:var(--fs-green); display:block; margin-bottom:.5rem;">Submission detail</strong>
                                            <dl class="fs-detail-grid">
                                                @foreach ($this->sessionAnswers($session->session_id) as $answer)
                                                    <dt>{{ $answer->question?->question_text ?? 'Question #'.$answer->question_id }}</dt>
                                                    <dd>{{ $answer->question_answer ?: '—' }}</dd>
                                                @endforeach
                                            </dl>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($sessions->hasPages())
                    <div class="fs-pagination" style="padding:1rem;">
                        <span>Page {{ $sessions->currentPage() }} of {{ $sessions->lastPage() }} ({{ $sessions->total() }} submissions)</span>
                        @if ($sessions->onFirstPage())
                            <span class="fs-btn fs-btn-secondary fs-btn-sm" style="opacity:.5;">Previous</span>
                        @else
                            <button type="button" class="fs-btn fs-btn-secondary fs-btn-sm" wire:click="goToPage({{ $sessions->currentPage() - 1 }})">Previous</button>
                        @endif
                        @if ($sessions->hasMorePages())
                            <button type="button" class="fs-btn fs-btn-secondary fs-btn-sm" wire:click="goToPage({{ $sessions->currentPage() + 1 }})">Next</button>
                        @else
                            <span class="fs-btn fs-btn-secondary fs-btn-sm" style="opacity:.5;">Next</span>
                        @endif
                    </div>
                @endif
            </div>
        @elseif ($selectedProfileId)
            <div class="fs-card">
                <p style="font-size:.875rem; color:rgb(107 114 128);">No form submissions yet for this profile.</p>
            </div>
        @endif
    </div>

</x-filament-panels::page>
