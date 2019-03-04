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
            <h4 class="mb-3">{{ __('Branch creation') }}</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{ route('branch.store') }}" id="branchCreateForm" enctype="multipart/form-data">
                @csrf

                <div class="form-row mb-3">
                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                        <label for="name" class="mb-1">{{ __('Name') }}</label>

                        <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" />

                        @if ($errors->has('name'))
                        <div class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('name') }}</strong>
                        </div>
                        @endif
                    </div>

                    <div class="col">
                        <label for="amount_appointments_by_time" class="mb-1">{{ __('Appointments by Time') }}</label>

                        <input id="amount_appointments_by_time" type="number" class="form-control{{ $errors->has('amount_appointments_by_time') ? ' is-invalid' : '' }}" name="amount_appointments_by_time" value="{{ old('amount_appointments_by_time') }}" min="1" />

                        @if ($errors->has('amount_appointments_by_time'))
                        <div class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('amount_appointments_by_time') }}</strong>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="form-row mb-3">
                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                        <label for="city" class="mb-1">{{ __('City') }}</label>

                        <input id="city" type="text" class="form-control{{ $errors->has('city') ? ' is-invalid' : '' }}" name="city" value="{{ old('city') }}" />

                        @if ($errors->has('city'))
                        <div class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('city') }}</strong>
                        </div>
                        @endif
                    </div>

                    <div class="col">
                        <label for="province" class="mb-1">{{ __('Province') }}</label>

                        <input id="province" type="text" class="form-control{{ $errors->has('province') ? ' is-invalid' : '' }}" name="province" value="{{ old('province') }}" />

                        @if ($errors->has('province'))
                        <div class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('province') }}</strong>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="form-row mb-3">
                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                        <label for="country" class="mb-1">{{ __('Country') }}</label>

                        <input id="country" type="text" class="form-control{{ $errors->has('country') ? ' is-invalid' : '' }}" name="country" value="{{ old('country') }}" />

                        @if ($errors->has('country'))
                        <div class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('country') }}</strong>
                        </div>
                        @endif
                    </div>

                    <div class="col">
                        <label for="country_code" class="mb-1">{{ __('Country Code') }}</label>

                        <input id="country_code" type="text" class="form-control{{ $errors->has('country_code') ? ' is-invalid' : '' }}" name="country_code" value="{{ old('country_code') }}" />

                        @if ($errors->has('country_code'))
                        <div class="invalid-feedback d-block" role="alert">
                            <strong>{{ $errors->first('country_code') }}</strong>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="list-group">
                    @php
                    $oDate = new Date();
                    $oDate->modify('first day of this month')->modify('+1 day')->modify('last Saturday');
                    @endphp

                    <div class="list-group-item list-group-item-dark p-2 text-center">{{ __('Working week') }}</div>

                    <div class="list-group-item list-group-item-secondary p-1 d-none d-md-block">
                        <div class="form-row text-center">
                            <div class="col-4">{{ __('Day') }}</div>
                            <div class="col-4">{{ __('From') }}</div>
                            <div class="col-4">{{ __('Until') }}</div>
                        </div>
                    </div>

                    @for ($iWeekDay=0; $iWeekDay<7; $iWeekDay++)
                        <div class="list-group-item p-2">
                            <div class="form-row">
                                <div class="col-12 col-md-4 align-self-center">
                                    <input id="day_number_{{ $iWeekDay }}" type="hidden" class="form-control" name="day_number[{{ $iWeekDay }}]" value="{{ $iWeekDay }}" />
                                    <div class="mb-1 my-md-0 form-check">
                                        <div class="form-group my-auto">
                                            <input id="is_working_day_{{ $iWeekDay }}" type="checkbox" class="form-check-input" name="is_working_day[{{ $iWeekDay }}]" value="1" />
                                            <label class="mb-0 my-md-0 font-weight-bold text-muted">{{ $oDate->format('l') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 align-self-center">
                                    <label class="mb-1 my-md-0 d-block d-md-none">{{ __('From') }}</label>
                                    <input id="from_{{ $iWeekDay }}" type="text" class="form-control" name="from[{{ $iWeekDay }}]" value="" />
                                </div>
                                <div class="col-12 col-md-4 align-self-center">
                                    <label class="mb-0 my-md-0 d-block d-md-none">{{ __('Until') }}</label>
                                    <input id="until_{{ $iWeekDay }}" type="text" class="form-control" name="until[{{ $iWeekDay }}]" value="" />
                                </div>
                            </div>
                        </div>

                        @php
                        $oDate->modify('+1 day');
                        @endphp
                    @endfor
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

@section('page-scripts')
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
    jQuery('#branchCreateForm').submit(function() {
        jQuery('button[type=submit]', this)
            .html('{{ __('Processing') }}...')
            .attr('disabled', 'disabled');

        return true;
    });
});
</script>
@endsection