@extends('layouts.admin')

@section('content')
<div class="container mt-3">
    <div class="row">
        <div class="col-12">
            @include('flash::message')
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{ route('site-parameters.update', ['id' => $aSiteParameter['id']]) }}" id="siteParametersForm">
                @method('PUT')

                @csrf

                <div class="form-group">
                    <label for="about_tanning_text" class="mb-0">{{ __('About our tanning text') }}</label>
                    <small class="form-text text-muted mt-0 mb-2">{{ __('Text will be displayed in "About our tanning" section') }}</small>

                    <textarea id="about_tanning_text" name="about_tanning_text" class="form-control no-resize" rows="8">{{ html_entity_decode(old('about_tanning_text', $aSiteParameter['about_tanning_text'])) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="mb-0">{{ __('Social network URLs') }}</label>
                    <small class="form-text text-muted mt-0 mb-2">{{ __('Social network URLs used for icons link') }}</small>

                    <div class="row">
                        @foreach (['pinterest', 'facebook', 'twitter', 'instagram'] as $sSocialNetwork)
                        <div class="col-12 col-lg-3">
                            <label for="{{ $sSocialNetwork }}_url" class="mb-1">{{ __(ucfirst($sSocialNetwork)) }}</label>

                            <input id="{{ $sSocialNetwork }}_url" name="{{ $sSocialNetwork }}_url" type="text"
                                   class="form-control{{ $errors->has($sSocialNetwork.'_url') ? ' is-invalid' : '' }} mb-2"
                                   value="{{ old($sSocialNetwork.'_url', $aSiteParameter[$sSocialNetwork.'_url']) }}" />

                            @if ($errors->has($sSocialNetwork.'_url'))
                            <div class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first($sSocialNetwork.'_url') }}</strong>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-group my-3 text-center">
                    <button type="submit" class="btn btn-block btn-primary shadow-none">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script type="text/javascript">
jQuery(document).ready(function() {
    @php
    $aLang = [
        'en' => 'en-US',
        'es' => 'es-ES'
    ];
    @endphp

    var sLang = '{{ $aLang[app()->getLocale()] }}';

    // Summernote configs
    jQuery('#about_tanning_text').summernote({
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol']],
            ['insert', ['link']]
        ],
        lang: sLang,
        height: 200
    });

    // Prevent multiple clicks
    jQuery('#siteParametersForm').submit(function() {
        jQuery('button[type=submit]', this)
            .html('{{ __('Processing') }}...')
            .attr('disabled', 'disabled');

        return true;
    });
});
</script>
@endpush