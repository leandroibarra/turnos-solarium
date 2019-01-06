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

    <nav class="navbar navbar-expand-md navbar-custom fixed-top top-nav-menu" role="navigation">
        <div class="container">
            <a class="navbar-brand" href="{{ route('index.index') }}">{{ config('app.name', 'Laravel') }}</a>

            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#topNavBar" aria-controls="topNavBar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <div class="collapse navbar-collapse" id="topNavBar">
                <ul class="navbar-nav ml-auto navscroll">
                    <li class="nav-item">
                        <a class="nav-link" href="#my-slider">{{ __('Home') }}</a>
                    </li>
                    @if ($aSiteParameter['about_tanning_text'] != '')
                    <li class="nav-item">
                        <a class="nav-link" href="#tanning">{{ __('Tanning') }}</a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="#prices">{{ __('Prices') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('book.index') }}">{{ __('Book online') }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="content" id="content">
        <div id="my-slider">
            @for ($iIndex=1; $iIndex<4; $iIndex++)
            <div class="my-slide" data-duration="8000" data-transition="2">
                <img class="my-slide-background" src="{{ asset('images/slide'.$iIndex.'.jpg') }}" alt="slide{{ $iIndex }}" data-size="cover">
                <h1 class="my-slider-slide slide-title color-light text-center"
                    style="top:300px; width:100%; text-align:center;"
                    data-transitionin="offsety:500;duration:1800;delay:500;easing:easeInOutExpo;"
                    data-transitionout="offsety:-500; scaley:0.1; duration:1200; delay:4000;">
                    Texto slide {{ $iIndex }}
                </h1>
            </div>
            @endfor
        </div>

        @if ($aSiteParameter['about_tanning_text'] != '')
        <section id="tanning" class="section bg-gray">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h2 class="h1">{{ __('About tanning') }}</h2>
                        <hr class="spacer-30">
                        <div class="tanning-content">{{ $aSiteParameter['about_tanning_text'] }}</div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <section id="prices" class="section">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h1>{{ __('Prices') }}</h1>
                        <hr class="spacer-20">
                    </div>
                </div>
                <div class="row">
                    @foreach (
                        [
                            ['14', '99', 'info'],
                            ['200', '00', 'warning'],
                            ['599', '00', 'success'],
                            ['1450', '50', 'danger']
                        ]
                        as $iKey => $aPrice
                    )
                    <div class="col-12 col-lg-3 col-md-6">
                        <div class="prices">
                            <div class="prices-header bg-{{ $aPrice[2] }}">
                                <h4 class="title">Nombre Plan {{ $iKey + 1 }}</h4>
                                <h2 class="price">
                                    <sup>$</sup><strong>{{ $aPrice[0] }}</strong>@if (!is_null($aPrice[1])).<sup>{{ $aPrice[1] }}</sup>@endif
                                </h2>
                            </div>
                            <hr class="spacer-10" />
                            <div class="prices-features">Descripción plan {{ $iKey + 1 }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

    <footer class="footer">
        <div class="container">
            <hr />

            <div class="row">
                <div class="col-12 col-md-8 d-none d-md-block">
                    <ul class="list-inline">
                        <li class="list-inline-item">
                            <a href="#my-slider">{{ __('Home') }}</a>
                        </li>
                        @if ($aSiteParameter['about_tanning_text'] != '')
                        <li class="list-inline-item">
                            <a href="#tanning">{{ __('Tanning') }}</a>
                        </li>
                        @endif
                        <li class="list-inline-item">
                            <a href="#prices">{{ __('Prices') }}</a>
                        </li>
                        <li class="list-inline-item">
                            <a href="{{ route('book.index') }}">{{ __('Book online') }}</a>
                        </li>
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
    <script src="{{ asset('js/smooth-scroll-2.2.0.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
    jQuery(document).ready(function() {
        // Show spinner
        jQuery('.spinner-container').fadeIn('fast').delay(500).fadeOut('slow');

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
            bOnHoverPause: false
        });

        // Smooth scroll settings
        jQuery('.navbar .navbar-nav a').smoothScroll({
            speed: 800
        });
    });
    </script>
</body>
</html>
