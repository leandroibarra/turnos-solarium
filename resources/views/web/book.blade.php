@extends('layouts.web')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="text-center text-uppercase mt-0 mb-3">{{ __('Book online') }}</h2>

            @include('flash::message')
        </div>
    </div>

    <div class="row">
        <div class="col-8 col-md-9">
            <div class="calendar-container"></div>
        </div>

        <div class="col-4 col-md-3 appointment-container">
            <header>
                <div class="p-1">
                    <h2>&nbsp;</h2>
                </div>
            </header>
            <div class="border p-3 appointment-content">
                <div class="text-center text-md-left">{{ __('Select a day and choose the time you wish book.') }}</div>
            </div>
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
                <button type="button" class="btn btn-grey" data-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-gold" id="readyAppointment" disabled>{{ __('Ready') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
<script type="text/javascript">
jQuery(document).ready(function() {
    // Initial calendar
    drawCalendar({{date('Y')}}, {{date('m')}});

    // Next or prev month clicks
    jQuery('.calendar-container').on('click', '.prev-month, .next-month', function(event) {
        event.preventDefault();
        event.stopPropagation();

        drawCalendar(jQuery(this).data('year'), jQuery(this).data('month'));
    });

    // Sticky appointment sidebar
    var oSidebar    = jQuery('.appointment-content'),
        oWindow     = jQuery(window),
        iOffset     = oSidebar.offset(),
        iPaddingTop = 15;

    oWindow.scroll(function() {
        if (oWindow.scrollTop() > iOffset.top)
            oSidebar.stop().animate({
                marginTop: oWindow.scrollTop() - iOffset.top + iPaddingTop
            });
        else
            oSidebar.stop().animate({
                marginTop: 0
            });
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
                    '<h5 class="text-center">'+'{{ __('Selected appointment') }}</h5>\n' +
                    '<div>\n' +
                    '   <span class="d-block d-md-inline-block mr-0 mr-md-1 font-weight-bold">{{ __('Date') }}:</span>\n' +
                    '   <span class="d-block d-md-inline-block">' + oActive.data('day').toString().replace(/^0+/ig, '') + ' {{ __('of') }} ' + oActive.data('month-label') + '</span>\n' +
                    '</div>\n' +
                    '<div class="mt-1 mt-md-0">\n' +
                    '   <span class="d-block d-md-inline-block mr-0 mr-md-1 font-weight-bold">{{ __('Time') }}: </span>\n' +
                    '   <span class="d-block d-md-inline-block">' + oActive.data('hour') + '</span>\n' +
                    '</div>\n' +
                    '<div class="mt-1 text-center">\n' +
                    '   <a href="{{ route('book.create') }}" role="button" class="btn btn-block btn-gold">{{ __('Confirm') }}</a>\n' +
                    '</div>\n';

                jQuery('.appointment-content').html(sContent);

                jQuery('#appointmentModal').modal('hide');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status==401 && jqXHR.responseJSON.message=='Unauthenticated.')
                    window.location.href = '{{ route('login') }}';
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
        }
    });
}
</script>
@endsection
