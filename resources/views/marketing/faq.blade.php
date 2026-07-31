@extends('marketing.layout')

@section('title', 'FAQ')

@section('content')
<div class="scanlink-container">
    <h2 class="page-title">FAQ</h2>
    <section class="faq-block clearfix">
        <div class="faq-raw">
            <h3>What does ScanLink cost?</h3>
            <p>
                Creating a ScanLink user account is free and includes one complimentary code to help get you started.<br><br>
                Whether you’re a large organisation or a sole trader ScanLink is available for all businesses. Access to the service is based on an annual subscription per ScanLink code so you only ever pay for what you use. You can start your own ScanLink mobile initiative from just $4 per month.
            </p>
        </div>

        <div class="faq-raw">
            <h3>How do I access my ScanLink account?</h3>
            <p>
                You can access your ScanLink account with your secure username and password via the ScanLink online portal from anywhere in the world 24/7.
            </p>
        </div>

        <div class="faq-raw">
            <h3>Where does ScanLink come from?</h3>
            <p>
                ScanLink is an Australian innovation developed by GALA Technologies Pty Ltd (ACN 161 505 513).<br><br>
                GALA Technologies specialise in the development and management of mobile interactive solutions for a variety of applications including mobile payments, real estate, direct marketing, retail marketing and workplace communication. Our extensive client base includes Government, national retail brands, corporate and SME's in Australia and overseas.
                <a href="http://www.galatech.com.au" target="_blank" rel="noopener">www.galatech.com.au</a>
            </p>
        </div>

        <div class="faq-raw">
            <h3>What is ScanLink?</h3>
            <p>
                ScanLink is a cloud based mobile content creation and management platform with comprehensive analytics and data collection functions that generates 'dynamic' Data Matrix and Quick Response (QR) codes that serve as a universal touch point to instantly connect web enabled mobile and tablet users with specific content on demand.<br><br>
                ScanLink provides users with the ability to easily create, control and measure mobile interactive initiatives to achieve specific outcomes.
            </p>
        </div>

        <div class="faq-raw">
            <h3>Where is ScanLink data stored and how secure is it?</h3>
            <p>
                All ScanLink data is stored and served by secure ISO compliant AWS infrastructure located in Sydney Australia. AWS is a trusted cloud provider for Australia's major banks and governments all around the world. AWS cloud infrastructure is designed and managed to comply with the following IT security standards:<br>
            </p>
            <ul class="faq-ul">
                <li>SOC 1/SSAE 16/ISAE 3402 (formerly SAS 70 Type II)</li>
                <li>SOC 2</li>
                <li>SOC 3</li>
                <li>FISMA, DIACAP, and FedRAMP</li>
                <li>PCI DSS Level 1</li>
                <li>ISO 27001</li>
                <li>ITAR</li>
                <li>FIPS 140-2</li>
            </ul>
            <br>
            <p>
                For full details on AWS security visit
                <a href="http://aws.amazon.com/security/" target="_blank" rel="noopener">http://aws.amazon.com/security/</a><br><br>
                Browser sessions are automatically encrypted with SSL and user passwords are protected with Message-Digest Algorithm 5 (MD5). All user account and code profile data is protected against unwanted search engine indexing with Robots.txt. All data uploaded, collected and served for each ScanLink user account is exclusively owned by the ScanLink registered account holder. GALA Technologies does not utilise, share or distribute user data for its own purposes or with any other third party.
            </p>
        </div>

        <div class="faq-raw">
            <h3>What if I need assistance?</h3>
            <p>
                Our Australian based customer support team can be contacted 24/7 via dedicated email at
                <a href="mailto:admin@scanlink.net.au">admin@scanlink.net.au</a>
                or during normal business hours Monday to Friday on 0417 557 640.
            </p>
        </div>
    </section>
</div>
@endsection
