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
                {{ __('Permissions').' '.__('of').' '.$aUser->email }}
            </h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{ route('permission.update', ['id' => $aUser->id]) }}" id="userPermissionForm">
                @method('PUT')

                @csrf

                <div class="form-group">
                    @if ($errors->has('permission'))
                    <div class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('permission') }}</strong>
                    </div>
                    @endif

                    @if ((bool) $aModulesPermissions)
                    <ul class="list-group">
                        @php
                        $iKey = 0;
                        $sCurrentModule = '';
                        @endphp

                        @foreach ($aModulesPermissions as $sModule=>$aModule)
                            @php
                            if ($sCurrentModule != $aModule) {
                                $sCurrentModule = $aModule;
                            @endphp
                            <li class="list-group-item list-group-item-secondary font-weight-bold text-center p-2 {{ ($iKey>0) ? 'mt-3' : '' }}">{{ __('permission.module.admin.'.$sModule) }}</li>
                            @php
                            }
                            @endphp

                            @foreach ($aModule as $sPermission)
                                @php
                                $iKey++;
                                @endphp
                                <li class="list-group-item p-2">
                                    <div class="form-check">
                                        <input id="permission[{{ $iKey }}]" name="permission[{{ $iKey }}]" type="checkbox"
                                               class="form-check-input" value="{{ $sPermission }}"
                                               {{ $aUser->hasPermissionTo($sPermission) ? 'checked' : '' }}
                                        />
                                        <label class="form-check-label" for="permission[{{ $iKey }}]">{{ __('permission.permission.'.$sPermission) }}</label>
                                    </div>
                                </li>
                            @endforeach
                        @endforeach
                    </ul>
                    @endif
                </div>

                <div class="form-group my-3 text-center">
                    <button type="submit" class="btn btn-block btn-primary shadow-none">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
<script type="text/javascript">
jQuery(document).ready(function() {
    // Prevent multiple clicks
    jQuery('#userPermissionForm').submit(function() {
        jQuery('button[type=submit]', this)
            .html('{{ __('Processing') }}...')
            .attr('disabled', 'disabled');

        return true;
    });
});
</script>
@endsection