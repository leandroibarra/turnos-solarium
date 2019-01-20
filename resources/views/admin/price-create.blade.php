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
            <h4 class="mb-3">{{ __('Price creation') }}</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{ route('price.store') }}" id="priceCreateForm">
                @csrf

                <div class="form-row mb-3">
                    <div class="col-12 col-md-7 mb-3 mb-md-0">
                        <label for="title" class="mb-1">{{ __('Title') }}</label>

                        <input id="title" type="text" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" name="title" value="{{ old('title') }}" />

                        @if ($errors->has('title'))
                        <div class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('title') }}</strong>
                        </div>
                        @endif
                    </div>

                    <div class="col">
                        <label for="price" class="mb-1">{{ __('Price') }}</label>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text {{ $errors->has('price') ? ' is-invalid' : '' }}">$</div>
                            </div>
                            <input id="price" type="text" class="form-control{{ $errors->has('price') ? ' is-invalid' : '' }}" name="price" value="{{ old('price') }}" />
                        </div>

                        @if ($errors->has('price'))
                        <div class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('price') }}</strong>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="mb-1">{{ __('Description') }}</label>

                    <textarea id="description" name="description" class="form-control no-resize {{ $errors->has('description') ? ' is-invalid' : '' }}" rows="8">{{ old('description') }}</textarea>

                    @if ($errors->has('description'))
                    <div class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('description') }}</strong>
                    </div>
                    @endif
                </div>

                <div class="form-group my-3 text-center">
                    <button type="submit" class="btn btn-block btn-primary shadow-none">{{ __('Create') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
<script src="{{ asset('js/jquery.mask-1.14.15.js') }}" type="text/javascript"></script>

<script type="text/javascript">
jQuery(document).ready(function() {
    // Define mask to price input
    jQuery('#price').mask(
        '00{{ $sThousandsSeparator }}000{{ $sDecimalPointSeparator }}00',
        {
            reverse: true
        }
    );

    // Prevent multiple clicks
    jQuery('#priceCreateForm').submit(function() {
        jQuery('button[type=submit]', this)
            .html('{{ __('Processing') }}...')
            .attr('disabled', 'disabled');

        return true;
    });
});
</script>
@endsection