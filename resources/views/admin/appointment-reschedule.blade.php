@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row mt-3">
        <div class="col-12">
            <h2 class="text-center text-uppercase mt-0 mb-3">{{ __('Appointment rescheduling') }}</h2>
        </div>
    </div>

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
            <form method="POST" action="{{ route('appointment.update', ['id' => request()->segment(3) ]) }}" id="rescheduleForm">
                <div class="border p-3 mt-2 mt-md-0 appointment-content">
                    <div class="text-center text-md-left">{{ __('Select a day and choose the time you want to reschedule the appointment.') }}</div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="spinner-container d-none">
    <svg class="spinner" viewBox="0 0 50 50">
        <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
    </svg>
</div>

<!-- Modal -->
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" id="readyAppointment" disabled>{{ __('Ready') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
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
            headers: {
                'appointment-id': '{{ request()->segment(3) }}'
            },
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

        var sContent = '' +
            '<input type="hidden" name="_token" value="{{ csrf_token() }}" />\n' +
            '<input type="hidden" name="date" value="' + [oActive.data('year'), oActive.data('month'), oActive.data('day')].join('-') + '" />\n' +
            '<input type="hidden" name="time" value="' + oActive.data('hour').replace(/\s(a|p)m/i, '') + '" />\n' +
            '<h5 class="text-center">'+'{{ __('Selected appointment') }}</h5>\n' +
            '<div>\n' +
            '   <span class="d-block-inline-block d-md-block d-lg-inline-block mr-0 mr-md-1 font-weight-bold">{{ __('Date') }}:</span>\n' +
            '   <span class="d-block-inline-block d-md-block d-lg-inline-block">' + oActive.data('day').toString().replace(/^0+/ig, '') + ' {{ __('of') }} ' + oActive.data('month-label') + '</span>\n' +
            '</div>\n' +
            '<div class="mt-1 mt-md-0">\n' +
            '   <span class="d-block-inline-block d-md-block d-lg-inline-block mr-0 mr-md-1 font-weight-bold">{{ __('Time') }}: </span>\n' +
            '   <span class="d-block-inline-block d-md-block d-lg-inline-block">' + oActive.data('hour') + '</span>\n' +
            '</div>\n' +
            '<div class="mt-1 text-center">\n' +
            '   <button type="submit" class="btn btn-block btn-primary">{{ __('Reschedule') }}</button>\n' +
            '</div>\n';

        // Replace appointment sidebar content
        jQuery('.appointment-content').html(sContent);

        // Hide modal
        jQuery('#appointmentModal').modal('hide');

        // Apply scroll to appointment sidebar
        jQuery('html').animate({
            scrollTop: jQuery('.appointment-content').find('button[type="submit"]').offset().top
        });
    });

    // Prevent multiple clicks
    jQuery('#rescheduleForm').submit(function() {
        jQuery('button[type=submit]', this)
            .html('{{ __('Processing') }}...')
            .attr('disabled', 'disabled');

        return true;
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
@endpush