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
            <form method="POST" action="{{ route('system-parameters.update', ['id' => $aSystemParameter['id']]) }}">
                @method('PUT')

                @csrf

                <div class="form-group">
                    <label for="appointment_minutes" class="mb-0">{{ __('Appointment duration') }}</label>
                    <small class="form-text text-muted mt-0 mb-2">{{ __('Minutes of each appointment to be shown in the calendar') }}</small>

                    <input id="appointment_minutes" name="appointment_minutes" type="text"
                           class="form-control{{ $errors->has('appointment_minutes') ? ' is-invalid' : '' }}"
                           value="{{ old('appointment_minutes', $aSystemParameter['appointment_minutes']) }}" disabled readonly />

                    @if ($errors->has('appointment_minutes'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('appointment_minutes') }}</strong>
                    </span>
                    @endif
                </div>
                <div class="form-group">
                    <label for="appointment_until_days" class="mb-0">{{ __('Reservation days limit') }}</label>
                    <small class="form-text text-muted mt-0 mb-2">{{ __('Number of days that will be enabled in the calendar to reserve appointments from the current date') }}</small>

                    <select id="appointment_until_days" name="appointment_until_days" class="form-control{{ $errors->has('appointment_until_days') ? ' is-invalid' : '' }}">
                        @foreach ([15, 30, 60, 90, 120] as $iDay)
                        <option value="{{ $iDay }}" {{ ($iDay == old('appointment_until_days', $aSystemParameter['appointment_until_days'])) ? 'selected' : '' }}>{{ $iDay }}</option>
                        @endforeach
                    </select>

                    @if ($errors->has('appointment_until_days'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('appointment_until_days') }}</strong>
                    </span>
                    @endif
                </div>
                <div class="form-group">
                    <label for="appointment_confirmed_email_subject" class="mb-0">{{ __('Confirmation email subject') }}</label>
                    <small class="form-text text-muted mt-0 mb-2">{{ __('Email subject that will be sent after booking an appointment') }}</small>

                    <input id="appointment_confirmed_email_subject" name="appointment_confirmed_email_subject" type="text"
                           class="form-control{{ $errors->has('appointment_confirmed_email_subject') ? ' is-invalid' : '' }}"
                           value="{{ old('appointment_confirmed_email_subject', $aSystemParameter['appointment_confirmed_email_subject']) }}" />

                    @if ($errors->has('appointment_confirmed_email_subject'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('appointment_confirmed_email_subject') }}</strong>
                    </span>
                    @endif
                </div>
                <div class="form-group">
                    <label for="appointment_confirmed_email_body" class="mb-0">{{ __('Confirmation email body') }}</label>
                    <small class="form-text text-muted mt-0 mb-1">{{ __('Email body that will be sent after booking an appointment in which you can use the following tags that will be replaced with corresponding data') }}:</small>
                    <small class="form-text text-muted mt-0 mb-2">
                        <span class="font-weight-bold mr-1">@_NAME_@</span>{{ __('Name of the user who booked the appointment') }}<br />
                        <span class="font-weight-bold mr-1">@_DATE_@</span>{{ __('Date of the appointment booked (For example: 21 of January)') }}<br />
                        <span class="font-weight-bold mr-1">@_TIME_@</span>{{ __('Time of the appointment booked (For example: 16:30 pm)')  }}
                    </small>

                    <textarea id="appointment_confirmed_email_body" name="appointment_confirmed_email_body"
                              class="form-control{{ $errors->has('appointment_confirmed_email_body') ? ' is-invalid' : '' }} no-resize">
                        {{ html_entity_decode(old('appointment_confirmed_email_body', $aSystemParameter['appointment_confirmed_email_body'])) }}
                    </textarea>

                    @if ($errors->has('appointment_confirmed_email_body'))
                    <span class="invalid-feedback appointment_confirmed_email_body" role="alert">
                        <strong>{{ $errors->first('appointment_confirmed_email_body') }}</strong>
                    </span>
                    @endif
                </div>

                <div class="form-group my-3 text-center">
                    <button type="submit" class="btn btn-block btn-primary shadow-none">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
<script type="text/javascript">
jQuery(document).ready(function() {
    // Summernote configs
    jQuery('#appointment_confirmed_email_body').summernote({
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['fontsize', ['fontsize']],
            ['para', ['paragraph']]
        ],
        lang: 'es-ES',
        height: 200,
        hint: {
            words: ['@_NAME_@', '@_DATE_@', '@_TIME_@'],
            match: /\B(\@\_\w{1,})$/,
            search: function (keyword, callback) {
                callback($.grep(this.words, function (item) {
                    return item.indexOf(keyword) === 0;
                }));
            }
        }
    });

    // Clean on submit form
    jQuery('form').on('submit', function() {
        if (jQuery(jQuery('#appointment_confirmed_email_body').summernote('code')).text().replace(/\s+/g, '').length == 0)
            jQuery('#appointment_confirmed_email_body').val('');
    });
});
</script>
@endsection