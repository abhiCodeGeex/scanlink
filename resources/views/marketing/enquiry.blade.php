@extends('marketing.mkt-page')

@section('title', 'Solutions Enquiry — ScanLink')
@section('meta_description', 'Tell us about your business and which ScanLink solutions you are interested in. We will be in touch.')

@section('content')
    <section class="mkt__page-hero">
        <div class="mkt__container">
            <span class="mkt__eyebrow">Enquiry</span>
            <h1>ScanLink Solutions Enquiry</h1>
            <p>Tell us about your business and the ScanLink applications you're interested in, and our team will be in touch.</p>
        </div>
    </section>

    <section class="mkt__section">
        <div class="mkt__container">
            <div class="mkt__form mkt__form--wide">
                @if ($errors->any())
                    <div class="mkt__alert mkt__alert--err">{{ $errors->first() }}</div>
                @elseif (session('enquiry_submitted'))
                    <div class="mkt__alert mkt__alert--ok">Thanks &mdash; your enquiry has been sent. We'll be in touch shortly.</div>
                @endif

                <form method="post" action="{{ route('marketing.enquiry.submit') }}">
                    @csrf
                    <div class="mkt__form-grid">
                        <div class="mkt__form-field">
                            <label for="companyName">Company name <span class="req" aria-hidden="true">*</span></label>
                            <input type="text" name="companyName" id="companyName" value="{{ old('companyName') }}" required>
                        </div>
                        <div class="mkt__form-field">
                            <label for="contactName">Contact name <span class="req" aria-hidden="true">*</span></label>
                            <input type="text" name="contactName" id="contactName" value="{{ old('contactName') }}" required>
                        </div>
                        <div class="mkt__form-field">
                            <label for="email">Email <span class="req" aria-hidden="true">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email">
                        </div>
                        <div class="mkt__form-field">
                            <label for="tel">Telephone</label>
                            <input type="text" name="tel" id="tel" value="{{ old('tel') }}" autocomplete="tel">
                        </div>
                        <div class="mkt__form-field mkt__form-field--full">
                            <label for="address">Address</label>
                            <input type="text" name="address" id="address" value="{{ old('address') }}">
                        </div>
                        <div class="mkt__form-field">
                            <label for="industryType">Industry type</label>
                            <input type="text" name="industryType" id="industryType" value="{{ old('industryType') }}">
                        </div>
                        <div class="mkt__form-field">
                            <label for="companySize">Company size</label>
                            <input type="text" name="companySize" id="companySize" value="{{ old('companySize') }}">
                        </div>
                        <div class="mkt__form-field mkt__form-field--full">
                            <label for="briefDescription">Brief description of company focus</label>
                            <textarea name="briefDescription" id="briefDescription" rows="3">{{ old('briefDescription') }}</textarea>
                        </div>
                    </div>

                    <fieldset class="mkt__checkset">
                        <legend>Tick the ScanLink application(s) you're interested in:</legend>
                        @php
                            $interests = [
                                'facilityAsset' => 'Facility Asset Management',
                                'plantEquipment' => 'Plant & Equipment Document Management',
                                'productProcedure' => 'Product & Procedure Management',
                                'communicationTraining' => 'Communication Training',
                                'videoProduction' => 'Video Production, or Re-Editing for Smartphone use',
                                'QRCodeVisual' => 'QR Code Visual Communication',
                                'allOfAbove' => 'All of the above',
                            ];
                        @endphp
                        @foreach ($interests as $field => $label)
                            <label class="mkt__check">
                                <input type="checkbox" name="{{ $field }}" value="1" @checked(old($field))> {{ $label }}
                            </label>
                        @endforeach
                    </fieldset>

                    <div class="mkt__form-field mkt__form-field--full">
                        <label for="comments">Comments</label>
                        <textarea name="comments" id="comments" rows="3">{{ old('comments') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg">Send enquiry</button>
                </form>
            </div>
        </div>
    </section>
@endsection
