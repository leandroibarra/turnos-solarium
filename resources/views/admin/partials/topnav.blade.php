@auth
<nav class="navbar navbar-lg navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="javascript:void(0);">@yield('title')</a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarCollapse">
            <form method="POST" action="{{ route('admin.logout') }}" class="ml-auto">
                @csrf

                <button type="submit" class="btn btn-outline-light">{{ __('Logout') }}</button>
            </form>
        </div>
    </div>
</nav>
@endauth