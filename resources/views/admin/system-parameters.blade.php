@extends('layouts.admin')

@section('content')
<div class="container mt-3">
    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{ route('system-parameters.update', ['id' => $aSystemParameter['id']]) }}" id="systemParametersForm">
                @method('PUT')

                @csrf

                <div class="form-group">
                    <label for="appointment_minutes" class="mb-0">{{ __('Appointment duration') }}</label>
                    <small class="form-text text-muted mt-0 mb-2">{{ __('Minutes of each appointment to be shown in the calendar') }}</small>

                    <input id="appointment_minutes" name="appointment_minutes" type="text"
                           class="form-control{{ $errors->has('appointment_minutes') ? ' is-invalid' : '' }}"
                           value="{{ old('appointment_minutes', $aSystemParameter['appointment_minutes']) }}" disabled readonly />

                    @if ($errors->has('appointment_minutes'))
                    <div class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('appointment_minutes') }}</strong>
                    </div>
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
                    <div class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('appointment_until_days') }}</strong>
                    </div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="appointment_confirmed_email_subject" class="mb-0">{{ __('Confirmation email subject') }}</label>
                    <small class="form-text text-muted mt-0 mb-2">{{ __('Email subject that will be sent after booking an appointment') }}</small>

                    <input id="appointment_confirmed_email_subject" name="appointment_confirmed_email_subject" type="text"
                           class="form-control{{ $errors->has('appointment_confirmed_email_subject') ? ' is-invalid' : '' }}"
                           value="{{ old('appointment_confirmed_email_subject', $aSystemParameter['appointment_confirmed_email_subject']) }}" />

                    @if ($errors->has('appointment_confirmed_email_subject'))
                    <div class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('appointment_confirmed_email_subject') }}</strong>
                    </div>
                    @endif
                </div>

                @php
                $aEmails = [
                    'confirmed' => [
                        'subject' => [
                            'key' => 'appointment_confirmed_email_subject',
                            'label' => 'Confirmation email subject',
                            'small' => 'Email subject that will be sent after booking an appointment'
                        ],
                        'body' => [
                            'key' => 'appointment_confirmed_email_body',
                            'label' => 'Confirmation email body',
                            'small' => 'Email body that will be sent after booking an appointment in which you can use the following tags that will be replaced with corresponding data'
                        ]
                    ],
                    'cancelled' => [
                        'subject' => [
                            'key' => 'appointment_cancelled_email_subject',
                            'label' => 'Cancellation email subject',
                            'small' => 'Email subject that will be sent after an appointment cancellation from administration section'
                        ],
                        'body' => [
                            'key' => 'appointment_cancelled_email_body',
                            'label' => 'Cancellation email body',
                            'small' => 'Email body that will be sent after an appointment cancellation from administration section in which you can use the following tags that will be replaced with corresponding data'
                        ]
                    ]
                ];
                @endphp
                @foreach ($aEmails as $sKey=>$aEmail)
                <div class="form-group">
                    <label for="{{ $aEmail['subject']['key'] }}" class="mb-0">{{ __($aEmail['subject']['label']) }}</label>
                    <small class="form-text text-muted mt-0 mb-2">{{ __($aEmail['subject']['small']) }}</small>

                    <input id="{{ $aEmail['subject']['key'] }}" name="{{ $aEmail['subject']['key'] }}" type="text"
                           class="form-control{{ $errors->has($aEmail['subject']['key']) ? ' is-invalid' : '' }}"
                           value="{{ old($aEmail['subject']['key'], $aSystemParameter[$aEmail['subject']['key']]) }}" />

                    @if ($errors->has($aEmail['subject']['key']))
                    <div class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first($aEmail['subject']['key']) }}</strong>
                    </div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="{{ $aEmail['body']['key'] }}" class="mb-0">{{ __($aEmail['body']['label']) }}</label>
                    <small class="form-text text-muted mt-0 mb-1">{{ __($aEmail['body']['small']) }}:</small>
                    <small class="form-text text-muted mt-0 mb-2">
                        <span class="font-weight-bold mr-1">@_NAME_@</span>{{ __('Name of the user who booked the appointment') }}<br />
                        <span class="font-weight-bold mr-1">@_DATE_@</span>{{ __('Date of the appointment booked (For example: 21 of January)') }}<br />
                        <span class="font-weight-bold mr-1">@_TIME_@</span>{{ __('Time of the appointment booked (For example: 16:30 pm)') }}<br />
                        <span class="font-weight-bold mr-1">@_CITY_@</span>{{ __('City of the branch (For example: Buenos Aires)') }}<br />
                        <span class="font-weight-bold mr-1">@_ADDRESS_@</span>{{ __('Address of the branch (For example: 9 de Julio 1200)') }}<br />
                        <span class="font-weight-bold mr-1">@_PRICES_@</span>{{ __('Price list belonging to the branch') }}<br />
                        <span class="font-weight-bold mr-1">@_PHONE_@</span>{{ __('Phone number of the branch') }}<br />
                        <span class="font-weight-bold mr-1">@_EMAIL_@</span>{{ __('E-mail address of the branch') }}
                    </small>

                    <textarea id="{{ $aEmail['body']['key'] }}" name="{{ $aEmail['body']['key'] }}"
                              class="form-control{{ $errors->has($aEmail['body']['key']) ? ' is-invalid' : '' }} no-resize">
                        {{ html_entity_decode(old($aEmail['body']['key'], $aSystemParameter[$aEmail['body']['key']])) }}
                    </textarea>

                    @if ($errors->has($aEmail['body']['key']))
                    <div class="invalid-feedback {{ $aEmail['body']['key'] }} d-block" role="alert">
                        <strong>{{ $errors->first($aEmail['body']['key']) }}</strong>
                    </div>
                    @endif
                </div>
                @endforeach

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

    // Summernote elements configurations
    jQuery('{{ implode(', ', ['#'.$aEmails['confirmed']['body']['key'], '#'.$aEmails['cancelled']['body']['key']]) }}').summernote({
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['fontsize', ['fontsize']],
            ['para', ['paragraph']]
        ],
        lang: sLang,
        height: 200,
        hint: {
            words: ['@_NAME_@', '@_DATE_@', '@_TIME_@', '@_CITY_@', '@_ADDRESS_@', '@_PRICES_@', '@_PHONE_@', '@_EMAIL_@'],
            match: /\B(\@\_\w{1,})$/,
            search: function (keyword, callback) {
                callback($.grep(this.words, function (item) {
                    return item.indexOf(keyword) === 0;
                }));
            }
        }
    });

    // Clean on submit form
    jQuery('#systemParametersForm').on('submit', function() {
        @foreach (['#'.$aEmails['confirmed']['body']['key'], '#'.$aEmails['cancelled']['body']['key']] as $sBodyKey)
        if (jQuery(jQuery('{{ $sBodyKey  }}').summernote('code')).text().replace(/\s+/g, '').length == 0)
            jQuery('{{ $sBodyKey }}').val('');
        @endforeach

        // Prevent multiple clicks
        jQuery('button[type=submit]', this)
            .html('{{ __('Processing') }}...')
            .attr('disabled', 'disabled');

        return true;
    });
});
</script>
@endpush