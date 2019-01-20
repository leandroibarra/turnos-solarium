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
            <h4 class="mb-3">{{ __('Exception creation') }}</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{ route('exception.store') }}" id="exceptionCreateForm">
                @csrf

                <div class="form-group">
                    <label for="datetimes" class="mb-1">{{ __('Date and time range') }}</label>

                    <input id="datetimes" type="text" class="form-control{{ $errors->has('datetimes') ? ' is-invalid' : '' }}" name="datetimes" value="{{ old('datetimes') }}" />

                    @if ($errors->has('datetimes'))
                    <div class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('datetimes') }}</strong>
                    </div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="type" class="mb-1">{{ __('Type') }}</label>

                    <div class="form-check">
                        <input id="type_holiday" type="radio" class="form-check-input" name="type" value="holiday"
                            {{ (old('type') == 'holiday') ? 'checked' : '' }}
                            {{ (!in_array(old('type'), ['holiday', 'other'])) ? 'checked' : '' }}
                        />

                        <label class="form-check-label" for="type_holiday">{{ __('Holiday') }}</label>
                    </div>

                    <div class="form-check">
                        <input id="type_other" type="radio" class="form-check-input" name="type" value="other"
                            {{ (old('type') == 'other') ? 'checked' : '' }}
                        />

                        <label class="form-check-label" for="type_other">{{ __('Other') }}</label>
                    </div>

                    @if ($errors->has('type'))
                    <div class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('type') }}</strong>
                    </div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="observations" class="mb-1">{{ __('Observations') }}</label>

                    <textarea class="form-control" id="observations" name="observations" rows="5">{{ old('observations') }}</textarea>
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
<link href="{{ asset('plugins/daterangepicker-3.0.3/daterangepicker.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('page-scripts')
<script src="{{ asset('js/moment-with-locales-2.18.1.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/daterangepicker-3.0.3/daterangepicker.js') }}" type="text/javascript"></script>

<script type="text/javascript">
jQuery(document).ready(function() {
    // Set moment locale
    moment.locale('{{ app()->getLocale() }}');

    // Initialize date picker range
    jQuery('input[name="datetimes"]').daterangepicker({
        timePicker: true,
        timePicker24Hour: true,
        timePickerIncrement: {{ $iAppointmentMinutes }},
        timePickerSeconds: false,
        minDate: moment().startOf('day'),
        startDate: setDateTime('start'),
        endDate: setDateTime('end'),
        locale: {
            format: '{{ __('YYYY-MM-DD HH:mm') }}',
            separator: '{{ __(' - ') }}',
            applyLabel: '{{ __('Ready') }}',
            cancelLabel: '{{ __('Cancel') }}'
        }
    });

    function setDateTime(psType) {
        var sDateTime = '';

        if ('{{ old('datetimes') }}' != '') {
            var sFromFormat = '{{ __('YYYY-MM-DD HH:mm') }}';
            var aDateParts = sFromFormat.split(' ').shift().split(sFromFormat.indexOf('/') ? '/' : '-');
            var aHourParts = sFromFormat.split(' ').pop().split(':');

            var aDateTimes = '{{ old('datetimes') }}'.split('{{ __(' - ') }}');
            var sDateTimeToConvert = '';

            switch (psType) {
                case 'start':
                    sDateTimeToConvert = aDateTimes.shift();
                    break;
                case 'end':
                    sDateTimeToConvert = aDateTimes.pop();
                    break;
            }

            var sRegExp = sFromFormat
                .replace(' ', '\\s')
                .replace('DD', '(\\d{2})')
                .replace('MM', '(\\d{2})')
                .replace('YYYY', '(\\d{4})')
                .replace('HH', '(\\d{2})')
                .replace('mm', '(\\d{2})');

            if (sFromFormat.indexOf('/'))
                sRegExp = sRegExp.replace(/\//g, '\\/');

            var aMatches = sDateTimeToConvert.match(new RegExp(sRegExp));

            sDateTime = moment(
                [
                    aMatches[aDateParts.indexOf('YYYY') + 1],
                    aMatches[aDateParts.indexOf('MM') + 1],
                    aMatches[aDateParts.indexOf('DD') + 1]
                ].join('-')
                + ' ' +
                [
                    aMatches[aDateParts.length + aHourParts.indexOf('HH') + 1],
                    aMatches[aDateParts.length + aHourParts.indexOf('mm') + 1]
                ].join(':')
            );
        } else {
            switch (psType) {
                case 'start':
                    sDateTime = moment().startOf('day');
                    break;
                case 'end':
                    sDateTime = moment().startOf('day').add(2, 'day');
                    break;
            }
        }

        return sDateTime;
    }

    // Prevent multiple clicks
    jQuery('#exceptionCreateForm').submit(function() {
        jQuery('button[type=submit]', this)
            .html('{{ __('Processing') }}...')
            .attr('disabled', 'disabled');

        return true;
    });
});
</script>
@endsection