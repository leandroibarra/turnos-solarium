@auth
@php
$sRouteName = request()->route()->getName();
@endphp

<nav class="navbar navbar-expand-lg navbar-dark bg-blue py-0">
    <div class="container">
        <div class="collapse navbar-collapse navMenu">
            @if (Auth::user()->hasRole(['Admin', 'Sysadmin', 'Employee']))
            <a class="btn btn-outline-light mb-2 mt-3 my-lg-2" href="{{ route('book.index') }}">{{ __('Book online') }}</a>
            @endif

            <ul class="navbar-nav mx-auto">
                @can (['admin.system-parameters.edit', 'admin.system-parameters.update'])
                <li class="nav-item {{ ($sRouteName == 'system-parameters.edit') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('system-parameters.edit') }}">{{ __('System Parameters') }}</a>
                </li>
                @endcan

                @can (['admin.site-parameters.edit', 'admin.site-parameters.update'])
                <li class="nav-item {{ ($sRouteName == 'site-parameters.edit') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('site-parameters.edit') }}">{{ __('Site Parameters') }}</a>
                </li>
                @endcan

                @can ('admin.price.list')
                <li class="nav-item {{ (in_array($sRouteName, ['price.list', 'price.create', 'price.edit'])) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('price.list') }}">{{ __('Prices') }}</a>
                </li>
                @endcan

                @can ('admin.slide.list')
                <li class="nav-item {{ (in_array($sRouteName, ['slide.list', 'slide.create', 'slide.edit'])) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('slide.list') }}">{{ __('Slides') }}</a>
                </li>
                @endcan

                @if (Auth::user()->hasRole('Sysadmin'))
                    @can ('admin.branch.list')
                    <li class="nav-item {{ (in_array($sRouteName, ['branch.list', 'branch.create', 'branch.edit'])) ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('branch.list') }}">{{ __('Branches') }}</a>
                    </li>
                    @endcan
                @endif

                @can ('admin.user.list')
                <li class="nav-item {{ (in_array($sRouteName, ['user.list', 'permission.edit', 'permission.update'])) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('user.list') }}">{{ __('Users') }}</a>
                </li>
                @endcan
            </ul>

            <div class="dropdown mb-3 mb-lg-0">
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
<nav class="navbar navbar-lg navbar-expand-lg navbar-dark bg-primary sticky-top shadow">
    <div class="container">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target=".navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse navMenu">
            @if (isset(current(request()->attributes)['oBranch']))
                @if (Auth::user()->hasRole(['Admin', 'Sysadmin']) && in_array($sRouteName, ['appointment.list', 'exception.list']))
                <div class="dropdown mt-3 mb-2 my-lg-0">
                    <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ current(request()->attributes)['oBranch']->name }}</button>
                    <div class="dropdown-menu change-branch-menu">
                        @php
                        foreach (current(request()->attributes)['oBranches'] as $oBranch) {
                            if ($oBranch->id == current(request()->attributes)['oBranch']->id)
                                continue;
                        @endphp
                        <a class="dropdown-item change-branch-item" href="javascript:void(0);" data-branch-id="{{ $oBranch->id }}" role="menuitem">{{ $oBranch->name }}</a>
                        @php
                        }
                        @endphp
                    </div>
                </div>
                @else
                <button class="btn btn-sm btn-outline-light mt-3 mb-2 my-lg-0 current-branch" type="button">{{ current(request()->attributes)['oBranch']->name }}</button>
                @endif
            @endif

            <ul class="navbar-nav mx-auto">
                @can ('admin.appointment.list')
                <li class="nav-item {{ (in_array($sRouteName, ['appointment.list', 'appointment.reschedule'])) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('appointment.list') }}">{{ __('Appointments') }}</a>
                </li>
                @endcan

                @can ('admin.exception.list')
                <li class="nav-item {{ (in_array($sRouteName, ['exception.list', 'exception.create', 'exception.edit'])) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('exception.list') }}">{{ __('Exceptions') }}</a>
                </li>
                @endcan
            </ul>
        </div>
    </div>
</nav>

@if (Auth::user()->hasRole(['Admin', 'Sysadmin']) && in_array($sRouteName, ['appointment.list', 'exception.list']))
    @push('page-scripts')
    <script type="text/javascript">
        // Prevent multiple clicks
        jQuery('.change-branch-item').click(function() {
            jQuery('.change-branch-menu')
                .prev()
                .removeClass('dropdown-toggle')
                .attr('disabled', true)
                .html('{{ __('Processing') }}...');

            jQuery.ajax({
                type: 'POST',
                url: '/admin/select-branch',
                data: {
                    _token: '{{ csrf_token() }}',
                    branch_id: jQuery(this).data('branch-id')
                },
                success: function(data) {
                    location.reload();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status==401 && jqXHR.responseJSON.message=='Unauthenticated.')
                        window.location.href = '{{ route('login') }}';
                }
            });
        });
    </script>
    @endpush
@endif

@endauth