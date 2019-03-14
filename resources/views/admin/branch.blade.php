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
            <h4 class="mb-3 overflow-hidden">
                {{ __('Branches') }}

                @can (['admin.branch.create', 'admin.branch.store'])
                <a href="{{ route('branch.create') }}" class="btn btn-sm btn-primary float-right">
                    <i class="fas fa-plus"></i>
                    {{ __('Create') }}
                </a>
                @endcan
            </h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12 branches-container">
            @if (!$aEnabledBranches->isEmpty())
                @php
                $iColMd = 12;

                if ((Auth::user()->can('admin.branch.edit') && Auth::user()->can('admin.branch.update')) || Auth::user()->can('admin.branch.delete'))
                    $iColMd -= 2;
                @endphp

                <ul class="list-group">
                @foreach ($aEnabledBranches as $aBranch)
                    <li class="list-group-item p-2">
                        <div class="row">
                            <div class="col-12 col-md-{{ $iColMd }} align-self-center">
                                <div class="row">
                                    <div class="col-12 text-center text-md-left">
                                        <label class="text-muted mb-0 mr-1">{{ __('Name') }}:</label>{{ $aBranch->name }}
                                    </div>
                                    <div class="col-12 text-center text-md-left">
                                        <label class="text-muted mb-0 mr-1">{{ __('Location') }}:</label>{{ implode(', ', [$aBranch->city, $aBranch->province, $aBranch->country]) }}
                                    </div>
                                </div>
                            </div>
                            @if ((Auth::user()->can('admin.branch.edit') && Auth::user()->can('admin.branch.update')) || Auth::user()->can('admin.branch.delete'))
                            <div class="col-12 col-md-2 align-self-center text-center text-md-right mt-2 mt-md-0">
                                @can (['admin.branch.edit', 'admin.branch.update'])
                                <a href="{{ route('branch.edit', ['id' => $aBranch->id ]) }}" class="btn btn-sm btn-secondary" title="{{ __('Edit') }}" role="button">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                @endcan

                                @can ('admin.branch.delete')
                                <button class="btn btn-sm btn-danger" title="{{ __('Delete') }}"
                                    data-branch-id="{{ $aBranch->id }}"
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

@can ('admin.branch.delete')
<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">{{ __('Branch deletion') }}</h6>
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
    @can ('admin.branch.delete')
    // Open delete modal
    jQuery('#deleteModal').on('show.bs.modal', function(event) {
        var oTarget = jQuery(event.relatedTarget);
        var oModal = jQuery(this);

        var sContent = '' +
            '{{ __('Are you sure you want to delete the branch?') }}' +
            '<form method="POST">' +
            '   <input type="hidden" name="branch-id" id="branch-id" value="' + oTarget.data('branch-id') + '" />' +
            '</form>';

        oModal.find('.modal-body').html(sContent);
    });

    // Send request and close delete modal
    jQuery('#readyDelete').on('click', function(event) {
        event.preventDefault();
        event.stopPropagation();

        var iBranchId = jQuery('#branch-id').val();

        // Close modal
        jQuery('#deleteModal').modal('hide');

        // Show spinner
        jQuery('.spinner-container').removeClass('d-none');

        // Ajax request time
        var iRequest = new Date().getTime();

        // Make request
        jQuery.ajax({
            type: 'PUT',
            url: '/admin/branches/' + iBranchId + '/delete',
            data: {
                _token: '{{ csrf_token() }}',
                branch_id: iBranchId
            },
            success: function(result) {
                var sClass = 'danger';

                if (result.status == 'success') {
                    sClass = 'success';

                    // Remove element from list
                    jQuery('button[data-branch-id=' + iBranchId + ']').closest('li.list-group-item').remove();
                }

                // Add message from result process
                jQuery('<div />', {
                    'class': 'alert alert-dismissible show alert-' + sClass,
                    'role': 'alert'
                }).html(
                    result.message +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '   <span aria-hidden="true">&times;</span>' +
                    '</button>'
                ).prependTo(jQuery('.branches-container'));
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == 401 && jqXHR.responseJSON.message == 'Unauthenticated.')
                    window.location.href = '{{ route('admin.login') }}';
            },
            complete: function(jqXHR, textStatus) {
                // Ajax complete time
                var iComplete = new Date().getTime();

                // Calculate time remaining to hide spinner
                var iRemainig = (iComplete - iRequest < 600) ? 600 - (iComplete - iRequest) : 0;

                // Hide loading
                setTimeout(function() {
                    jQuery('.spinner-container').addClass('d-none');
                }, iRemainig);
            }
        });
    });
    @endcan
});
</script>
@endpush