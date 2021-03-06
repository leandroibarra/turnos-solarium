@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row mt-3">
        <div class="col-12">
            <h4 class="mb-3 overflow-hidden">
                {{ __('Exceptions') }}

                @can (['admin.exception.create', 'admin.exception.store'])
                <a href="{{ route('exception.create') }}" class="btn btn-sm btn-primary float-right">
                    <i class="fas fa-plus"></i>
                    {{ __('Create') }}
                </a>
                @endcan
            </h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12 exceptions-container">
            @if (!$aEnabledExceptions->isEmpty())
                @php
                $iColMd = 12;

                if ((Auth::user()->can('admin.exception.edit') && Auth::user()->can('admin.exception.update')) || Auth::user()->can('admin.exception.delete'))
                    $iColMd -= 2;
                @endphp
                <ul class="list-group">
                @foreach ($aEnabledExceptions as $iKey=>$aException)
                    <li class="list-group-item p-2">
                        <div class="row">
                            <div class="col-12 col-md-{{ $iColMd }} align-self-center text-center text-md-left">
                                <div class="row">
                                    <div class="col-12 col-md-5 text-center text-md-left order-1 order-md-1">
                                        <label class="text-muted mb-0 mr-1">{{ __('From') }}:</label>{{ Date::createFromFormat('Y-m-d H:i:s', $aException->datetime_from)->format(__('m/d/y H:i \\h\\s')) }}
                                    </div>
                                    <div class="col-12 col-md-7 text-center text-md-left order-3 order-md-2">
                                        <label class="text-muted mb-0 mr-1">{{ __('Type') }}:</label>{{ __(ucfirst($aException->type)) }}
                                    </div>
                                    <div class="col-12 col-md-5 text-center text-md-left order-2 order-md-3">
                                        <label class="text-muted mb-0 mr-1">{{ __('To') }}:</label>{{ Date::createFromFormat('Y-m-d H:i:s', $aException->datetime_to)->format(__('m/d/y H:i \\h\\s')) }}
                                    </div>
                                    <div class="col-12 col-md-7 text-center text-md-left order-4 order-md-4">
                                        <label class="text-muted mb-0 mr-1">{{ __('Obs.') }}:</label><span class="observations">{{ (!is_null($aException->observations)) ? $aException->observations : '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            @if ((Auth::user()->can('admin.exception.edit') && Auth::user()->can('admin.exception.update')) || Auth::user()->can('admin.exception.delete'))
                            <div class="col-12 col-md-2 align-self-center text-center text-md-right mt-2 mt-md-0">
                                @can (['admin.exception.edit', 'admin.exception.update'])
                                <a href="{{ route('exception.edit', ['id' => $aException->id ]) }}" class="btn btn-sm btn-secondary" title="{{ __('Edit') }}" role="button">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                @endcan

                                @can ('admin.exception.delete')
                                <button class="btn btn-sm btn-danger" title="{{ __('Delete') }}"
                                    data-exception-id="{{ $aException->id }}"
                                    data-target="#deleteModal"
                                    data-toggle="modal"
                                >
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                @endcan
                            </div>
                            @endif
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

@can ('admin.exception.delete')
<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">{{ __('Exception deletion') }}</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center"></div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('No') }}</button>
                <button type="button" class="btn btn-primary" id="readyDelete">{{ __('Yes') }}</button>
            </div>
        </div>
    </div>
</div>
@endcan
@endsection

@push('page-scripts')
<script type="text/javascript">
jQuery(document).ready(function() {
    @can ('admin.exception.delete')
    // Open delete modal
    jQuery('#deleteModal').on('show.bs.modal', function(event) {
        var oTarget = jQuery(event.relatedTarget);
        var oModal = jQuery(this);

        var sContent = '' +
            '{{ __('Are you sure you want to delete the exception?') }}' +
            '<form method="POST">' +
                '<input type="hidden" name="exception-id" id="exception-id" value="'+oTarget.data('exception-id')+'" />' +
            '</form>';

        oModal.find('.modal-body').html(sContent);
    });

    // Send request and close delete modal
    jQuery('#readyDelete').on('click', function(event) {
        event.preventDefault();
        event.stopPropagation();

        var iExceptionId = jQuery('#exception-id').val();

        // Close modal
        jQuery('#deleteModal').modal('hide');

        // Show spinner
        jQuery('.spinner-container').removeClass('d-none');

        // Ajax request time
        var iRequest = new Date().getTime();

        // Make request
        jQuery.ajax({
            type: 'PUT',
            url: '/admin/exceptions/' + iExceptionId + '/delete',
            data: {
                _token: '{{ csrf_token() }}',
                exception_id: iExceptionId
            },
            success: function (result) {
                if (result.status == 'success') {
                    // Remove element from list
                    jQuery('button[data-exception-id=' + iExceptionId + ']').closest('li.list-group-item').remove();
                }

                // Show message from result process
                showNotify(result.message, result.status);
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
    @endcan

    var iShowChar = 25;

    jQuery('.observations').each(function() {
        var oContent = jQuery(this).html();

        if (oContent.length > iShowChar) {
            var showContent = oContent.substr(0, iShowChar);
            var hiddenContent = oContent.substr(iShowChar, oContent.length - iShowChar);

            var sHtml = showContent + '<span class="more-ellipses">...</span><span class="hidden-content"><span>' + hiddenContent + '</span>&nbsp;<span class="more-link">{{ __('more') }}</span></span>';

            jQuery(this).html(sHtml);
        }
    });

    jQuery('.more-link').click(function() {
        if (jQuery(this).hasClass('less')) {
            jQuery(this).removeClass('less');

            jQuery(this).html('{{ __('more') }}');
        } else {
            jQuery(this).addClass('less');

            jQuery(this).html('{{ __('less') }}');
        }

        jQuery(this).parent().prev().toggle();
        jQuery(this).prev().toggle();

        return false;
    });
});
</script>
@endpush