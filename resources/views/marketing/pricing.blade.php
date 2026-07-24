@extends('marketing.layout')

@section('title', 'Pricing')
@section('nav_pricing_active', 'active')

@section('content')
<section class="pricing-block clearfix">
    <div class="scanlink-container">
        <h2 class="greenHead">Your first ScanLink code is FREE!</h2>
        <p>
            ScanLink provides scalable and affordable access to mobile QR code and content management services for any size business.<br>
            The ScanLink platform enables users to activate as few or as many mobile codes as required at anytime.<br>
            Pricing for each code activation is calculated on a 12 month subscription basis which can be renewed indefinitely.<br>
            Use the code calculator below to calculate for your code requirements. Remember your first ScanLink code is FREE and you can add new codes to your account at anytime.
        </p>

        <div class="content-register coRound">
            <div class="content-main-register">
                <div class="content-dispaly-amount-register">
                    <div class="code-amount"><sup>$</sup><span id="code_amount">0.00</span><br></div>
                    <div class="code-permonth">Per Code / Month <br></div>
                    <div class="code-total">Total Annual subscription <b>$<span id="subscription_amount">0.00</span></b></div>
                </div>
                <div class="content-label-register">
                    <label for="no_codes">Enter a number of codes required:</label>
                </div>
                <div class="content-textbox-register">
                    <input
                        type="text"
                        name="no_codes"
                        id="no_codes"
                        class="text-fi"
                        maxlength="4"
                        inputmode="numeric"
                        value=""
                    >
                    <label class="error" id="no_codes_error" generated="true"></label>
                </div>
                <div class="content-button-register">
                    <input
                        type="button"
                        name="calculate"
                        id="calculate"
                        class="text-fi"
                        value="Calculate"
                    >
                </div>
            </div>
        </div>

        <p class="marketing-info text-20 margin-B40">
            Ready to get started? Register your ScanLink account in 3 easy steps
            @unless ($isPortalUser ?? false)
                <a href="{{ url('/portal/register') }}" class="regblue bGreen">REGISTER</a>
            @endunless
        </p>

        <h2 class="greenHead">Workplace industrial labels and tags from just $3</h2>
        <p class="margin-B40">
            We provide high quality industrial labels and tag solutions to help deploy your mobile initiative in the workplace.<br>
            Available to order in any quantity directly from your ScanLink account. We also provide customised labels and tags to suit individual requirements.
        </p>

        <h2 class="greenHead">Mobile video production</h2>
        <p>
            Our experienced creative and production team offers a wide-range of affordable mobile optimised video and animation production services. We can provide a range of production techniques and solutions to accommodate your budget.<br>
            Contact us to discuss your requirements.
        </p>
        <p>
            <a href="{{ route('marketing.contact') }}" class="regblue bGreen fright"><strong>Enquire</strong></a>
        </p>
    </div>
</section>
@endsection

@push('scripts')
<script>
    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }

    function calculatePricing() {
        var qty = $('#no_codes').val();
        $('#no_codes_error').text('');

        if (qty === '') {
            alert('Enter a number of code required.');
            $('#no_codes').focus();
            return false;
        }
        if (qty.length > 4) {
            alert('Enter a number of code less than 4 character.');
            $('#no_codes').focus();
            return false;
        }
        if (parseInt(qty, 10) > 1000) {
            alert('Enter a number of code less than 1000.');
            $('#no_codes').focus();
            return false;
        }

        $.ajax({
            url: @json(route('marketing.pricing.calculate')),
            type: 'POST',
            dataType: 'json',
            headers: { 'X-CSRF-TOKEN': @json(csrf_token()) },
            data: {
                no_codes: qty,
                _token: @json(csrf_token())
            }
        }).done(function (data) {
            var errors = data.errors || {};
            var errorKeys = Object.keys(errors);
            if (errorKeys.length > 0) {
                for (var i = 0; i < errorKeys.length; i++) {
                    alert(errors[errorKeys[i]]);
                }
                return;
            }
            $('#code_amount').html(data.amount);
            $('#subscription_amount').html(data.totalsubscrption);
        }).fail(function (_jqXHR, textStatus) {
            alert('Request failed: ' + textStatus);
        });
    }

    $(document).ready(function () {
        $('#no_codes').on('keypress', function (e) {
            return isNumberKey(e);
        });
        $('#calculate').on('click', function () {
            calculatePricing();
        });
    });
</script>
@endpush
