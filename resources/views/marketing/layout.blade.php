<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ScanLink')</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('styles/style.css') }}" media="screen">
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <style>
        .scanlink-container { max-width: 1160px; margin: 0 auto; }
        .main-container { float: left; width: 100%; }
        label.error { color: #F00; font-size: 12px; }
        label.ok { color: #35D10A; font-size: 12px; }
        .contact-form #save {
            float: left;
            margin: 8px 0 0 0;
            min-width: 90px;
            height: 36px;
            line-height: 36px;
            padding: 0 18px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            color: #fff;
            border: 1px solid #006201;
            border-radius: 6px;
            cursor: pointer;
            background: linear-gradient(to bottom, #008901 0%, #007a01 100%);
        }
        .contact-form .captcha {
            display: inline-block;
            vertical-align: middle;
            margin: 4px 8px 8px 0;
            border: 1px solid #ddd;
        }
        .contact-form img[src*="capcha-img"] {
            display: none;
        }
    </style>
    @stack('head')
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
            @if ($isPortalUser ?? false)
                <section class="welcome-box">
                    <span class="logout-btn logout-btn-new">
                        <form method="post" action="{{ url('/portal/logout') }}" style="display:inline;margin:0;padding:0;">
                            @csrf
                            <button type="submit" style="all:unset;cursor:pointer;color:inherit;">Logout</button>
                        </form>
                    </span>
                    <span class="label">Welcome:</span>
                    <span class="username">{{ $portalUserEmail ?? '' }}</span>
                </section>

                <ul class="menu">
                    <li>
                        <a href="{{ url('/portal/contact') }}" class="contact @yield('nav_contact_active')">
                            <span class="text"><span>Contact us</span></span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/portal/profiles') }}" class="dashboard">
                            <span class="text"><span>Dashboard</span></span>
                        </a>
                    </li>
                    <li id="my_account">
                        <a href="#" class="selectSub arrow">
                            <span class="text"><span>My Account</span></span>
                        </a>
                        <ul class="subMenu" id="subMenu">
                            <li><a href="{{ url('/portal/account') }}">Edit user profile</a></li>
                            <li><a href="{{ url('/portal/profiles') }}">Master Code List</a></li>
                            <li><a href="{{ url('/portal/code-balance') }}">Code Balance</a></li>
                            <li><a href="{{ url('/portal/purchase-codes') }}">Purchase codes</a></li>
                            <li><a href="{{ url('/portal/multiple-code-renewal') }}">Multiple Code Renewal</a></li>
                        </ul>
                    </li>
                    <li id="how_to">
                        <a href="#" class="selectSub arrow">
                            <span class="text"><span>How to</span></span>
                        </a>
                        <ul class="subMenu" id="subMenu2">
                            @foreach ($howToLinks as $howto)
                                <li><a href="{{ $howto['url'] }}" title="{{ $howto['title'] }}">{{ $howto['title'] }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
            @else
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
                        <a href="{{ route('marketing.contact') }}" class="contact @yield('nav_contact_active')">
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
                            @foreach ($howToLinks as $howto)
                                <li><a href="{{ $howto['url'] }}" title="{{ $howto['title'] }}">{{ $howto['title'] }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                </ul>

                <span class="login-menu" id="login">
                    <a href="{{ route('marketing.home') }}#login">
                        <span class="text">Login</span>
                    </a>
                </span>
            @endif
        </section>
    </nav>

    <section class="wrapper clerfix">
        <section id="content">
            <section class="content-container cearfix">
                @yield('content')
            </section>
        </section>

        <footer id="footer" class="clearfix">
            <section class="footer-left">
                <ul>
                    <li><a href="{{ route('marketing.home') }}">Home</a></li>
                    <li><a href="{{ route('marketing.home') }}#content" class="scan-link">What is ScanLink?</a></li>
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
                    <figure class="fleft">
                        <a href="{{ route('marketing.home') }}">
                            <img src="{{ asset('images/img3.png') }}" alt="" />
                        </a>
                    </figure>
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
        $('#my_account').mouseover(function () {
            $('#subMenu').show();
            $('#my_account a.selectSub').addClass('active');
        }).mouseout(function () {
            $('#subMenu').hide();
            $('#my_account a.selectSub').removeClass('active');
        });

        $('#how_to').mouseover(function () {
            $('#subMenu2').show();
            $('#how_to a').addClass('active');
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

        $('.close-btn, .black-tint').click(function () {
            $('.black-tint').hide();
            $('.player-window').hide();
            $('#how_to_pop_up').empty();
        });
    });
</script>
@stack('scripts')
</body>
</html>
