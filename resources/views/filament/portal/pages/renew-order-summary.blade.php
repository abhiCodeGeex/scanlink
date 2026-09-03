<x-filament-panels::page>
    <div class="legacy-purchase-page">
        <link rel="stylesheet" href="{{ asset('styles/style.css') }}">
        <style>
            .fi-page-header { display: none !important; }
            .legacy-purchase-page .renew-code-list {
                display: inline-block;
                margin-top: 6px;
                color: #4b5563;
                font-size: 12.5px;
                line-height: 1.5;
                word-break: break-word;
            }
            html.dark .legacy-purchase-page .renew-code-list { color: rgb(156 163 175); }

            .legacy-purchase-page .proceed-btn {
                background: #008401 !important;
                color: #fff !important;
                border: 0 !important;
                border-radius: 6px !important;
                box-shadow: none !important;
                cursor: pointer;
                min-width: 140px;
                height: 40px;
                font-weight: 700;
                text-transform: lowercase;
            }
            #progressbar_renew {
                display: none;
                margin-top: 12px;
                font-weight: 600;
            }
        </style>

        <section id="content-ordersummary">
            <h2 class="page-title">Order Summary</h2>
            <div class="SectionTitleBox-orderSummary">
                <ul class="OrderSummaryListing">
                    <li class="Title">Item</li>
                    <li>
                        <div class="subscription-ordersummary">$ {{ number_format($this->totalAmount(), 2) }} AUD</div>
                        <div style="width:70%;">
                            12 months code subscription<br>
                            @foreach ($this->amountLines() as $line)
                                {{ $line }}<br>
                            @endforeach
                            {{-- Name the codes. A count alone gave the user no way to notice
                                 they were about to be invoiced for the wrong selection. --}}
                            @if ($numbers = $this->profileNumbers())
                                <span class="renew-code-list">
                                    Code{{ count($numbers) === 1 ? '' : 's' }}:
                                    {{ collect($numbers)->map(fn (int $id): string => '#'.$id)->implode(', ') }}
                                </span>
                            @endif
                        </div>
                    </li>
                    <li>
                        <div class="total-subscription-ordersummary">$ {{ number_format($this->totalAmount(), 2) }} (includes GST)</div>
                        <div><b>Total</b></div>
                    </li>
                    <li class="Title">Secure Payment Option</li>
                    <li>
                        <div class="RadioInvoice">
                            <div class="AgreeRadio">
                                <input id="paymentoption" type="radio" checked>
                            </div>
                            <div class="AgreeRadioText">
                                By Invoice <br>
                                ScanLink will mail an invoice to you. Terms are 14 days.
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="AgreeWithCondition">
                            <input id="termscondition" type="checkbox" wire:model.live="agreeTerms">
                            I agree with <a href="{{ url('/terms') }}" target="_blank">terms & condition.</a>
                            <br><br>
                            <button
                                type="button"
                                id="proceed"
                                class="proceed-btn"
                                wire:click="proceed"
                                wire:loading.attr="disabled"
                                x-on:click="document.getElementById('progressbar_renew').style.display='block'"
                            >proceed</button>
                        </div>
                    </li>
                </ul>
            </div>
        </section>
        <div id="progressbar_renew" wire:loading wire:target="proceed">
            Processing order...
        </div>
    </div>
</x-filament-panels::page>
