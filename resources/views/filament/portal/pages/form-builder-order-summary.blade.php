<x-filament-panels::page>
    <div class="legacy-purchase-page">
        <link rel="stylesheet" href="{{ asset('styles/style.css') }}">
        <style>
            .fi-page-header { display: none !important; }
            .legacy-purchase-page .proceed-btn {
                background: #008401 !important;
                color: #fff !important;
                border: 0 !important;
                border-radius: 6px !important;
                cursor: pointer;
                min-width: 140px;
                height: 40px;
                font-weight: 700;
            }
        </style>

        <section id="content-ordersummary">
            <h2 class="page-title">Order Summary</h2>
            <div class="SectionTitleBox-orderSummary">
                <ul class="OrderSummaryListing">
                    <li class="Title">Item</li>
                    <li>
                        <div class="subscription-ordersummary">${{ number_format($this->totalAmount(), 2) }} AUD</div>
                        <div>Scanlink Form Builder Activation</div>
                    </li>
                    <li>
                        <div class="total-subscription-ordersummary">${{ number_format($this->totalAmount(), 2) }} (includes GST)</div>
                        <div><b>Total</b></div>
                    </li>
                    <li class="Title">Secure Payment Option</li>
                    <li>
                        <div class="RadioInvoice">
                            <div class="AgreeRadio">
                                <input id="paymentoption" type="radio" checked disabled>
                            </div>
                            <div class="AgreeRadioText">
                                By Invoice <br>
                                ScanLink will mail an invoice to you. Terms are 14 days.
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="AgreeWithCondition">
                            <input id="termscondition" type="checkbox" wire:model.live="agreeTerms">
                            I agree with <a href="{{ url('/terms') }}" target="_blank">terms &amp; condition</a>.
                            <br><br>
                            <button
                                type="button"
                                id="proceed"
                                class="proceed-btn"
                                wire:click="proceed"
                                wire:loading.attr="disabled"
                            >PROCEED</button>
                        </div>
                    </li>
                </ul>
            </div>
        </section>
    </div>
</x-filament-panels::page>
