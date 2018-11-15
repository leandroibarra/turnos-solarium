@extends('layouts.web')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <h2 class="text-center text-uppercase mt-0 mb-3">{{ __('Book online') }}</h2>
        </div>

        <div class="col-8 col-md-9">

            <div class="calendar-container"></div>
        </div>
        <div class="col-4 col-md-3 sidebar-container">
            <header>
                <div class="p-1">
                    <h2>&nbsp;</h2>
                </div>
            </header>
            <div class="border sidebar-content">
                <div class="p-3 text-center text-md-left">{{ __('Select a day and choose the time you wish book.') }}</div>
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
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-md-4 text-center">
                        <h5>{{ __('Morning') }}</h5>
                        @for ($iHour=0; $iHour<1; $iHour++)
                            @for ($iMinute=10; $iMinute<60; $iMinute+=10)
                            <div>1{{$iHour}}:{{$iMinute}} AM</div>
                            @endfor
                        @endfor
                    </div>
                    <div class="col-12 col-md-4 text-center">
                        <h5>{{ __('Afternoon') }}</h5>
                        @for ($iHour=2; $iHour<=4; $iHour++)
                            @for ($iMinute=20; $iMinute<60; $iMinute+=10)
                            <div>1{{$iHour}}:{{$iMinute}} PM</div>
                            @endfor
                        @endfor
                    </div>
                    <div class="col-12 col-md-4 text-center">
                        <h5>{{ __('Night') }}</h5>
                        @for ($iHour=8; $iHour<10; $iHour++)
                            @for ($iMinute=30; $iMinute<60; $iMinute+=10)
                            <div>1{{$iHour}}:{{$iMinute}} PM</div>
                            @endfor
                        @endfor
                    </div>
                </div>
            </div>
            <div class="modal-footer mx-auto">
                <button type="button" class="btn btn-grey" data-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-gold">{{ __('Confirm') }}</button>
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

    // Next or prev moth clicks
    jQuery('.calendar-container').on('click', '.prev-month, .next-month', function(event) {
        event.preventDefault();
        event.stopPropagation();

        drawCalendar(jQuery(this).data('year'), jQuery(this).data('month'));
    });

    // Sticky sidebar
    var oSidebar    = jQuery('.sidebar-content'),
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

    jQuery('#appointmentModal').on('show.bs.modal', function(event) {
        var oTarget = jQuery(event.relatedTarget);
        var oModal = jQuery(this);

        oModal.find('.modal-title').text('{{ __('Select an hour to date') }}: ' + oTarget.data('day') + ' {{ __('of') }} ' + oTarget.data('month-label'));
    });
});

function drawCalendar(piYear, piMonth) {
    // Show spinner
    jQuery('.spinner-container').removeClass('d-none');

    // Ajax request time
    var iRequest = new Date().getTime();

    jQuery.ajax({
        type: 'GET',
        url: '/calendar/'+piYear+'/'+piMonth,
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

            // Calculate time remaining to hide loading
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
