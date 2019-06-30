@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row mt-3">
        <div class="col-12">
            <h4 class="mb-3">{{ __('Schedule edition') }}</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{ route('schedule.update') }}" id="branchWorkingWeekEditForm" enctype="multipart/form-data">
                @method('PUT')

                @csrf

                <div class="list-group">
                    @php
                    $oDate = new Date();
                    $oDate->modify('first day of this month')->modify('+1 day')->modify('last Sunday');
                    @endphp

                    <div class="list-group-item list-group-item-secondary p-1 d-none d-md-block">
                        <div class="form-row text-center">
                            <div class="col-4">{{ __('Day') }}</div>
                            <div class="col-4">{{ __('From') }}</div>
                            <div class="col-4">{{ __('Until') }}</div>
                        </div>
                    </div>

                    @for ($iWeekDay=0; $iWeekDay<7; $iWeekDay++)
                        @php
                        $oWorkingDay = $oBranch->workingWeek->get($iWeekDay);
                        @endphp
                        <div class="list-group-item p-2">
                            <div class="form-row">
                                <div class="col-12 col-md-4 align-self-center">
                                    <input id="day_number.{{ $iWeekDay }}" type="hidden" class="form-control" name="day_number[{{ $iWeekDay }}]" value="{{ $iWeekDay }}" />
                                    <div class="mb-1 my-md-0 form-check">
                                        <div class="form-group my-auto">
                                            <input id="is_working_day.{{ $iWeekDay }}" type="checkbox" class="form-check-input"
                                                   name="is_working_day[{{ $iWeekDay }}]" data-day-number="{{ $iWeekDay }}"
                                                   {{ (old("is_working_day.{$iWeekDay}", $oWorkingDay->is_working_day)==1) ? 'checked="checked"' : '' }}
                                                   value="1" />
                                            <label class="mb-0 my-md-0 font-weight-bold text-muted">{{ $oDate->format('l') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 align-self-center">
                                    <label class="mb-1 my-md-0 d-block d-md-none">{{ __('From') }}</label>
                                    <div class="input-group date" id="from{{ $iWeekDay }}" data-target-input="nearest">
                                        <input id="from.{{ $iWeekDay }}" type="text" class="form-control{{ $errors->has("from.{$iWeekDay}") ? ' is-invalid' : '' }} datetimepicker-input"
                                               data-target="#from{{ $iWeekDay }}" name="from[{{ $iWeekDay }}]"
                                               readonly="readonly" {{ (old("is_working_day.{$iWeekDay}", $oWorkingDay->is_working_day)==1) ? '' : 'disabled' }}
                                               value="{{ old("from.{$iWeekDay}", $oWorkingDay->from) }}" />
                                        <div class="input-group-append" data-target="#from{{ $iWeekDay }}" data-toggle="datetimepicker">
                                            <div class="input-group-text{{ $errors->has("from.{$iWeekDay}") ? ' border-danger' : '' }}">
                                                <i class="far fa-clock"></i>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($errors->has("from.{$iWeekDay}"))
                                    <div class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $errors->first("from.{$iWeekDay}") }}</strong>
                                    </div>
                                    @endif
                                </div>
                                <div class="col-12 col-md-4 align-self-center">
                                    <label class="mb-0 my-md-0 d-block d-md-none">{{ __('Until') }}</label>
                                    <div class="input-group date" id="until{{ $iWeekDay }}" data-target-input="nearest">
                                        <input id="until.{{ $iWeekDay }}" type="text" class="form-control{{ $errors->has("until.{$iWeekDay}") ? ' is-invalid' : '' }} datetimepicker-input"
                                               data-target="#until{{ $iWeekDay }}" name="until[{{ $iWeekDay }}]"
                                               readonly="readonly" {{ (old("is_working_day.{$iWeekDay}", $oWorkingDay->is_working_day)==1) ? '' : 'disabled' }}
                                               value="{{ old("until.{$iWeekDay}", $oWorkingDay->until) }}" />
                                        <div class="input-group-append" data-target="#until{{ $iWeekDay }}" data-toggle="datetimepicker">
                                            <div class="input-group-text{{ $errors->has("until.{$iWeekDay}") ? ' border-danger' : '' }}">
                                                <i class="far fa-clock"></i>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($errors->has("until.{$iWeekDay}"))
                                    <div class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $errors->first("until.{$iWeekDay}") }}</strong>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @php
                        $oDate->modify('+1 day');
                        @endphp
                    @endfor
                </div>

                <div class="form-group my-3 text-center">
                    <button type="submit" class="btn btn-block btn-primary shadow-none">{{ __('Edit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-styles')
<link href="{{ asset('plugins/tempusdominus-5.0.1/tempusdominus-bootstrap-4.css') }}" rel="stylesheet" type="text/css" />
@endsection

@push('page-scripts')
<script src="{{ asset('js/moment-with-locales-2.18.1.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/tempusdominus-5.0.1/tempusdominus-bootstrap-4.js') }}" type="text/javascript"></script>

<script type="text/javascript">
jQuery(document).ready(function() {
    // Set moment locale
    moment.locale('{{ app()->getLocale() }}');

    @for ($iWeekDay=0; $iWeekDay<7; $iWeekDay++)
    // Initialize date time picker range
    jQuery('#from{{ $iWeekDay }}, #until{{ $iWeekDay }}').datetimepicker({
        format: 'HH:mm',
        ignoreReadonly: true,
        stepping: '{{ $iAppointmentMinutes }}'
    });

    jQuery('#from{{ $iWeekDay }}').on('hide.datetimepicker', function(event) {
        jQuery('#until{{ $iWeekDay }}').datetimepicker('minDate', event.date);
    });
    jQuery('#until{{ $iWeekDay }}').on('hide.datetimepicker', function(event) {
        jQuery('#from{{ $iWeekDay }}').datetimepicker('maxDate', event.date);
    });
    @endfor

    // Enable or disable from time input and until time input
    jQuery('input[name^=is_working_day]').click(function() {
        var iDayNumber = jQuery(this).data('day-number');

        if (jQuery(this).is(':checked'))
            jQuery(jQuery('[name="from[' + iDayNumber + ']"], [name="until[' + iDayNumber + ']"]')).removeAttr('disabled');
        else
            jQuery(jQuery('[name="from[' + iDayNumber + ']"], [name="until[' + iDayNumber + ']"]')).attr('disabled', true).val('');
    });

    // Prevent multiple clicks
    jQuery('#branchWorkingWeekEditForm').submit(function() {
        jQuery('button[type=submit]', this)
            .html('{{ __('Processing') }}...')
            .attr('disabled', 'disabled');

        return true;
    });
});
</script>
@endpush