<x-filament-panels::page>
    <style>
        .sl-gs {
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
            width: 100%;
            max-width: 980px;
            margin: 0;
            padding: 0 0 1.5rem;
            box-sizing: border-box;
        }
        .sl-gs__title {
            display: flex;
            align-items: center;
            gap: 6px;
            margin: 0 0 12px;
            font-size: 13px;
            font-weight: bold;
            color: #e36c09;
        }
        .sl-gs__title-icon {
            display: inline-block;
            width: 0;
            height: 0;
            border-top: 5px solid transparent;
            border-bottom: 5px solid transparent;
            border-left: 8px solid #333;
            margin-right: 2px;
        }
        .sl-gs__msg {
            font-size: 12px;
            padding: 2px 0 10px;
        }
        .sl-gs__msg--success { color: #35D10A; }
        .sl-gs__msg--error { color: #F00; }
        .sl-gs__box {
            width: 100%;
            border: 1px solid #c8c8c8;
            border-radius: 4px;
            background: #fff;
            overflow: hidden;
            box-sizing: border-box;
        }
        .sl-gs__required-bar {
            background: #6b6b6b;
            color: #000;
            font-size: 12px;
            font-weight: bold;
            text-align: right;
            padding: 6px 10px;
        }
        .sl-gs__required-bar span { color: #ff0000; }
        .sl-gs__table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        .sl-gs__table td {
            padding: 8px 10px;
            vertical-align: middle;
            font-size: 13px;
        }
        .sl-gs__label {
            width: 25%;
            text-align: right;
            white-space: nowrap;
            font-weight: bold;
            color: #333;
        }
        .sl-gs__label .req { color: #ff0000; }
        .sl-gs__field {
            width: 75%;
            text-align: left;
        }
        .sl-gs__field input[type="text"],
        .sl-gs__field input[type="password"] {
            width: 98%;
            max-width: 640px;
            box-sizing: border-box;
            border: 1px solid #bdbdbd;
            border-radius: 3px;
            padding: 6px 8px;
            font-size: 13px;
            color: #333;
            background: #fff;
        }
        .sl-gs__btn {
            display: inline-block;
            background: linear-gradient(to bottom, #9a9a9a 0%, #6f6f6f 100%);
            color: #fff !important;
            border: 1px solid #5a5a5a;
            border-radius: 4px;
            padding: 7px 16px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1.2;
        }
        .sl-gs__btn:hover {
            background: linear-gradient(to bottom, #a8a8a8 0%, #777 100%);
        }
    </style>

    <div class="sl-gs">
        <div class="sl-gs__title">
            <span class="sl-gs__title-icon" aria-hidden="true"></span>
            <strong>Global Settings</strong>
        </div>

        @if ($formMessage !== '')
            <div class="sl-gs__msg {{ $formMessageType === 'success' ? 'sl-gs__msg--success' : 'sl-gs__msg--error' }}">
                {{ $formMessage }}
            </div>
        @endif

        <div class="sl-gs__box">
            <div class="sl-gs__required-bar">
                <span>*</span> Indicates Required Information
            </div>

            <table class="sl-gs__table" cellspacing="0" cellpadding="3" border="0">
                <tbody>
                    <tr><td colspan="2" style="height:10px;"></td></tr>

                    @forelse ($this->settingsRows() as $row)
                        @php $key = (string) $row->title; @endphp
                        <tr wire:key="gs-{{ $row->id }}">
                            <td class="sl-gs__label">
                                <span class="req">*</span>&nbsp;{{ \App\Filament\Pages\GlobalSettings::settingLabel($key) }}:
                            </td>
                            <td class="sl-gs__field">
                                <input
                                    type="text"
                                    wire:model="values.{{ $key }}"
                                    id="{{ $key }}"
                                    name="{{ $key }}"
                                    autocomplete="off"
                                >
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="padding:20px;text-align:center;color:#888;">
                                No settings found in the database.
                            </td>
                        </tr>
                    @endforelse

                    <tr>
                        <td class="sl-gs__label">&nbsp;</td>
                        <td class="sl-gs__field">
                            <button type="button" class="sl-gs__btn" wire:click="save" id="btnsettings">
                                Edit Settings
                            </button>
                        </td>
                    </tr>
                    <tr><td colspan="2" style="height:10px;"></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
