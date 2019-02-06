@extends('layouts.web')

@section('content')
<div class="container">
    <div class="row justify-content-center container-center">
        <div class="col-12 col-sm-10 col-md-8">
            <h2 class="text-center text-uppercase mb-3">{{ __('Reset Password') }}</h2>

            @if (session('status'))
            <div class="alert alert-success text-center" role="alert">
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" id="emailForm">
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

                <div class="form-group my-3 text-center">
                    <button type="submit" class="btn btn-block btn-gold">{{ __('Send password reset link') }}</button>
                </div>

                <div class="form-group mb-0 text-center form-links">
                    <span class="mr-0 mr-sm-1">{{__('Do you already have an account?') }}</span>
                    <a href="{{ route('login') }}">{{ __('Login') }}</a>
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
    jQuery('#emailForm').submit(function() {
        jQuery('button[type=submit]', this)
            .html('{{ __('Processing') }}...')
            .attr('disabled', 'disabled');

        return true;
    });
});
</script>
@endsection