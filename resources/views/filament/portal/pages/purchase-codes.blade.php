<x-filament-panels::page>
    <div class="legacy-purchase-page">
        <link rel="stylesheet" href="{{ asset('styles/style.css') }}">
        <style>
            .fi-page-header { display: none !important; }
            .legacy-purchase-page { font-family: Arial, Helvetica, sans-serif; max-width: 980px; }
            .legacy-purchase-page .clear { clear: both; }
            .legacy-purchase-page .secondstep-title-purchase { color: #008601; margin-top: 20px; }
            .legacy-purchase-page .SectionTitle-purchase-right { width: 150px; float: right; }
            .legacy-purchase-page .tab-btn {
                border: 0;
                width: auto !important;
                min-width: 120px;
                text-align: center;
                display: inline-block;
                float: left;
                text-decoration: none;
                box-sizing: border-box;
                white-space: nowrap;
            }
            .legacy-purchase-page .content-register { display: block; }
            .legacy-purchase-page .content-main-register { width: calc(100% - 50px); }
            .legacy-purchase-page .content-textbox-register { width: 220px; }
            .legacy-purchase-page .content-textbox-register input.text-fi {
                width: 200px;
                height: 28px;
                line-height: 28px;
                border: 1px solid #d8d8d8;
                border-radius: 6px;
                font-size: 14px;
                padding: 0 8px;
            }
            .legacy-purchase-page .content-button-register .text-fi,
            .legacy-purchase-page .secondstep-submit .text-fi,
            .legacy-purchase-page #exit.text-fi {
                background: #008401 !important;
                color: #fff !important;
                border: 0 !important;
                border-radius: 6px !important;
                box-shadow: none !important;
                text-shadow: none !important;
                cursor: pointer;
            }
            .legacy-purchase-page #calculate.text-fi,
            .legacy-purchase-page #calculateRe.text-fi {
                height: 32px;
                min-width: 145px;
                font-weight: 700;
                border-radius: 0 !important;
                margin-left: -4px;
                clip-path: polygon(0 0, 90% 0, 100% 50%, 90% 100%, 0 100%);
                padding-right: 20px;
                text-align: center;
            }
            .legacy-purchase-page .secondstep-submit .text-fi { min-width: 140px; font-weight: 700; height: 40px; }
            .legacy-purchase-page #exit.text-fi { min-width: 100px; height: 38px; font-weight: 700; }
            .legacy-purchase-page .resellerMarginDiff { margin-top: 4px; }
        </style>

        <section>
            <h2 class="page-title">Code Balance</h2>
            <div class="secondstep-title-purchase">
                <div class="SectionTitle-purchase-right">
                    <input type="button" id="exit" name="exit" class="text-fi" value="EXIT" wire:click="exitPage">
                </div>
                <div class="current-code-balance-text">
                    <div class="current-code-balance-text-left">Your current code availability balance</div>
                    <div class="current-code-balance-text-right">{{ $this->availabilityBalance() }}</div>
                </div>
            </div>
            <div class="clear"></div>

            <h3>Enter the amount of codes you would like to activate and click the calculate button to display the total order value</h3>
            <div class="clear"></div>

            <div class="SecondPurchaseBox">
                <a href="javascript:;" id="purchase_code" class="tab-btn {{ $activeTab === 'purchase' ? 'GreenTitlePurchaseCodes' : 'BlackTitlePurchaseCodes' }}" wire:click.prevent="switchTab('purchase')">Purchase Codes</a>
                <a href="javascript:;" id="agency_reseller" class="tab-btn {{ $activeTab === 'reseller' ? 'GreenTitlePurchaseCodes' : 'BlackTitlePurchaseCodes' }}" wire:click.prevent="switchTab('reseller')">Agency/Reseller</a>
                <div class="clear"></div>

                <div class="content-register" id="purchase_code_content" style="{{ $activeTab === 'purchase' ? 'display:block;' : 'display:none;' }}">
                    <div class="content-main-register">
                        <div class="content-dispaly-amount-register">
                            <div class="code-amount">${{ $purchaseAmount }}</div>
                            <div class="code-permonth">Per Code / Month</div>
                            <div class="code-total">Total Annual subscription <b>${{ $purchaseTotalAnnual }}</b></div>
                        </div>
                        <div class="content-label-register">
                            <label for="no_codes">Enter a number of codes required:</label>
                        </div>
                        <div class="content-textbox-register">
                            <input id="no_codes" type="text" class="text-fi" maxlength="4" inputmode="numeric" wire:model.defer="purchaseQuantity">
                        </div>
                        <div class="content-button-register">
                            <input type="button" id="calculate" class="text-fi" value="CALCULATE" wire:click="calculatePurchase">
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="secondstep-submit">
                        <input type="button" id="order-summary" class="text-fi" value="PURCHASE" wire:click="submitPurchase">
                    </div>
                </div>

                <div class="content-register" id="agency_reseller_content" style="{{ $activeTab === 'reseller' ? 'display:block;' : 'display:none;' }}">
                    <div class="content-main-register">
                        <div class="content-label-register reseller">
                            <label for="reseller_code_client">Enter your reseller password:</label>
                        </div>
                        <div class="content-textbox-register reseller">
                            <input id="reseller_code_client" type="text" class="text-fi" wire:model.defer="resellerCode">
                        </div>
                        <div class="clear"></div>
                        <div class="content-dispaly-amount-register">
                            <div class="code-amount">${{ $resellerAmount }}</div>
                            <div class="code-permonth">Per Code / Month</div>
                            <div class="code-total">Total Annual subscription <b>${{ $resellerTotalAnnual }}</b></div>
                            <div class="code-total resellerMarginDiff">Reseller margin <b>${{ $resellerMargin }}</b></div>
                        </div>
                        <div class="content-label-register">
                            <label for="reseller_code_qty">Enter a number of codes required:</label>
                        </div>
                        <div class="content-textbox-register">
                            <input id="reseller_code_qty" type="text" class="text-fi" maxlength="4" inputmode="numeric" wire:model.defer="resellerQuantity">
                        </div>
                        <div class="content-button-register">
                            <input type="button" id="calculateRe" class="text-fi" value="CALCULATE" wire:click="calculateReseller">
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="secondstep-submit">
                        <input type="button" id="order-summaryRe" class="text-fi" value="PURCHASE" wire:click="submitReseller">
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-filament-panels::page>
