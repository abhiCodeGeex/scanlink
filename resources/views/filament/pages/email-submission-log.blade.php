<x-filament-panels::page>
    <style>
        .esl-panel {
            border: 1px solid rgb(209 213 219);
            border-radius: 8px;
            background: #fff;
            overflow: hidden;
        }
        .dark .esl-panel {
            border-color: rgb(75 85 99);
            background: rgb(17 24 39);
        }
        .esl-panel-bar {
            background: rgb(107 114 128);
            color: #fff;
            font-size: .75rem;
            font-weight: 600;
            text-align: right;
            padding: .45rem .9rem;
        }
        .esl-panel-body {
            padding: 1.5rem 1.25rem 1.75rem;
        }
        .esl-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .75rem 1.25rem;
            margin-bottom: 1.25rem;
        }
        .esl-field {
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .esl-label {
            font-size: .875rem;
            font-weight: 600;
            color: rgb(55 65 81);
            white-space: nowrap;
        }
        .dark .esl-label { color: rgb(209 213 219); }
        .esl-input {
            min-width: 11rem;
            border-radius: 6px;
            border: 1px solid rgb(209 213 219);
            padding: .4rem .6rem;
            font-size: .875rem;
            background: #fff;
        }
        .dark .esl-input {
            border-color: rgb(75 85 99);
            background: rgb(31 41 55);
            color: #fff;
        }
        .esl-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 6.5rem;
            border-radius: 6px;
            border: 1px solid rgb(156 163 175);
            background: linear-gradient(to bottom, #f3f4f6, #9ca3af);
            color: #fff;
            font-size: .875rem;
            font-weight: 700;
            padding: .4rem 1rem;
            cursor: pointer;
            text-shadow: 0 1px 0 rgb(0 0 0 / .2);
        }
        .esl-btn:hover {
            background: linear-gradient(to bottom, #e5e7eb, #6b7280);
        }
    </style>

    <div class="esl-panel">
        <div class="esl-panel-bar">* Indicates Required Information</div>
        <div class="esl-panel-body">
            <div class="esl-row">
                <div class="esl-field">
                    <label class="esl-label" for="esl-from">From:</label>
                    <input
                        id="esl-from"
                        type="date"
                        class="esl-input"
                        wire:model="dateFrom"
                        required
                    >
                </div>
                <div class="esl-field">
                    <label class="esl-label" for="esl-to">To:</label>
                    <input
                        id="esl-to"
                        type="date"
                        class="esl-input"
                        wire:model="dateTo"
                        required
                    >
                </div>
            </div>
            <button type="button" class="esl-btn" wire:click="getCsv">
                Get CSV
            </button>
        </div>
    </div>
</x-filament-panels::page>
