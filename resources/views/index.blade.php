<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('plugins/bootstrap-4.1.3/bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/fontawesome-5.3.1.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/my-slider/css/my-slider.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/public.css') }}" rel="stylesheet" type="text/css" />
</head>

<body data-spy="scroll" data-target=".navscroll">
    <!--[if lt IE 8]>
    <p>{{ __('You are using an <strong>outdated</strong> browser. Please upgrade your browser to improve your experience.') }}</p>
    <![endif]-->

    <div class="spinner-container">
        <svg class="spinner" viewBox="0 0 50 50">
            <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
        </svg>
    </div>

    <nav class="navbar navbar-expand-md navbar-custom fixed-top top-nav-menu {{ ($aEnabledSlides->isEmpty()) ? 'top-nav-collapse' : '' }}" role="navigation">
        <div class="container">
            <a class="navbar-brand" href="{{ route('index.index') }}">{{ config('app.name', 'Laravel') }}</a>

            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#topNavBar" aria-controls="topNavBar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <div class="collapse navbar-collapse" id="topNavBar">
                <ul class="navbar-nav ml-auto navscroll">
                    @if (!$aEnabledSlides->isEmpty())
                    <li class="nav-item">
                        <a class="nav-link" href="#my-slider">{{ __('Home') }}</a>
                    </li>
                    @endif
                    @if ($aSiteParameter['about_tanning_text'] != '')
                    <li class="nav-item">
                        <a class="nav-link" href="#tanning">{{ __('Tanning') }}</a>
                    </li>
                    @endif
                    @if (!empty($aBranchesPrices))
                    <li class="nav-item">
                        <a class="nav-link" href="#prices">{{ __('Prices') }}</a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Book online') }}</a>
                    </li>
                    @if ($aSiteParameter['store_url'] != '')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ $aSiteParameter['store_url'] }}" target="_blank">{{ __('Buy online') }}</a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <div class="content" id="content">
        @if ($aEnabledSlides->isEmpty() && empty($aSiteParameter['about_tanning_text']))
            <div class="welcome-title text-center d-flex my-5 py-4 py-md-5">
                <div class="align-self-center mx-auto my-5 py-4 py-md-5">{{ __('Welcome to').' '.config('app.name', 'Laravel') }}</div>
            </div>
        @else
            @if (!$aEnabledSlides->isEmpty())
            <div id="my-slider">
                @foreach ($aEnabledSlides as $aSlide)
                <div class="my-slide" data-duration="8000" data-transition="2">
                    @if ($aSlide->link)
                    <a href="{{ $aSlide->link }}" target="_blank">
                    @endif
                    <img class="my-slide-background" src="{{ $aSlide->fullPath }}" alt="{{ $aSlide->image }}" data-size="cover" />
                    @if ($aSlide->link)
                    </a>
                    @endif
                    <h1 class="my-slider-slide slide-title color-light text-center"
                        style="top:300px; width:100%; text-align:center;"
                        data-transitionin="offsety:500; duration:1800; delay:500; easing:easeInOutExpo;"
                        data-transitionout="offsety:-500; scaley:0.1; duration:1200; delay:4000;">
                        {{ $aSlide->title }}
                    </h1>
                </div>
                @endforeach
            </div>
            @endif

            @if (!empty($aSiteParameter['about_tanning_text']))
            <section id="tanning" class="section bg-gray">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="h1 text-center">{{ __('About our tanning') }}</h2>
                            <hr class="spacer-30">
                            <div class="tanning-content">{!! $aSiteParameter['about_tanning_text'] !!}</div>
                        </div>
                    </div>
                </div>
            </section>
            @endif

            @if (!empty($aBranchesPrices))
            <section id="prices" class="section">
                <div class="container">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h1>{{ __('Prices') }}</h1>
                            <hr class="spacer-20">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <ul class="nav nav-tabs" role="tablist">
                                @foreach ($aBranchesPrices as $iKey => $aBranch)
                                <li class="nav-item">
                                    <a class="nav-link {{ $iKey === 0 ? 'active' : '' }}" data-toggle="tab" href="#branch-{{ $aBranch['id'] }}">
                                        {{ $aBranch['name'] }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                            <div class="tab-content">
                                @foreach ($aBranchesPrices as $iKey => $aBranch)
                                <div class="tab-pane container {{ $iKey === 0 ? 'active' : '' }}" id="branch-{{ $aBranch['id'] }}">
                                    <div class="row">
                                        @foreach (array_chunk($aBranch['enabled_prices'], 4, true) as $aPrices)
                                            @php
                                            $sClassFirst = $sClassLast = '';
                                            $iKey = 0;
                                            switch (count($aPrices)) {
                                                case 1:
                                                    $sClassFirst = 'mx-md-auto';
                                                    break;
                                                case 2:
                                                    $sClassFirst = 'offset-lg-3';
                                                    break;
                                                case 3:
                                                    $sClassFirst = 'offset-lg-1';
                                                    $sClassLast = 'offset-md-3 offset-lg-0';
                                                    break;
                                            }
                                            @endphp
                                            @foreach ($aPrices as $iIndex=>$aPrice)
                                                @php
                                                $iKey++;
                                                $aBgColors = ['info', 'warning', 'success', 'danger', 'aqua', 'yellow', 'olive', 'red', 'blue', 'orange', 'green', 'maroon'];
                                                $sBgColor = $aBgColors[$iIndex % count($aBgColors)];
                                                $aPriceParts = explode($sDecimalPointSeparator, $aPrice['price']);
                                                @endphp
                                            <div class="col-12 col-lg-3 col-md-6 {{ ($iKey == 1) ? $sClassFirst : ((count($aPrices) == $iKey) ? $sClassLast : '') }} count-prices-{{count($aPrices)}}">
                                                <div class="prices">
                                                    <div class="prices-header bg-{{ $sBgColor }}">
                                                        <h4 class="title">{{ $aPrice['title'] }}</h4>
                                                        <h2 class="price">
                                                            <sup>$</sup><strong>{{ $aPriceParts[0] }}</strong>{{ $sDecimalPointSeparator }}<sup>{{ $aPriceParts[1] }}</sup>
                                                        </h2>
                                                    </div>
                                                    <hr class="spacer-10" />
                                                    <div class="prices-features">{{ $aPrice['description'] }}</div>
                                                </div>
                                            </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            @endif
        @endif
    </div>

    <footer class="footer">
        <div class="container">
            <hr />

            <div class="row">
                <div class="col-12 col-md-8 d-none d-md-block">
                    <ul class="list-inline">
                        @if (!$aEnabledSlides->isEmpty())
                        <li class="list-inline-item">
                            <a href="#my-slider">{{ __('Home') }}</a>
                        </li>
                        @endif
                        @if (!empty($aSiteParameter['about_tanning_text']))
                        <li class="list-inline-item">
                            <a href="#tanning">{{ __('Tanning') }}</a>
                        </li>
                        @endif
                        @if (!empty($aBranchesPrices))
                        <li class="list-inline-item">
                            <a href="#prices">{{ __('Prices') }}</a>
                        </li>
                        @endif
                        <li class="list-inline-item">
                            <a href="{{ route('login') }}">{{ __('Book online') }}</a>
                        </li>
                        @if ($aSiteParameter['store_url'] != '')
                        <li class="list-inline-item">
                            <a href="{{ $aSiteParameter['store_url'] }}" target="_blank">{{ __('Buy online') }}</a>
                        </li>
                        @endif
                    </ul>
                </div>

                <div class="col-12 col-md-4">
                    <div class="footer-social-wrapper text-right">
                        @if ($aSiteParameter['pinterest_url'] != '')
                        <a class="social-btn" href="{{ $aSiteParameter['pinterest_url'] }}" target="_blank">
                            <i class="fab fa-pinterest-p"></i>
                            <span>p</span>
                        </a>
                        @endif
                        @if ($aSiteParameter['facebook_url'] != '')
                        <a class="social-btn" href="{{ $aSiteParameter['facebook_url'] }}" target="_blank">
                            <i class="fab fa-facebook-f"></i>
                            <span>f</span>
                        </a>
                        @endif
                        @if ($aSiteParameter['twitter_url'] != '')
                        <a class="social-btn" href="{{ $aSiteParameter['twitter_url'] }}" target="_blank">
                            <i class="fab fa-twitter"></i>
                            <span>t</span>
                        </a>
                        @endif
                        @if ($aSiteParameter['instagram_url'] != '')
                        <a class="social-btn" href="{{ $aSiteParameter['instagram_url'] }}" target="_blank">
                            <i class="fab fa-instagram"></i>
                            <span>i</span>
                        </a>
                        @endif
                    </div>
                </div>

                <div class="col-12">
                    <p class="copyright">© {{ date('Y').' '.config('app.name', 'Laravel') }}</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.3.1.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/bootstrap-4.1.3/bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/my-slider/js/my-slider.js') }}" type="text/javascript"></script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/gsap/latest/TweenMax.min.js" type="text/javascript"></script>

    <script src="{{ asset('js/scrolltopcontrol-1.1.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
    jQuery(document).ready(function() {
        // Show spinner
        jQuery('.spinner-container').fadeIn('fast').delay(500).fadeOut('slow');

        @if (!$aEnabledSlides->isEmpty())
        // Navigation to collapse the navbar on scroll
        jQuery(window).scroll(function() {
            if (jQuery('.navbar').offset().top > 50)
                jQuery('.navbar.fixed-top').addClass('top-nav-collapse');
            else
                jQuery('.navbar.fixed-top').removeClass('top-nav-collapse');
        });

        // Initialize my slider
        jQuery('#my-slider').mySlider({
            width: 1200,
            height: 674,
            bFullSize: false,
            bFullWidth: true,
            bPauseOnHover: false
        });
        @endif

        /*
         * BEGIN - Smooth Scroll Animation
         */
        // Select all links with hashes
        jQuery('a[href*="#"]')
            // Remove links that don't actually link to anything
            .not('[href="#"]')
            // Remove links that belongs to branches prices navigation
            .not('[href*="#branch"]')
            .click(function(event) {
                // Process links of this page only
                if (
                    location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') &&
                    location.hostname == this.hostname
                ) {
                    // Figure out element to scroll to
                    var oTarget = jQuery(this.hash);

                    oTarget = (oTarget.length) ? oTarget : jQuery('[name=' + this.hash.slice(1) + ']');

                    // If target exist
                    if (oTarget.length) {
                        // Only cancel default event if animation is actually gonna happen
                        event.preventDefault();

                        jQuery('html, body').animate(
                            {
                                scrollTop: oTarget.offset().top
                            },
                            800,
                            function() {
                                // Callback after animation
                                var $oTarget = jQuery(oTarget);

                                // Must change focus
                                $oTarget.focus();

                                if ($oTarget.is(':focus')) {
                                    // Check if the target was focused
                                    return false;
                                } else {
                                    // Adding tabindex attribute for not focusable elements
                                    $oTarget.attr('tabindex', '-1');

                                    // Set focus again
                                    $oTarget.focus();
                                }
                            }
                        );
                    }
                }
            });
        /*
         * END - Smooth Scroll Animation
         */
    });
    </script>
</body>
</html>
