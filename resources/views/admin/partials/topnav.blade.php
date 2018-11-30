@auth
<nav class="navbar navbar-lg navbar-expand-lg navbar-dark bg-primary fixed-top shadow">
    <div class="container">
        {{--<a class="navbar-brand" href="javascript:void(0);">@yield('title')</a>--}}

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item {{ (request()->route()->getName() == 'appointment.list') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('appointment.list') }}">{{ __('Appointments') }}</a>
                </li>
                <li class="nav-item {{ (request()->route()->getName() == 'system-parameters.edit') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('system-parameters.edit') }}">{{ __('System Parameters') }}</a>
                </li>
            </ul>

            <a class="btn btn-sm btn-outline-light mt-2 mt-lg-0" href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</nav>
@endauth