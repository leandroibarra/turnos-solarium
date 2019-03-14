@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center container-center">
        <div class="col-12 col-sm-10 col-md-8">
            <h2 class="text-center text-uppercase mb-3">{{ __('Administration') }}</h2>

            @include('flash::message')

            <form method="POST" action="{{ route('admin.create') }}" id="loginForm" class="loginForm">
                @csrf

                <div class="form-group mb-2">
                    <label for="email" class="mb-1 label-form">{{ __('E-Mail Address') }}</label>

                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" autofocus />

                    @if ($errors->has('email'))
                    <div class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </div>
                    @endif
                </div>

                <div class="form-group mb-2">
                    <label for="password" class="mb-1 label-form">{{ __('Password') }}</label>

                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" />

                    @if ($errors->has('password'))
                    <div class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('password') }}</strong>
                    </div>
                    @endif
                </div>

                <div class="form-group my-3 text-center">
                    <button type="submit" class="btn btn-block btn-primary shadow-none">{{ __('Enter') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script type="text/javascript">
jQuery(document).ready(function() {
    // Prevent multiple clicks
    jQuery('#loginForm').submit(function() {
        jQuery('button[type=submit]', this)
            .html('{{ __('Processing') }}...')
            .attr('disabled', 'disabled');

        return true;
    });
});
</script>
@endpush