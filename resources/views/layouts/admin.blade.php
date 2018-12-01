<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/bootstrap-4.1.3.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/summernote-bs4-0.8.11.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/fontawesome-5.3.1.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet" type="text/css" />
</head>
<body>
@include('admin.partials.topnav')

@yield('content')

@include('admin.partials.footer')

<!-- Scripts -->
<script src="{{ asset('js/jquery-3.3.1.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/popper-1.11.0.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/bootstrap-4.1.3.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/summernote-bs4-0.8.11.js') }}" type="text/javascript"></script>

<!-- Page Scripts -->
@yield('page-scripts')
</body>
</html>
