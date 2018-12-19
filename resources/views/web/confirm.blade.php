@extends('layouts.web')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="text-center text-uppercase mt-0 mb-3">{{ __('Your data') }}</h2>
            <h5 class="text-center text-md-left mt-0 mb-3">{{ __('To complete the reservation, please complete the following data') }}:</h5>
        </div>
    </div>

    <form method="POST" action="{{ route('appointment.store') }}" id="confirmForm" class="appointment-confirm-form">
        @csrf

        <div class="row">
            <div class="col-12 col-md-9">
                <div class="row">
                    <div class="col-6 col-md-6 mb-2">
                        <a href="{{ route('book.index') }}">
                            <i class="arrow left"></i>
                            <span>{{ __('Choose other appointment') }}</span>
                        </a>
                    </div>
                    <div class="col-6 col-md-6 mb-1 text-right text-muted text-nowrap">
                        <span class="text-danger font-weight-bold">*</span> {{ __('Mandatory fields') }}
                    </div>
                </div>

                <div class="form-group mb-2">
                    <label for="email" class="mb-1 label-form">{{ __('E-Mail Address') }} <span class="text-danger font-weight-bold">*</span></label>

                    <input id="email" type="email" class="form-control" name="email" value="{{ Auth::user()->email }}" disabled />
                </div>

                <div class="form-group mb-2">
                    <label for="name" class="mb-1 label-form">{{ __('Name') }} <span class="text-danger font-weight-bold">*</span></label>

                    <input id="name" type="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name', Auth::user()->name) }}" />

                    @if ($errors->has('name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                    @endif
                </div>

                <div class="form-group mb-2">
                    <label for="phone" class="mb-1 label-form">{{ __('Phone Number') }} <span class="text-danger font-weight-bold">*</span></label>

                    <input id="phone" type="phone" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{ old('phone') }}" />

                    @if ($errors->has('phone'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('phone') }}</strong>
                    </span>
                    @endif

                    <small class="form-text text-muted my-0">{{ __('You need to specify the area code and phone number, for example: +54 011 4444-0000 or 341 1118888') }}</small>
                </div>

                <div class="form-group mb-2">
                    <label for="comment" class="mb-1 label-form">{{ __('Comment') }}</label>

                    <textarea class="form-control" id="comment" name="comment" rows="5">{{ old('comment') }}</textarea>
                </div>
            </div>

            <div class="col-12 col-md-3 appointment-container">
                <div class="border p-3 mt-2 mt-md-0 appointment-content">
                    <h5 class="text-center">{{ __('Selected appointment') }}</h5>
                    <div>
                        <span class="d-block-inline-block d-md-block d-lg-inline-block mr-0 mr-md-1 font-weight-bold">{{ __('Date') }}:</span>
                        <span class="d-block-inline-block d-md-block d-lg-inline-block">{{ $oDateTime->format('d').' '.__('of').' '.$oDateTime->format('F') }}</span>
                    </div>
                    <div class="mt-1 mt-md-0">
                        <span class="d-block-inline-block d-md-block d-lg-inline-block mr-0 mr-md-1 font-weight-bold">{{ __('Time') }}: </span>
                        <span class="d-block-inline-block d-md-block d-lg-inline-block">{{ $oDateTime->format('H:ia') }}</span>
                    </div>
                    <div class="mt-1 text-center">
                        <button type="submit" class="btn btn-block btn-gold">{{ __('Book') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('page-scripts')
<script type="text/javascript">
jQuery(document).ready(function() {
    // Prevent multiple clicks
    jQuery('#confirmForm').submit(function() {
        jQuery('button[type=submit]', this)
            .html('{{ __('Processing') }}...')
            .attr('disabled', 'disabled');

        return true;
    });
});
</script>
@endsection