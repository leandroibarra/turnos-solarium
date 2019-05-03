@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row mt-3">
        <div class="col-12">
            <h4 class="mb-3">{{ __('Slide creation') }}</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{ route('slide.store') }}" id="slideCreateForm" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="image" class="mb-1">{{ __('Image') }}</label>

                    <input id="image" type="file" class="form-control{{ $errors->has('image') ? ' is-invalid' : '' }}" name="image" />

                    @if ($errors->has('image'))
                    <div class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('image') }}</strong>
                    </div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="title" class="mb-1">{{ __('Title') }}</label>

                    <input id="title" type="text" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" name="title" value="{{ old('title') }}" />

                    @if ($errors->has('title'))
                        <div class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('title') }}</strong>
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

@section('page-styles')
<link href="{{ asset('plugins/fileinput-4.5.2/css/fileinput.css') }}" rel="stylesheet" type="text/css" />
@endsection

@push('page-scripts')
<script src="{{ asset('plugins/fileinput-4.5.2/js/fileinput.js') }}" type="text/javascript"></script>

<script type="text/javascript">
jQuery(document).ready(function() {
    // Define options to image input
    jQuery('#image').fileinput({
        language: '{{ app()->getLocale() }}',
        maxFileCount: 1,
        validateInitialCount: true,
        dropZoneTitle: '{{ __('Drag & drop the file here') }} &hellip;',
        showUpload: false,
        allowedFileExtensions: ['jpeg', 'jpg', 'png'],
        maxFileSize: 2048
    });

    // Prevent multiple clicks
    jQuery('#slideCreateForm').submit(function() {
        jQuery('button[type=submit]', this)
            .html('{{ __('Processing') }}...')
            .attr('disabled', 'disabled');

        return true;
    });
});
</script>
@endpush