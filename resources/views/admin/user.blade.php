@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row mt-3">
        <div class="col-12">
            @include('flash::message')
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h4 class="mb-3">
                {{ __('Users') }}
            </h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <ul class="list-group">
            @php
            $iColMd = 12;

            if ((Auth::user()->can('admin.permission.edit') && Auth::user()->can('admin.permission.update')))
                $iColMd -= 2;
            @endphp

            @foreach ($aUsers as $aUser)
                <li class="list-group-item p-2">
                    <div class="row">
                        <div class="col-12 col-md-{{ $iColMd }} align-self-center text-center text-md-left">
                            <div class="row">
                                <div class="col-12 col-sm-6 col-md-4 text-center text-sm-right text-md-left">
                                    <i class="far fa-user text-muted mr-2"></i>{{ (!is_null($aUser->name)) ? $aUser->name : '-' }}
                                </div>
                                <div class="col col-md-8 text-center text-sm-left">
                                    <i class="far fa-envelope text-muted mr-2"></i>{{ $aUser->email }}
                                    @if (Auth::user()->id == $aUser->id)
                                        <span class="badge badge-success">{{ __('You') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6 col-md-4 text-center text-sm-right text-md-left">
                                    <i class="fas fa-mobile-alt text-muted mr-2"></i>{{ (!is_null($aUser->phone)) ? $aUser->phone : '-' }}
                                </div>
                                <div class="col col-md-8 text-center text-sm-left">
                                    @php
                                        $aRoles = $aUser->getRoleNames();
                                    @endphp

                                    @if (!$aRoles->isEmpty())
                                        @foreach ($aRoles as $sRole)
                                            @php
                                                $sClass = '';

                                                switch ($sRole) {
                                                    case 'Sysadmin':
                                                        $sClass = 'danger';
                                                        break;
                                                    case 'Admin':
                                                        $sClass = 'warning';
                                                        break;
                                                    default:
                                                        $sClass = 'info';
                                                        break;
                                                }
                                            @endphp
                                            <span class="badge badge-{{ $sClass }}">{{ $sRole }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if ((Auth::user()->can('admin.permission.edit') && Auth::user()->can('admin.permission.update')))
                        <div class="col-12 col-md-2 align-self-center text-center text-md-right mt-2 mt-md-0">
                            @can (['admin.permission.edit', 'admin.permission.update'])
                            <a href="{{ route('permission.edit', ['id' => $aUser->id ]) }}" class="btn btn-sm btn-secondary" title="{{ __('Permissions') }}" role="button">
                                <i class="fas fa-user-check"></i>
                            </a>
                            @endcan
                        </div>
                        @endif
                    </div>
                </li>
            @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection