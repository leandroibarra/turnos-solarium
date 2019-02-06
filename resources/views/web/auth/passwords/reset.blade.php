@extends('layouts.web')

@section('content')
<div class="container">
    <div class="row justify-content-center container-center">
        <div class="col-12 col-sm-10 col-md-8">
            <h2 class="text-center text-uppercase mb-3">{{ __('Reset Password') }}</h2>

            <form method="POST" action="{{ route('password.update') }}" id="resetForm">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group mb-2">
                    <label for="email" class="mb-1 label-form">{{ __('E-Mail Address') }}</label>

                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" autofocus />

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

                <div class="form-group mb-2">
                    <label for="password-confirm" class="mb-1 label-form">{{ __('Confirm Password') }}</label>

                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" />
                </div>

                <div class="form-group my-3 text-center">
                    <button type="submit" class="btn btn-block btn-gold">{{ ucfirst(__('Reset Password')) }}</button>
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
    jQuery('#resetForm').submit(function() {
        jQuery('button[type=submit]', this)
            .html('{{ __('Processing') }}...')
            .attr('disabled', 'disabled');

        return true;
    });
});
</script>
@endsection