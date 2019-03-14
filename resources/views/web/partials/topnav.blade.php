@auth
<nav class="navbar navbar-lg navbar-expand-lg navbar-light bg-transparent">
    <div class="container">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarCollapse">
            @if (Auth::user()->hasRole(['Admin', 'Sysadmin', 'Employee']))
            <a class="btn btn-outline-dark mt-2 mt-lg-0" href="{{ route('admin') }}">{{ __('Administration') }}</a>
            @endif

            <div class="dropdown mt-{{ (Auth::user()->hasRole(['Admin', 'Sysadmin'])) ? 2 : 3 }} mt-lg-0 ml-auto">
                <button class="btn btn-outline-white dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}</button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
@endauth