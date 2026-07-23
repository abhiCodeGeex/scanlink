<x-filament-panels::page>
    @php
        $summary = $this->orderSummary();
    @endphp

    {{-- Do NOT load public/styles/style.css globally — scoped styles only. --}}
    <style>
        .sl-ol {
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0 0 1.5rem;
            box-sizing: border-box;
        }
        .sl-ol__panel {
            background: #fff;
            border: 1px solid #e5ebe5;
            border-radius: 4px;
            padding: 18px 22px 28px;
            box-sizing: border-box;
            overflow: hidden;
        }
        .sl-ol__crumb {
            margin: 0 0 10px;
            font-size: 13px;
            font-weight: bold;
            color: #444;
        }
        .sl-ol__layout {
            display: grid;
            grid-template-columns: minmax(280px, 1fr) minmax(280px, 1fr);
            gap: 24px 30px;
            align-items: start;
        }
        @media (max-width: 900px) {
            .sl-ol__layout { grid-template-columns: 1fr; }
        }
        .sl-ol__left h3 {
            margin: 0 0 10px;
            font-size: 18px;
            color: #333;
        }
        .sl-ol__left p {
            margin: 0 0 18px;
            font-size: 13px;
            line-height: 1.45;
            color: #444;
        }
        .sl-ol__products {
            display: flex;
            flex-wrap: wrap;
            gap: 18px 24px;
            justify-content: flex-start;
        }
        .sl-ol__product {
            width: 215px;
            text-align: center;
        }
        .sl-ol__product--large { width: 237px; }
        .sl-ol__frame {
            margin: 0 auto 10px;
            text-align: center;
            background-repeat: no-repeat;
            background-position: left top;
        }
        .sl-ol__frame--small {
            width: 170px;
            height: 182px;
            padding: 31px 0 0;
            background-image: url("{{ asset('images/label_50_40_shadow.jpg') }}");
            box-sizing: border-box;
        }
        .sl-ol__frame--large {
            width: 197px;
            height: 228px;
            padding: 40px 0 0;
            background-image: url("{{ asset('images/label_100_75_shadow.jpg') }}");
            box-sizing: border-box;
        }
        .sl-ol__frame img {
            display: inline-block;
        }
        .sl-ol__frame--small img {
            width: 99px;
            padding-left: 4px;
            padding-right: 5px;
        }
        .sl-ol__frame--large img {
            width: 120px;
            padding-left: 7px;
            padding-right: 5px;
        }
        .sl-ol__product label {
            display: block;
            font-weight: bold;
            margin: 10px 0;
            font-size: 13px;
        }
        .sl-ol__product input[type="text"],
        .sl-ol__product input[type="number"] {
            width: 80px;
            text-align: center;
            color: #838383;
            border: 1px solid #ccc;
            border-radius: 3px;
            padding: 6px 4px;
            font-size: 13px;
        }
        .sl-ol__qty-hint {
            margin: 8px 0 0;
            font-size: 12px;
            color: #666;
        }
        .sl-ol__right {
            border-left: 1px solid #e5ebe5;
            padding-left: 24px;
        }
        @media (max-width: 900px) {
            .sl-ol__right {
                border-left: 0;
                padding-left: 0;
                border-top: 1px solid #e5ebe5;
                padding-top: 18px;
            }
        }
        .sl-ol__right h3 {
            margin: 0 0 12px;
            font-size: 16px;
        }
        .sl-ol table.listing-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 14px;
        }
        .sl-ol table.listing-table th,
        .sl-ol table.listing-table td {
            border: 1px solid #ccc;
            padding: 8px 6px;
        }
        .sl-ol table.listing-table th {
            background: #e8e8e8;
            text-align: left;
        }
        .sl-ol__btn {
            display: inline-block;
            background: #008901;
            color: #fff !important;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            text-decoration: none !important;
            border: 1px solid #006201;
            border-radius: 5px;
            padding: 9px 28px;
            cursor: pointer;
            line-height: 1.2;
            margin: 4px 0 18px;
        }
        .sl-ol__btn:hover { background: #00a001; }
        .sl-ol__custom-title {
            color: #008401;
            font-size: 20px;
            margin: 8px 0 6px;
            font-weight: bold;
        }
        .sl-ol__custom-body {
            font-size: 13px;
            color: #444;
            margin-bottom: 6px;
        }
        .sl-ol__custom-link a {
            color: #008401;
            font-size: 17px;
            text-decoration: underline;
        }
        .sl-ol__empty {
            text-align: center;
            font-weight: bold;
            color: #5286BE;
            padding: 28px 12px;
        }
        .sl-ol__actions-row {
            margin-top: 8px;
        }
        .sl-ol__btn--ghost {
            background: #fff;
            color: #008901 !important;
            margin-left: 8px;
        }
        .sl-ol__btn--ghost:hover {
            background: #f3fff3;
        }
    </style>

    <div class="sl-ol">
        <div class="sl-ol__panel">
            @if (! $selectedProfileId)
                <div class="sl-ol__empty">Select a profile from Master Code List to order labels.</div>
            @else
                <h1 class="sl-ol__crumb">
                    Order Label &gt;&gt;
                    {{ $typeName ?: 'Code' }} &gt;&gt;
                    {{ filled($profileName) ? ucwords($profileName) : '' }}
                </h1>

                <div class="sl-ol__layout">
                    <section class="sl-ol__left">
                        <h3>Industrial Grade Labels</h3>
                        <p>
                            Our industrial labels are water, UV, chemical and abrasive resistant with a 5 year warranty and rated to withstand temperatures up to 93°C.
                            <br><br>
                            We supply single label orders and print your own equipment packages are also available.
                        </p>

                        <div class="sl-ol__products">
                            <div class="sl-ol__product">
                                <figure class="sl-ol__frame sl-ol__frame--small">
                                    @if ($qrUrl)
                                        <img src="{{ $qrUrl }}" alt="QR label small" class="qr-label-small">
                                    @endif
                                </figure>
                                <label>50 X 40 mm Label</label>
                                <input
                                    type="text"
                                    inputmode="numeric"
                                    wire:model.live="qtySmall"
                                    autocomplete="off"
                                >
                                <p class="sl-ol__qty-hint">Enter Quantity</p>
                            </div>

                            <div class="sl-ol__product sl-ol__product--large">
                                <figure class="sl-ol__frame sl-ol__frame--large">
                                    @if ($qrUrl)
                                        <img src="{{ $qrUrl }}" alt="QR label large" class="qr-label-big">
                                    @endif
                                </figure>
                                <label>100 X 75 mm Label</label>
                                <input
                                    type="text"
                                    inputmode="numeric"
                                    wire:model.live="qtyLarge"
                                    autocomplete="off"
                                >
                                <p class="sl-ol__qty-hint">Enter Quantity</p>
                            </div>
                        </div>
                    </section>

                    <section class="sl-ol__right">
                        <h3>Order Summary :</h3>
                        <table class="listing-table" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1) 50 X 40 mm label</td>
                                    <td align="center">{{ $summary['qty_small'] }}</td>
                                    <td align="center">{{ number_format($priceSmall, 0) }}</td>
                                    <td align="center">{{ number_format($summary['tot_small'], 0) }}</td>
                                </tr>
                                <tr>
                                    <td>2) 100 X 75 mm label</td>
                                    <td align="center">{{ $summary['qty_large'] }}</td>
                                    <td align="center">{{ number_format($priceLarge, 0) }}</td>
                                    <td align="center">{{ number_format($summary['tot_large'], 0) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3"><strong>Total</strong></td>
                                    <td align="center"><strong>{{ number_format($summary['total'], 0) }}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="3">Postage &amp; Handling</td>
                                    <td align="center">{{ number_format($summary['postage'], 0) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2"><strong>Grand Total (inc GST)</strong></td>
                                    <td align="center">AUD</td>
                                    <td align="center"><strong>{{ number_format($summary['grand_total'], 0) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="sl-ol__actions-row">
                            <button type="button" class="sl-ol__btn" wire:click="submitOrder">NEXT</button>
                            <a class="sl-ol__btn sl-ol__btn--ghost" href="{{ $this->returnToListUrl() }}">Return to list</a>
                        </div>

                        <div class="sl-ol__custom-title">Customised Labels</div>
                        <div class="sl-ol__custom-body">
                            We also supply customised industrial labels featuring your logo/design<br>
                            with up to 4 colours.
                        </div>
                        <div class="sl-ol__custom-link">
                            <a href="{{ $this->contactUrl() }}">Contact us to discuss your requirements</a>
                        </div>
                    </section>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
