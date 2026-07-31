<x-filament-panels::page>
    <div class="legacy-purchase-page">
        <link rel="stylesheet" href="{{ asset('styles/style.css') }}">
        <style>
            .fi-page-header { display: none !important; }
            .legacy-purchase-page { font-family: Arial, Helvetica, sans-serif; }
            .legacy-purchase-page .registerli { min-width: 450px; }
            .legacy-purchase-page .purchase-div-submit { width: 150px; }
            .legacy-purchase-page .next-btn {
                background: #008401 !important;
                color: #fff !important;
                border: 0 !important;
                border-radius: 6px !important;
                box-shadow: none !important;
                cursor: pointer;
                height: 40px;
                min-width: 90px;
                font-weight: 700;
            }
        </style>

        <section id="content-ordersummary">
            <section class="add-form-right-register">
                <div class="SectionTitleBox-orderSummary">
                    <div class="SectionTitleRegister">&nbsp;</div>
                    <ul class="form-view clearfix">
                        <li class="registerli">
                            <label>Last name:</label>
                            <input id="last_name" type="text" class="text-fi registertextwidth" wire:model.defer="lastName">
                        </li>
                        <li class="registerli">
                            <label>Billing address:</label>
                            <input id="billing_address" type="text" class="text-fi registertextwidth" wire:model.defer="billingAddress">
                        </li>
                        <li class="registerli">
                            <label>Town:</label>
                            <input id="town" type="text" class="text-fi registertextwidth" wire:model.defer="town">
                        </li>
                        <li class="registerli">
                            <label>Postal code:</label>
                            <input id="postal_code" type="text" class="text-fi registertextwidth" wire:model.defer="postalCode">
                        </li>
                    </ul>
                </div>
            </section>

            <section class="add-form-left-register">
                <div class="SectionTitleBox-orderSummary">
                    <div class="SectionTitleRegister">Billing Details</div>
                    <ul class="form-view clearfix">
                        <li class="registerli">
                            <label>First name:</label>
                            <input id="first_name" type="text" class="text-fi registertextwidth" wire:model.defer="firstName">
                        </li>
                        <li class="registerli">
                            <label>Company name:</label>
                            <input id="company_name" type="text" class="text-fi registertextwidth" wire:model.defer="companyName">
                        </li>
                        <li class="registerli">
                            <label>Email(this will also be your username):</label>
                            <input id="email" type="text" class="text-fi registertextwidth" wire:model.defer="email">
                        </li>
                        <li class="registerli">
                            <label>Telephone number:</label>
                            <input id="phone" type="text" class="text-fi registertextwidth" wire:model.defer="phone">
                        </li>
                        <li class="registerli">
                            <div class="purchase-div-submit">
                                <button type="button" class="next-btn" wire:click="next">NEXT</button>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>
        </section>
    </div>
</x-filament-panels::page>

