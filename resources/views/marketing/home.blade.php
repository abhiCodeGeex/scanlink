<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to ScanLink</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('styles/style.css') }}" media="screen">
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <style>
        /* Match live home: Versatile heading is green */
        .smartInfo-box:nth-child(2) h3 { color: #007900; }
        .main-container { float: left; width: 100%; }
        .forgot-box {
            width: 337px;
            position: absolute;
            right: 0;
            top: 85px;
            padding: 20px 30px;
            background: url({{ asset('images/login-bg.png') }}) repeat left top;
            display: none;
            color: #fff;
        }
        .forgot-box label { color: #fff; display: block; margin: 0 0 4px; }
        .forgot-box .text-fi {
            width: 224px;
            padding: 5px 10px;
            height: 32px;
            line-height: 32px;
            background: #fff;
            border: 1px solid #1f2730;
            float: left;
            margin: 0 0 5px 0;
            -webkit-border-radius: 6px;
            -moz-border-radius: 6px;
            border-radius: 6px;
        }
        .login-error {
            color: #ff0;
            display: block;
            margin: 0 0 10px;
            font-size: 12px;
            line-height: 14px;
        }
        .scanlink-container {
            max-width: 1160px;
            margin: 0 auto;
        }
        .login-box #signin_submit {
            float: left;
            margin: 8px 0 0 90px;
            width: 70px;
            height: 42px;
            line-height: 42px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            color: #fff;
            border: 1px solid #006201;
            border-radius: 6px;
            cursor: pointer;
            background: linear-gradient(to bottom, #008901 0%, #007a01 100%);
        }
        .login-box #forgot_pass_link {
            color: #fff;
            float: left;
            margin: 0 0 8px 90px;
            font-size: 12px;
        }
    </style>
</head>
<body>
<section class="main-container clearfix">
    <header id="header">
        <section class="header-in clearfix">
            <h1 id="logo">
                <a href="{{ route('marketing.home') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="ScanLink" />
                </a>
            </h1>
        </section>
</header>

    <nav class="top-navigation">
        <section class="nav-in">
            <ul class="menu">
                <li>
                    <a href="{{ url('/voclogin') }}" class="voclogin">
                        <span class="text"><span>VOCC Login</span></span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('marketing.pricing') }}" class="pricing">
                        <span class="text"><span>Pricing</span></span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('marketing.contact') }}" class="contact">
                        <span class="text"><span>Contact us</span></span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('/portal/register') }}" class="register">
                        <span class="text"><span>Register</span></span>
                    </a>
                </li>
                <li id="how_to">
                    <a href="{{ route('marketing.how-to') }}" class="selectSub arrow">
                        <span class="text"><span>How to</span></span>
                    </a>
                    <ul class="subMenu" id="subMenu2">
                        @foreach (($howToLinks ?? []) as $howto)
                            <li><a href="{{ $howto['url'] }}" title="{{ $howto['title'] }}">{{ $howto['title'] }}</a></li>
                        @endforeach
                    </ul>
                </li>
            </ul>

            <span class="login-menu" id="login">
                <a href="#login">
                    <span class="text">Login</span>
                </a>
            </span>

            <section class="login-box" id="login_box" @if ($errors->any()) style="display:block;" @endif>
                @error('email')
                    <span class="login-error">{{ $message }}</span>
                @enderror
                @error('password')
                    <span class="login-error">{{ $message }}</span>
                @enderror

                <form id="signin" method="post" action="{{ route('marketing.portal-login') }}">
                    @csrf
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="text-fi" value="{{ old('email') }}" required autocomplete="username">
                    <br/>
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="text-fi" required autocomplete="current-password">
                    <br/>
                    <label class="lesslineheight">&nbsp;</label>
                    <a href="{{ url('/portal/password-reset/request') }}" id="forgot_pass_link">Forgot your password? </a>
                    <br/>
                    <input type="submit" id="signin_submit" value="Login">
                </form>
            </section>
        </section>
    </nav>

    <section class="wrapper clerfix">
        <section id="content">
            <section class="content-container cearfix">

                <div class="scanlink-container">
                    <div class="home-new-head">
                        <h2>Create<span>.</span>&nbsp;&nbsp;&nbsp;&nbsp;Connect<span>.</span>&nbsp;&nbsp;&nbsp;&nbsp;Measure<span>.</span></h2>
                        <p>Welcome to ScanLink. The worlds best QR Code generator and content<br />
                            management platform for creating mobile interactive experiences that<br />
                            <span>educate</span>, <span>inform</span> and <span>sell</span>...
                        </p>
                    </div>

                    <div class="home-smartInfo">
                        <div class="smartInfo-box">
                            <img src="{{ asset('images/easy-info-icon.png') }}" alt="" />
                            <h3>Its easy</h3>
                            <p>You simply upload your<br />
                                content and then download<br />
                                your 'dynamic' QR code.</p>
                        </div>

                        <div class="smartInfo-box">
                            <img src="{{ asset('images/versatile-info-icon.png') }}" alt="" />
                            <h3>Versatile</h3>
                            <p>Print your QR code on <br />
                                product labels, brochures,<br />
                                signage, business cards<br />
                                - Anywhere!</p>
                        </div>

                        <div class="smartInfo-box">
                            <img src="{{ asset('images/smart-info-icon.png') }}" alt="" />
                            <h3>Smart</h3>
                            <p>Each time someone scans your code<br />
                                ScanLink can record the time, date,<br />
                                type of device used and even the<br />
                                GPS location. You can also collect<br />
                                names, mobile numbers and email<br />
                                addresses to build your database.</p>
                        </div>
                    </div>

                    <section class="workplace-block-raw museum-video-div">
                        <div class="workplace-box fleft">
                            <div class="home-content-block-new clearfix">
                                <div class="m20">&nbsp;</div>
                                <p class="large-size">See how the Hellenic Museum uses ScanLink to create engaging mobile interactive exhibitions...</p>
                                <div class="watch-the-video-right taRight"><strong>Watch the video</strong></div>
                            </div>
                        </div>
                        <div class="workplace-box fright">
                            <iframe width="480" height="300" frameborder="0" allowfullscreen="" src="https://www.youtube.com/embed/kzARc58KXTA?rel=0&amp;autoplay=0&amp;loop=0&amp;wmode=opaque" marginwidth="0" marginheight="0"></iframe>
                        </div>
                    </section>

                    <div class="clearfix"></div>
                    <div class="mobileProds">
                        <h5>There are hundreds of applications for Scanlink.</h5>
                        <p class="green_text_normal_pdf">Click to download a PDF information sheet...</p>
                        <div class="mobileProdsRow">
                            @php
                                $pdfBase = 'https://scanlink.com.au/download_file.php';
                                $apps = [
                                    ['ScanLink-Hotel-Surveys.pdf', 'ScanLink-Hotel-Surveys.jpg', 'Hotel Surveys'],
                                    ['Museum-Flyer.pdf', 'Museum-Flyer.jpg', 'Arts & Culture'],
                                    ['ScanLink-Intercative-Memorials.pdf', 'ScanLink-Memorial-Flyer.jpg', 'Monuments & Memorials'],
                                    ['Business-mobile-brochure-HR.pdf', 'Business-mobile-brochure.jpg', 'Business Profile'],
                                    ['ScanLink-Mobile-Surveys-2.pdf', 'ScanLink-Customer-feedback-Surveys.jpg', 'Customer Feedback'],
                                    ['ScanLink-for-product-packaging.pdf', 'ScanLink-Product-packaging.jpg', 'Product Packaging'],
                                    ['Mobile-Interactive-Workplace.pdf', 'Mobile-Interactive-Workplace.jpg', 'The Workplace'],
                                ];
                            @endphp
                            @foreach ($apps as $app)
                                <div class="pdf-info-sheet">
                                    <a href="{{ $pdfBase }}?filename={{ urlencode($app[0]) }}&filepath=images/brochure" target="_blank" rel="noopener">
                                        <img alt="" src="{{ asset('images/'.$app[1]) }}">
                                    </a>
                                    <p>{!! $app[2] === 'Arts & Culture' ? 'Arts &amp; Culture' : e($app[2]) !!}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div style="margin-left:-20px;margin-right:-20px;">
                    <div class="scanlink-container">
                        <div id="ExpressCodeGenBlock" class="ExpressCodeGenBlock clearfix">
                            <div class="ExpressCodeLeft">
                                <h2>Express Code Generator</h2>
                                <div class="ExpressCodeLeftInn">
                                    <p class="Exp1">
                                        Just need to create and download<br>
                                        a basic mobile code fast..?
                                    </p>
                                    <div class="ExpForm clearfix">
                                        <div class="ExpFi1">
                                            <label>Enter the Web page address (URL) here</label>
                                            <input type="text" value="http://" name="txt_url" id="txt_url" class="url-textbox" />
                                        </div>
                                        <div class="ExpFi2">
                                            <label>QR</label>
                                            <input type="radio" name="code_type" class="type_of_code" value="0" checked="" />
                                        </div>
                                        <div class="ExpFi2">
                                            <label>DM</label>
                                            <input type="radio" name="code_type" class="type_of_code" value="1" />
                                        </div>
                                    </div>
                                    <div class="monitor">
                                        <img src="{{ asset('images/monitor3.png') }}" class="monitor3" alt="" />
                                        <h3>Create a mobile code with the works...</h3>
                                        <p>Powerful analytics, data collection functions,
                                            content creation templates, mobile interactive forms and more</p>
                                        <p class="green_text_normal">
                                            <a href="{{ url('/portal/register') }}"><b>Click here </b>to get started with your <b>first code FREE</b> for a year!</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="ExpressCodeRight">
                                <h2>Code Preview</h2>
                                <div class="qrCodeBox">
                                    <img src="{{ asset('images/qr_code_sample.png') }}" id="code_preview_img" alt="" />
                                    <input type="hidden" name="code_preview_flag" id="code_preview_flag" value="0"/>
                                </div>
                                <div class="Express-combo">
                                    <div class="form-view1">
                                        <select name="download_option" id="download_option" class="">
                                            <option value="" selected="">Download As</option>
                                            <option value="pdf">PDF</option>
                                            <option value="tiff">TIFF</option>
                                            <option value="eps">Eps(Vector)</option>
                                        </select>
                                    </div>
                                    <br>
                                    <input type="button" onclick="alert('Express code download will be available after you register and create a code in the portal.');" value="Download" />
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="genQr">
                        <div class="scanlink-container">
                            <div class="genqr-dtl">
                                <p>Scanlink is a powerful QR Code generator<br>
                                    and Mobile Engagement Platform<br>
                                    featuring a variety of solutions<br>
                                    for business of all sizes</p>
                                <p class="readyStart">
                                    <a href="{{ url('/portal/register') }}" id="register-link">Try it now with your first code<br><b>FREE for a year!</b></a>
                                </p>
                                <div class="Arrow-Down"><img src="{{ asset('images/arrow-down-png.png') }}" width="50" height="68" alt=""></div>
                                <div class="Scroll-down">Scroll down to learn more</div>
                            </div>
                            <div class="Hend_image"><img src="{{ asset('images/hand-phone-icon-original.png') }}" alt=""></div>
                        </div>
                    </div>

                    <div class="clearfix"></div>

                    <section>
                        <div class="URL-image"><img src="{{ asset('images/website-680x249.jpg') }}" width="100%" alt=""></div>
                        <div class="clear"></div>
                        <div class="green-content">
                            <div class="scanlink-container">
                                <div class="Hend-img"><img src="{{ asset('images/Your-website.png') }}" width="323" height="390" alt=""></div>
                                <div class="Green-Right" style="margin-right: 175px;">Create a Scanlink QR Code<br>
                                    linked to any URL...</div>
                                <div class="Links">
                                    <div class="Show-Me"><a href="https://www.youtube.com/embed/uEDTnBPUk28?rel=0" id="show_me_how"><img src="{{ asset('images/Show-me-how.png') }}" width="42" height="41" alt=""></a></div>
                                    <div class="show-me-title">SHOW ME HOW</div>
                                </div>
                                <div class="Links">
                                    <div class="Show-Me"><a href="{{ url('/portal/register') }}"><img src="{{ asset('images/dO-IT-nOW.png') }}" width="42" height="41" alt=""></a></div>
                                    <div class="show-me-title">&nbsp;&nbsp;&nbsp;&nbsp;DO IT NOW</div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section>
                        <div class="URL-image"><img src="{{ asset('images/Low-res2.png') }}" width="100%" alt=""></div>
                        <div class="clear"></div>
                        <div class="green-content">
                            <div class="scanlink-container">
                                <div class="Hend-img"><img src="{{ asset('images/Finalist-Hend.png') }}" width="323" height="390" alt=""></div>
                                <div class="Green-Right" style="margin-right: 175px;">Create mobile interactive<br>
                                    packaging & signage...</div>
                                <div class="Links">
                                    <div class="Show-Me"><a href="{{ route('marketing.how-to') }}"><img src="{{ asset('images/Show-me-More.png') }}" height="41" alt=""></a></div>
                                    <div class="show-me-title">SHOW ME MORE</div>
                                </div>
                                <div class="Links">
                                    <div class="Show-Me"><a href="{{ url('/portal/register') }}"><img src="{{ asset('images/dO-IT-nOW.png') }}" width="42" height="41" alt=""></a></div>
                                    <div class="show-me-title">&nbsp;&nbsp;&nbsp;&nbsp;DO IT NOW</div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section>
                        <div class="URL-image"><img src="{{ asset('images/banner_workplace3.png') }}" height="410" width="100%" alt=""></div>
                        <div class="clear"></div>
                        <div class="green-content" style="height: 223px;">
                            <div class="scanlink-container">
                                <div class="Hend-img-new" style="margin-top: -181px;"><img src="{{ asset('images/Civil-Globale.png') }}" width="323" height="390" alt=""></div>
                                <div class="Green-Right" style="margin-right: 138px;">Create a mobile interactive workplace <br>
                                    to enhance Health safety sustainability <br>
                                    & productivity...</div>
                                <div class="Links">
                                    <div class="Show-Me"><a href="{{ route('marketing.how-to') }}"><img src="{{ asset('images/Show-me-More.png') }}" height="41" alt=""></a></div>
                                    <div class="show-me-title">SHOW ME MORE</div>
                                </div>
                                <div class="Links">
                                    <div class="Show-Me"><a href="{{ url('/portal/register') }}"><img src="{{ asset('images/dO-IT-nOW.png') }}" width="42" height="41" alt=""></a></div>
                                    <div class="show-me-title">&nbsp;&nbsp;&nbsp;&nbsp;DO IT NOW</div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section>
                        <div class="URL-image"><img src="{{ asset('images/20140320_113407 LR.jpg') }}" width="100%" alt=""></div>
                        <div class="clear"></div>
                        <div class="green-content">
                            <div class="scanlink-container">
                                <div class="Hend-img"><img src="{{ asset('images/Chubb.png') }}" width="323" height="390" alt=""></div>
                                <div class="Green-Right" style="margin-right: 155px;">Create mobile interactive forms,<br>
                                    surveys & checklists...</div>
                                <div class="Links">
                                    <div class="Show-Me"><a href="{{ route('marketing.how-to') }}"><img src="{{ asset('images/Show-me-More.png') }}" height="41" alt=""></a></div>
                                    <div class="show-me-title">SHOW ME MORE</div>
                                </div>
                                <div class="Links">
                                    <div class="Show-Me"><a href="{{ url('/portal/register') }}"><img src="{{ asset('images/dO-IT-nOW.png') }}" width="42" height="41" alt=""></a></div>
                                    <div class="show-me-title">&nbsp;&nbsp;&nbsp;&nbsp;DO IT NOW</div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="Main">
                        <div class="URL-image"><img src="{{ asset('images/20140604_135849_new.jpg') }}" width="100%" alt=""></div>
                        <div class="clear"></div>
                        <div class="green-content">
                            <div class="scanlink-container">
                                <div class="Hend-img"><img src="{{ asset('images/Gala.png') }}" width="323" height="390" alt=""></div>
                                <div class="Green-Right" style="margin-right: 175px;">Create a mobile profile for you <br>
                                    or your business...</div>
                                <div class="Links">
                                    <div class="Show-Me"><a href="{{ route('marketing.how-to') }}"><img src="{{ asset('images/Show-me-More.png') }}" height="41" alt=""></a></div>
                                    <div class="show-me-title">SHOW ME MORE</div>
                                </div>
                                <div class="Links">
                                    <div class="Show-Me"><a href="{{ url('/portal/register') }}"><img src="{{ asset('images/dO-IT-nOW.png') }}" width="42" height="41" alt=""></a></div>
                                    <div class="show-me-title">&nbsp;&nbsp;&nbsp;&nbsp;DO IT NOW</div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="clearfix">&nbsp;</div>

                <section class="workplace-block-raw">
                    <div class="scanlink-container">
                        <div class="workplace-box fleft">
                            <div class="home-content-block-new clearfix">
                                <div class="m20">&nbsp;</div>
                                <p class="large-size taRight">5 Tips to help make your<br/>QR Code initiative<br/>a success</p>
                                <div class="watch-the-video-right taRight"><strong>WATCH THE VIDEO</strong></div>
                            </div>
                        </div>
                        <div class="workplace-box fright">
                            <iframe width="480" height="300" frameborder="0" allowfullscreen="" src="https://www.youtube.com/embed/5c8EnTAMEl4?rel=0&amp;autoplay=0&amp;loop=0&amp;wmode=opaque" marginwidth="0" marginheight="0"></iframe>
                        </div>
                    </div>
                </section>
                <div class="clearfix">&nbsp;</div>

            </section>
        </section>

        <footer id="footer" class="clearfix">
            <section class="footer-left">
                <ul>
                    <li><a href="{{ route('marketing.home') }}">Home</a></li>
                    <li><a href="#content" class="scan-link">What is ScanLink?</a></li>
                    <li><a href="{{ route('marketing.faq') }}">FAQ</a></li>
                    <li><a href="{{ route('marketing.contact') }}">Contact us</a></li>
                    <li><a href="{{ route('marketing.terms') }}">Terms and Conditions</a></li>
                    <li class="last"><a href="{{ route('marketing.privacy') }}">Privacy Policy</a></li>
                </ul>
            </section>
            <section class="footer-right">
                <div class="aus-tag">
                    <img src="{{ asset('images/aus-map-icon.png') }}" alt="" />
                    <span>An Australian Innovation</span>
                </div>
                <div class="fleft">
                    <figure class="fleft"><a href="{{ route('marketing.home') }}"><img src="{{ asset('images/img3.png') }}" alt="" /></a></figure>
                    <span>Powered by <a href="{{ route('marketing.home') }}">ScanLink Technologies</a></span>
                </div>
            </section>
        </footer>
    </section>
