@extends('layouts.web')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="text-center text-uppercase mt-0 mb-3">{{ __('Book online') }}</h2>

            @include('flash::message')
        </div>
    </div>

    @if (!$aEnabledPrices->isEmpty())
    <div class="row">
        @foreach (array_chunk($aEnabledPrices->toArray(), 4) as $aPrices)
            @php
            $sClassFirst = $sClassLast = '';

            switch (count($aPrices)) {
                case 1:
                    $sClassFirst = 'mx-md-auto';
                    break;
                case 2:
                    $sClassFirst = 'offset-lg-3';
                    break;
                case 3:
                    $sClassFirst = 'offset-lg-1';
                    $sClassLast = 'offset-md-3 offset-lg-0';
                    break;
            }
            @endphp
            @foreach ($aPrices as $iKey=>$aPrice)
            <div class="col-12 col-lg-3 col-md-6 {{ ($iKey==0) ? $sClassFirst : (($iKey==count($aPrices)-1) ? $sClassLast : '') }}">
                <div class="card-deck">
                    <div class="card mt-3 mb-4 box-shadow text-center">
                        <div class="card-header">
                            <h4 class="my-0 font-weight-normal">{{ $aPrice['title'] }}</h4>
                        </div>
                        <div class="card-body">
                            <h1 class="card-title pricing-card-title">
                                @php
                                $aPriceParts = explode($sDecimalPointSeparator, $aPrice['price']);
                                @endphp
                                $ <strong>{{ $aPriceParts[0] }}</strong>{{ $sDecimalPointSeparator }}<small>{{ $aPriceParts[1] }}</small>
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @endforeach
    </div>
    @endif

    <div class="row book-wrapper">
        <div class="col-12 col-md-9">
            <div class="calendar-container"></div>
        </div>

        <div class="col-12 col-md-3 appointment-container">
            <header class="d-none d-md-block">
                <div class="p-1">
                    <h2>&nbsp;</h2>
                </div>
            </header>
            <div class="border p-3 mt-2 mt-md-0 appointment-content">
                <div class="text-center text-md-left mb-2 font-weight-bold">{{ $oBranch->name }}</div>
                <div class="text-center text-md-left">{{ __('Select a day and choose the time you wish book.') }}</div>
            </div>
        </div>
    </div>

    @if (!$aGrantedAppointments->isEmpty())
    <div class="row">
        <div class="col-12">
            <h2 class="mt-4 mb-0">{{ __('Your appointments') }}</h2>
        </div>
    </div>

    <div class="row">
        @foreach ($aGrantedAppointments as $iKey=>$aAppointment)
            @php
            $sDateHeader = Date::createFromFormat('Y-m-d', $aAppointment->date)->format(__('l j \\of F'));
            $sTimeBody = Date::createFromFormat('H:i:s', $aAppointment->time)->format('H:i a');
            @endphp
        <div class="col-12 col-lg-3 col-md-4 mt-3" data-appointment-id="{{ $aAppointment->id }}">
            <div class="card-deck">
                <div class="card box-shadow">
                    <div class="card-body">
                        <div class="row">
                            <div class="col text-center align-self-center">
                                <i class="far fa-calendar text-muted mr-2"></i>{{ $sDateHeader }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col text-center align-self-center mt-2">
                                <i class="far fa-clock text-muted mr-2"></i>{{ $sTimeBody }}
                            </div>
                        </div>
                        @if ($aAppointment->date>date('Y-m-d') || ($aAppointment->date==date('Y-m-d') && $aAppointment->time>date('H:i:s')))
                        <div class="row">
                            <div class="col text-center align-self-center mt-2">
                                <button class="btn btn-sm btn-block btn-gold" title="{{ __('Cancel') }}"
                                        data-appointment-id="{{ $aAppointment->id }}"
                                        data-date-header="{{ $sDateHeader }}"
                                        data-time-body="{{ $sTimeBody }}"
                                        data-target="#cancelModal"
                                        data-toggle="modal"
                                >
                                    {{ __('Cancel') }}
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<div class="spinner-container d-none">
    <svg class="spinner" viewBox="0 0 50 50">
        <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
    </svg>
</div>

<!-- Modals -->
<div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"></h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-grey" data-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-gold" id="readyAppointment" disabled>{{ __('Ready') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">{{ __('Appointment cancellation') }}</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center"></div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-grey" data-dismiss="modal">{{ __('No') }}</button>
                <button type="button" class="btn btn-gold" id="readyCancel">{{ __('Yes') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
<script type="text/javascript">
jQuery(document).ready(function() {
    // Initial calendar
    drawCalendar('{{date('Y')}}', '{{date('m')}}');

    // Next or prev month clicks
    jQuery('.calendar-container').on('click', '.prev-month, .next-month', function(event) {
        event.preventDefault();
        event.stopPropagation();

        drawCalendar(jQuery(this).data('year'), jQuery(this).data('month'));
    });

    // Open appointment modal
    jQuery('#appointmentModal').on('show.bs.modal', function(event) {
        var oTarget = jQuery(event.relatedTarget);
        var oModal = jQuery(this);

        // Ajax request time
        var iRequest = new Date().getTime();

        // Show spinner
        oModal.find('.modal-body').html(
            '<div class="my-2 py-4">\n' +
            '    <svg class="spinner" viewBox="0 0 50 50">\n' +
            '        <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>\n' +
            '    </svg>\n' +
            '</div>'
        );

        oModal.find('.modal-title').text('{{ __('Select an hour to date') }}: ' + oTarget.data('day') + ' {{ __('of') }} ' + oTarget.data('month-label'));

        jQuery.ajax({
            type: 'GET',
            url: '/appointments/'+oTarget.data('year')+'/'+oTarget.data('month')+'/'+oTarget.data('day'),
            success: function(data) {
                // Ajax complete time
                var iComplete = new Date().getTime();

                // Calculate time remaining to hide loading
                var iRemainig = (iComplete-iRequest < 600) ? 600-(iComplete-iRequest) : 0;

                // Replace spinner with content
                setTimeout(function() {
                    oModal.find('.modal-body').html(data);
                }, iRemainig);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status==401 && jqXHR.responseJSON.message=='Unauthenticated.')
                    window.location.href = '{{ route('login') }}';
            }
        });
    });

    // Select appointment time and save data
    jQuery('.modal-body').on('click', '.appointment-hour', function() {
        var oThis = jQuery(this);

        if (!oThis.hasClass('active')) {
            // Remove other active appointment
            jQuery('.appointment-hour').removeClass('active');

            // Set active class to clicked appointment
            oThis.addClass('active');
        } else {
            oThis.removeClass('active');
        }

        if (jQuery('.appointment-hour.active').length > 0)
            jQuery('#readyAppointment').removeAttr('disabled');
        else
            jQuery('#readyAppointment').attr('disabled', true);

    });

    // Close appointment modal
    jQuery('#appointmentModal').on('hide.bs.modal', function(event) {
        jQuery('#readyAppointment').attr('disabled', true);
    });

    // Replace sidebar content and close appointment modal
    jQuery('#readyAppointment').click(function() {
        var oActive = jQuery('.appointment-hour.active');

        jQuery.ajax({
            type: 'POST',
            url: '/appointments/set',
            data: {
                _token: '{{ csrf_token() }}',
                date: [oActive.data('year'), oActive.data('month'), oActive.data('day')].join('-'),
                time: oActive.data('hour').replace(/\s(a|p)m/i, '')
            },
            success: function(data) {
                var sContent = '' +
                    '<h5 class="text-center">'+'{{ __('Selected appointment in :branchName', ['branchName' => $oBranch->name]) }}</h5>\n' +
                    '<div>\n' +
                    '   <span class="d-block-inline-block d-md-block d-lg-inline-block mr-0 mr-md-1 font-weight-bold">{{ __('Date') }}:</span>\n' +
                    '   <span class="d-block-inline-block d-md-block d-lg-inline-block">' + oActive.data('day').toString().replace(/^0+/ig, '') + ' {{ __('of') }} ' + oActive.data('month-label') + '</span>\n' +
                    '</div>\n' +
                    '<div class="mt-1 mt-md-0">\n' +
                    '   <span class="d-block-inline-block d-md-block d-lg-inline-block mr-0 mr-md-1 font-weight-bold">{{ __('Time') }}: </span>\n' +
                    '   <span class="d-block-inline-block d-md-block d-lg-inline-block">' + oActive.data('hour') + '</span>\n' +
                    '</div>\n' +
                    '<div class="mt-1 text-center">\n' +
                    '   <a href="{{ route('book.create') }}" role="button" class="btn btn-block btn-gold">{{ __('Confirm') }}</a>\n' +
                    '</div>\n';

                // Replace appointment sidebar content
                jQuery('.appointment-content').html(sContent);

                // Hide modal
                jQuery('#appointmentModal').modal('hide');

                // Apply scroll to appointment sidebar
                jQuery('html').animate({
                    scrollTop: jQuery('.appointment-content').find('a[role="button"]').offset().top
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status==401 && jqXHR.responseJSON.message=='Unauthenticated.')
                    window.location.href = '{{ route('login') }}';
            }
        });
    });

    // Open cancel modal
    jQuery('#cancelModal').on('show.bs.modal', function(event) {
        var oTarget = jQuery(event.relatedTarget);
        var oModal = jQuery(this);

        var sContent = '' +
            '{{ __('Are you sure you want to cancel the scheduled appointment to day') }} <strong>' + oTarget.data('date-header') + '</strong> {{ __('to') }} <strong>' + oTarget.data('time-body') + '</strong>?' +
            '<form method="POST">' +
            '<input type="hidden" name="appointment-id" id="appointment-id" value="'+oTarget.data('appointment-id')+'" />' +
            '<input type="hidden" name="date-header" id="date-header" value="'+oTarget.data('date-header')+'" />' +
            '</form>';

        oModal.find('.modal-body').html(sContent);
    });

    // Send request and close cancel modal
    jQuery('#readyCancel').on('click', function(event) {
        event.preventDefault();
        event.stopPropagation();

        var oModal = jQuery(this);

        var iAppointmentId = jQuery('#appointment-id').val();

        // Close modal
        jQuery('#cancelModal').modal('hide');

        // Ajax request time
        var iRequest = new Date().getTime();

        // Show spinner
        oModal.find('.modal-body').html(
            '<div class="my-2 py-4">\n' +
            '    <svg class="spinner" viewBox="0 0 50 50">\n' +
            '        <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>\n' +
            '    </svg>\n' +
            '</div>'
        );

        // Make request
        jQuery.ajax({
            type: 'PUT',
            url: '/appointments/' + iAppointmentId + '/cancel',
            data: {
                _token: '{{ csrf_token() }}',
                appointment_id: iAppointmentId
            },
            success: function (result) {
                // Remove appointment card wrapper
                jQuery('button[data-appointment-id=' + iAppointmentId + ']').closest('.card-deck').parent().remove()
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == 401 && jqXHR.responseJSON.message == 'Unauthenticated.')
                    window.location.href = '{{ route('admin.login') }}';

                if (jqXHR.status == 403)
                    window.location.reload();
            },
            complete: function(jqXHR, textStatus) {
                // Ajax complete time
                var iComplete = new Date().getTime();

                // Calculate time remaining to hide spinner
                var iRemainig = (iComplete-iRequest < 600) ? 600-(iComplete-iRequest) : 0;

                // Hide loading
                setTimeout(function() {
                    jQuery('.spinner-container').addClass('d-none');
                }, iRemainig);
            }
        });
    });
});

function drawCalendar(piYear, piMonth) {
    // Show spinner
    jQuery('.spinner-container').removeClass('d-none');

    // Ajax request time
    var iRequest = new Date().getTime();

    jQuery.ajax({
        type: 'GET',
        url: '/calendars/'+piYear+'/'+piMonth,
        success: function(data) {
            jQuery('.calendar-container').html(data);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            if (jqXHR.status==401 && jqXHR.responseJSON.message=='Unauthenticated.')
                window.location.href = '{{ route('login') }}';
        },
        complete: function(jqXHR, textStatus) {
            // Ajax complete time
            var iComplete = new Date().getTime();

            // Calculate time remaining to hide spinner
            var iRemainig = (iComplete-iRequest < 600) ? 600-(iComplete-iRequest) : 0;

            // Hide loading
            setTimeout(function() {
                jQuery('.spinner-container').addClass('d-none');
            }, iRemainig);

            // Box wrapper
            var oWrapper = jQuery('.book-wrapper .col-12:first-child');

            // Apply scroll to where corresponds
            if (jQuery('.current-day').length > 0)
                oWrapper.animate({
                    scrollTop: jQuery('.current-day').offset().top - oWrapper.offset().top - jQuery('.calendar-container header').height()
                });
            else
                oWrapper.animate({
                    scrollTop: 0
                });
        }
    });
}
</script>
@endsection
