@extends('layouts.web')

@section('content')
<div class="container">
    <div class="row justify-content-center container-center">
        <div class="col-12 col-sm-10 col-md-8">
            <h2 class="text-center text-uppercase mb-3">{{ __('Login') }}</h2>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group mb-2">
                    <label for="email" class="mb-1 label-form">{{ __('E-Mail Address') }}</label>

                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" autofocus />

                    @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                    @endif
                </div>

                <div class="form-group mb-2">
                    <label for="password" class="mb-1 label-form">{{ __('Password') }}</label>

                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" />

                    @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                    @endif
                </div>

                <div class="form-group my-3 text-center">
                    <button type="submit" class="btn btn-block btn-gold">{{ __('Login') }}</button>
                </div>

                @if (Route::has('register'))
                <div class="form-group mb-0 text-center form-links">
                    <span class="mr-0 mr-sm-1">{{__("You don't have an account yet?") }}</span>
                    <a href="{{ route('register') }}">{{ __('Register') }}</a>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