</section>

<section class="black-tint" style="display:none;"></section>
<section class="player-window" style="display:none;">
    <span class="close-btn"></span>
    <section class="player-window-in" id="how_to_pop_up"></section>
</section>

<script>
    $(document).ready(function () {
        $('.login-menu').click(function (e) {
            e.preventDefault();
            $('.login-menu a').toggleClass('active');
            $('.login-box').slideToggle(500);
        });

        @if ($errors->any())
            $('.login-menu a').addClass('active');
            $('.login-box').show();
        @endif

        if (window.location.hash === '#login') {
            $('.login-menu a').addClass('active');
            $('.login-box').show();
        }

        $('#how_to').mouseover(function () {
            $('#subMenu2').show();
            $('#how_to a').addClass('active');
            $('.login-menu a').removeClass('active');
            $('.login-box').hide();
        }).mouseout(function () {
            $('#subMenu2').hide();
            $('#how_to a').removeClass('active');
        });

        $('#how_to #subMenu2 a').click(function (e) {
            e.preventDefault();
            var url = $(this).attr('href');
            if (!url) return false;
            $('#how_to_pop_up').html('<iframe src="' + url + '" frameborder="0" allowfullscreen wmode="transparent" width="480" height="300"></iframe>');
            $('.black-tint').show();
            $('.player-window').show(500);
        });

        $('#show_me_how').click(function (e) {
            e.preventDefault();
            var url = $(this).attr('href');
            $('#how_to_pop_up').html('<iframe src="' + url + '" frameborder="0" allowfullscreen wmode="transparent" width="480" height="300"></iframe>');
            $('.black-tint').show();
            $('.player-window').show(500);
        });

        $('.close-btn, .black-tint').click(function () {
            $('.black-tint').hide();
            $('.player-window').hide();
            $('#how_to_pop_up').html('');
        });
    });
</script>
</body>
</html>
