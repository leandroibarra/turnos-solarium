<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    <!-- Styles -->
    <link href="{{ asset('css/bootstrap-4.1.3.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/errors.css') }}" rel="stylesheet" type="text/css" />
</head>
<body>
    @php
    $sFrom = (request()->segments()[0] == 'admin') ? 'admin' : '';
    @endphp
    <div class="md:flex min-h-screen">
        <div class="w-full bg-white flex items-center justify-center">
            <div class="max-w-sm m-8">
                <div class="text-black text-5xl md:text-15xl font-black">
                    @yield('code', __('Oh no'))
                </div>

                <div class="w-16 h-1 bg-{{ ($sFrom == 'admin') ? 'primary' : 'gold' }} my-3 md:my-6"></div>

                <p class="text-grey-darker text-2xl md:text-3xl font-light mb-8 leading-normal">
                    @yield('message')
                </p>

                <a href="{{ url('/'.$sFrom) }}" class="btn btn-lg btn-{{ ($sFrom == 'admin') ? 'primary' : 'gold' }}">{{ __('Go Home') }}</a>
            </div>
        </div>
    </div>
</body>
</html>
