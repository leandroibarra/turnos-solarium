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
            <h4 class="mb-3">
                {{ __('Appointments') }}
                <!--a href="javascript:void(0);" class="btn btn-sm btn-primary float-right">
                    <i class="fas fa-plus"></i>
                    {{ __('Create') }}
                </a-->
            </h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12 appointments-container">
            @if (!$aGrantedAppointments->isEmpty())
                <ul class="list-group">
                @php
                $sCurrentDate = '';
                @endphp

                @foreach ($aGrantedAppointments as $iKey=>$aAppointment)
                    @php
                    if ($sCurrentDate != $aAppointment->date) {
                        $sCurrentDate = $aAppointment->date;

                        $sDateHeader = Date::createFromFormat('Y-m-d', $aAppointment->date)->format(__('l j \\of F'))
                    @endphp
                    <li class="list-group-item list-group-item-secondary font-weight-bold text-center p-2 {{ ($iKey>0) ? 'mt-3' : '' }}">{{ $sDateHeader }}</li>
                    @php
                    }

                    $sTimeBody = Date::createFromFormat('H:i:s', $aAppointment->time)->format('H:i a');
                    @endphp
                    <li class="list-group-item p-2">
                        <div class="row">
                            <div class="col-12 col-md-8 align-self-center text-center text-md-left">
                                <i class="far fa-clock text-muted mr-2"></i>{{ $sTimeBody }}
                                <i class="far fa-user text-muted ml-3 mr-2"></i>{{ $aAppointment->name }}
                                <br />
                                <i class="far fa-envelope text-muted mr-2"></i>{{ $aAppointment->user['email'] }}
                                <i class="fas fa-mobile-alt text-muted ml-3 mr-2"></i>{{ $aAppointment->phone }}
                            </div>
                            <div class="col-12 col-md-4 align-self-center text-center text-md-right mt-2 mt-md-0">
                                @can(['admin.appointment.reschedule', 'admin.appointment.update'])
                                <a href="{{ route('appointment.reschedule', ['id' => $aAppointment->id ]) }}" class="btn btn-sm btn-secondary" title="{{ __('Reschedule') }}" role="button">
                                    <i class="far fa-calendar-alt"></i>
                                </a>
                                @endcan

                                @can('admin.appointment.cancel')
                                <button class="btn btn-sm btn-danger" title="{{ __('Cancel') }}"
                                    data-appointment-id="{{ $aAppointment->id }}"
                                    data-date-header="{{ $sDateHeader }}"
                                    data-time-body="{{ $sTimeBody }}"
                                    data-target="#cancelModal"
                                    data-toggle="modal"
                                >
                                    <i class="fas fa-ban"></i>
                                </button>
                                @endcan
                            </div>
                        </div>
                    </li>
                @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>

<div class="spinner-container d-none">
    <svg class="spinner" viewBox="0 0 50 50">
        <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
    </svg>
</div>

<!-- Modal -->
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('No') }}</button>
                <button type="button" class="btn btn-primary" id="readyCancel">{{ __('Yes') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
<script type="text/javascript">
jQuery(document).ready(function() {
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

        var iAppointmentId = jQuery('#appointment-id').val();
        var sDateHeader = jQuery('#date-header').val();

        // Close modal
        jQuery('#cancelModal').modal('hide');

        // Show spinner
        jQuery('.spinner-container').removeClass('d-none');

        // Ajax request time
        var iRequest = new Date().getTime();

        // Make request
        jQuery.ajax({
            type: 'PUT',
            url: '/admin/appointments/' + iAppointmentId + '/cancel',
            data: {
                _token: '{{ csrf_token() }}',
                appointment_id: iAppointmentId
            },
            success: function (result) {
                var sClass = 'danger';

                if (result.status == 'success') {
                    sClass = 'success';

                    // Remove element from list
                    jQuery('button[data-appointment-id=' + iAppointmentId + ']').closest('li.list-group-item:not(.list-group-item-secondary)').remove();

                    // Remove header if correspond
                    if (jQuery('button[data-date-header="' + sDateHeader + '"]').length == 0)
                        jQuery('li:contains(' + sDateHeader + ')').remove();
                }

                // Add message from result process
                jQuery('<div />', {
                    'class': 'alert alert-dismissible show alert-' + sClass,
                    'role': 'alert'
                }).html(
                    result.message +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                    '</button>'
                ).prependTo(jQuery('.appointments-container'));
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == 401 && jqXHR.responseJSON.message == 'Unauthenticated.')
                    window.location.href = '{{ route('admin.login') }}';
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
</script>
@endsection