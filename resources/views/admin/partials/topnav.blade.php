@auth
<nav class="navbar navbar-lg navbar-expand-lg navbar-dark bg-primary fixed-top shadow">
    <div class="container">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarCollapse">
            @if (Auth::user()->hasRole(['Admin', 'Sysadmin']))
            <a class="btn btn-outline-light mt-3 mb-1 my-lg-0" href="{{ route('book.index') }}">{{ __('Book online') }}</a>
            @endif

            <ul class="navbar-nav mx-auto">
                @can('admin.appointment.list')
                <li class="nav-item {{ (in_array(request()->route()->getName(), ['appointment.list', 'appointment.reschedule'])) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('appointment.list') }}">{{ __('Appointments') }}</a>
                </li>
                @endcan

                @can(['admin.system-parameters.edit', 'admin.system-parameters.update'])
                <li class="nav-item {{ (request()->route()->getName() == 'system-parameters.edit') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('system-parameters.edit') }}">{{ __('System Parameters') }}</a>
                </li>
                @endcan

                @can('admin.exception.list')
                <li class="nav-item {{ (in_array(request()->route()->getName(), ['exception.list', 'exception.create', 'exception.edit'])) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('exception.list') }}">{{ __('Exceptions') }}</a>
                </li>
                @endcan

                @can(['admin.site-parameters.edit', 'admin.site-parameters.update'])
                <li class="nav-item {{ (request()->route()->getName() == 'site-parameters.edit') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('site-parameters.edit') }}">{{ __('Site Parameters') }}</a>
                </li>
                @endcan

                @can('admin.price.list')
                <li class="nav-item {{ (in_array(request()->route()->getName(), ['price.list', 'price.create', 'price.edit'])) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('price.list') }}">{{ __('Prices') }}</a>
                </li>
                @endcan

                @can('admin.user.list')
                <li class="nav-item {{ (in_array(request()->route()->getName(), ['user.list', 'permission.edit', 'permission.update'])) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('user.list') }}">{{ __('Users') }}</a>
                </li>
                @endcan
            </ul>

            <div class="dropdown mt-2 mt-lg-0">
                <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}</button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
@endauth