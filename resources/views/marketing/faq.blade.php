@extends('marketing.mkt-page')

@section('title', 'FAQ — ScanLink')
@section('meta_description', 'Frequently asked questions about ScanLink — cost, access, security and support.')

@section('content')
    <section class="mkt__page-hero">
        <div class="mkt__container">
            <span class="mkt__eyebrow">Help centre</span>
            <h1>Frequently asked questions</h1>
            <p>Everything you need to know about ScanLink &mdash; cost, access, security and support.</p>
        </div>
    </section>

    <section class="mkt__section">
        <div class="mkt__container">
            <div class="mkt__faq">
                <article class="mkt__faq-item">
                    <h3>What does ScanLink cost?</h3>
                    <p>Creating a ScanLink user account is free and includes one complimentary code to help get you started.</p>
                    <p>Whether you&rsquo;re a large organisation or a sole trader, ScanLink is available for all businesses. Access is based on an annual subscription per code, so you only pay for what you use &mdash; from just $4 per month.</p>
                </article>
                <article class="mkt__faq-item">
                    <h3>How do I access my ScanLink account?</h3>
                    <p>Access your account with your secure username and password via the ScanLink online portal, from anywhere in the world, 24/7.</p>
                </article>
                <article class="mkt__faq-item">
                    <h3>Where does ScanLink come from?</h3>
                    <p>ScanLink is an Australian innovation developed by GALA Technologies Pty Ltd (ACN 161 505 513), specialists in mobile interactive solutions for mobile payments, real estate, marketing, retail and workplace communication. Our client base includes government, national retail brands, corporate and SMEs in Australia and overseas. <a href="http://www.galatech.com.au" target="_blank" rel="noopener">www.galatech.com.au</a></p>
                </article>
                <article class="mkt__faq-item">
                    <h3>What is ScanLink?</h3>
                    <p>ScanLink is a cloud-based mobile content creation and management platform with comprehensive analytics and data-collection functions. It generates dynamic Data Matrix and QR codes that act as a universal touch point, instantly connecting mobile and tablet users with specific content on demand.</p>
                    <p>It gives you the ability to easily create, control and measure mobile interactive initiatives to achieve specific outcomes.</p>
                </article>
                <article class="mkt__faq-item">
                    <h3>Where is ScanLink data stored and how secure is it?</h3>
                    <p>All ScanLink data is stored and served by secure, ISO-compliant AWS infrastructure located in Sydney, Australia &mdash; a trusted cloud provider for major banks and governments worldwide. AWS infrastructure complies with standards including:</p>
                    <ul>
                        <li>SOC 1 / SSAE 16 / ISAE 3402, SOC 2, SOC 3</li>
                        <li>FISMA, DIACAP and FedRAMP</li>
                        <li>PCI DSS Level 1</li>
                        <li>ISO 27001, ITAR, FIPS 140-2</li>
                    </ul>
                    <p>Browser sessions are encrypted with SSL. All data uploaded, collected and served for each account is exclusively owned by the registered account holder &mdash; GALA Technologies does not use, share or distribute user data. For full AWS security details visit <a href="http://aws.amazon.com/security/" target="_blank" rel="noopener">aws.amazon.com/security</a>.</p>
                </article>
                <article class="mkt__faq-item">
                    <h3>What if I need assistance?</h3>
                    <p>Our Australian-based support team can be reached 24/7 at <a href="mailto:admin@scanlink.net.au">admin@scanlink.net.au</a>, or during business hours Monday to Friday on 0417 557 640.</p>
                </article>
            </div>
        </div>
    </section>
@endsection
