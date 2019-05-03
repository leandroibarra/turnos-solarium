<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('plugins/bootstrap-4.1.3/bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/fontawesome-5.3.1.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/animate-3.7.0.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/common.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/web.css') }}" rel="stylesheet" type="text/css" />
</head>
<body>
    @include('web.partials.topnav')

    @yield('content')

    @include('web.partials.footer')

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.3.1.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/bootstrap-4.1.3/bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/bootstrap-notify-3.1.5.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/common.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
    @if (request()->session()->has('flash_notification'))
        @foreach (session('flash_notification', collect())->toArray() as $aMessage)
        showNotify('{{ $aMessage['message'] }}', '{{ $aMessage['level'] }}', '{{ $aMessage['important'] ? false : true }}');
        @endforeach
    @endif
    </script>

    <!-- Page Scripts -->
    @yield('page-scripts')
</body>
</html>
