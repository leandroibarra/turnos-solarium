@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row mt-3">
        <div class="col-12">
            <h4 class="mb-3 overflow-hidden">
                {{ __('Slides') }}

                @can (['admin.slide.create', 'admin.slide.store'])
                <a href="{{ route('slide.create') }}" class="btn btn-sm btn-primary float-right">
                    <i class="fas fa-plus"></i>
                    {{ __('Create') }}
                </a>
                @endcan
            </h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12 slides-container">
            @if (!$aEnabledSlides->isEmpty())
                @php
                $iColMd = 12;

                if (Auth::user()->can('admin.slide.sort'))
                    $iColMd -= 2;

                if ((Auth::user()->can('admin.slide.edit') && Auth::user()->can('admin.slide.update')) || Auth::user()->can('admin.slide.delete'))
                    $iColMd -= 2;
                @endphp

                <ul class="list-group">
                @foreach ($aEnabledSlides as $iKey=>$aSlide)
                    <li class="list-group-item p-2" data-index="{{ $iKey }}">
                        <div class="row">
                            @can ('admin.slide.sort')
                            <div class="col-12 col-md-2 align-self-center text-center slide-order" data-index="{{ $iKey }}" data-slide-id="{{ $aSlide->id }}">
                                <a href="javascript:void(0);" class="sort-slide {{ ($iKey == 0) ? 'disabled' : '' }}" data-sort-type="desc" title="{{ __('Sort') }}">
                                    <i class="fas fa-chevron-up"></i>
                                </a>
                                <h4 class="my-0" title="{{ __('Order') }}">#{{ $aSlide->order }}</h4>
                                <a href="javascript:void(0);" class="sort-slide {{ ($iKey == count($aEnabledSlides)-1) ? 'disabled' : '' }}" data-sort-type="asc" title="{{ __('Sort') }}">
                                    <i class="fas fa-chevron-down"></i>
                                </a>
                            </div>
                            @endcan
                            <div class="col-12 col-md-{{ $iColMd }} align-self-center text-center text-md-left">
                                <div class="row">
                                    <div class="col-12 col-md-4 text-center text-md-left mb-2 mb-md-0">
                                        <img src="{{ $aSlide->fullPath }}" alt="{{ $aSlide->image }}" title="{{ $aSlide->image }}" border="0" class="img-fluid" />
                                    </div>
                                    <div class="col-12 col-md-8 text-center text-md-left">
                                        <div>
                                            <label class="text-muted mb-0 mr-1">{{ __('Title') }}:</label>{{ $aSlide->title }}
                                        </div>
                                        <div>
                                            <label class="text-muted mb-0 mr-1">{{ __('Link') }}:</label>{{ ($aSlide->link) ? $aSlide->link : '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ((Auth::user()->can('admin.slide.edit') && Auth::user()->can('admin.slide.update')) || Auth::user()->can('admin.slide.delete'))
                            <div class="col-12 col-md-2 align-self-center text-center text-md-right mt-2 mt-md-0">
                                @can (['admin.slide.edit', 'admin.slide.update'])
                                <a href="{{ route('slide.edit', ['id' => $aSlide->id ]) }}" class="btn btn-sm btn-secondary" title="{{ __('Edit') }}" role="button">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                @endcan

                                @can ('admin.slide.delete')
                                <button class="btn btn-sm btn-danger" title="{{ __('Delete') }}"
                                    data-slide-id="{{ $aSlide->id }}"
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

@can ('admin.slide.delete')
<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">{{ __('Slide deletion') }}</h6>
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
    @can ('admin.slide.delete')
    // Open delete modal
    jQuery('#deleteModal').on('show.bs.modal', function(event) {
        var oTarget = jQuery(event.relatedTarget);
        var oModal = jQuery(this);

        var sContent = '' +
            '{{ __('Are you sure you want to delete the slide?') }}' +
            '<form method="POST">' +
            '   <input type="hidden" name="slide-id" id="slide-id" value="' + oTarget.data('slide-id') + '" />' +
            '</form>';

        oModal.find('.modal-body').html(sContent);
    });

    // Send request and close delete modal
    jQuery('#readyDelete').on('click', function(event) {
        event.preventDefault();
        event.stopPropagation();

        var iSlideId = jQuery('#slide-id').val();

        // Close modal
        jQuery('#deleteModal').modal('hide');

        // Show spinner
        jQuery('.spinner-container').removeClass('d-none');

        // Ajax request time
        var iRequest = new Date().getTime();

        // Make request
        jQuery.ajax({
            type: 'PUT',
            url: '/admin/slides/' + iSlideId + '/delete',
            data: {
                _token: '{{ csrf_token() }}',
                slide_id: iSlideId
            },
            success: function(result) {
                if (result.status == 'success') {
                    // Remove element from list
                    jQuery('button[data-slide-id=' + iSlideId + ']').closest('li.list-group-item').remove();
                }

                // Show message from result process
                showNotify(result.message, result.status);

                // Update index and order for each slide
                updateIndexAndOrder();
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

    @can ('admin.slide.sort')
    jQuery('.slides-container').on('click', '.sort-slide:not(.disabled)', function(event) {
        event.preventDefault();
        event.stopPropagation();

        var aData = new Array();

        // Obtain parameter from clicked element
        var iSlideId = jQuery(this).closest('.slide-order').data('slide-id');
        var iIndex = jQuery(this).closest('.slide-order').data('index');
        var sSortType = jQuery(this).data('sort-type');

        if (sSortType != 'asc' && sSortType != 'desc') {
            console.log('Sort type is not valid');
        } else if (sSortType == 'desc' && iIndex == 0) {
            console.log('Sort asc on this element not allowed');
        } else if (sSortType == 'asc' && iIndex == parseInt({{ count($aEnabledSlides) }})-1) {
            console.log('Sort desc on this element not allowed');
        } else {
            // Calculate this order and other index
            var iOrderThis = (sSortType == 'asc') ? iIndex + 2 : iIndex;
            var iIndexOther = (sSortType == 'asc') ? iIndex + 1 : iIndex - 1;

            // Build data to send
            aData.push(
                {
                    id: iSlideId,
                    order: iOrderThis
                },
                {
                    id: jQuery('.list-group-item[data-index=' + iIndexOther + ']').find('.slide-order').data('slide-id'),
                    order: iIndex + 1
                }
            );

            // Show spinner
            jQuery('.spinner-container').removeClass('d-none');

            // Ajax request time
            var iRequest = new Date().getTime();

            // Make request
            jQuery.ajax({
                type: 'PUT',
                url: '/admin/slides/sort',
                data: {
                    _token: '{{ csrf_token() }}',
                    slides: aData
                },
                success: function(result) {
                    if (result.status == 'success') {
                        // Build and sort array of index
                        var aIndex = new Array(iIndex, iIndexOther).sort();

                        // Obtain contents to switch
                        var sContentToMajor = jQuery('.list-group-item:eq(' + aIndex[0] + ')').html();
                        var sContentToMinor = jQuery('.list-group-item:eq(' + aIndex[1] + ')').html();

                        // Switch content between list elements by index
                        jQuery('.list-group-item:eq(' + aIndex[0] + ')').html(sContentToMinor);
                        jQuery('.list-group-item:eq(' + aIndex[1] + ')').html(sContentToMajor);

                        // Update index and order for each slide
                        updateIndexAndOrder();
                    }
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
        }
    });
    @endcan

    @if (Auth::user()->can('admin.slide.sort') || Auth::user()->can('admin.slide.delete'))
    function updateIndexAndOrder() {
        jQuery('.slides-container .list-group-item').each(function(iIndex, oElement) {
            var oSlideOrder = jQuery(oElement).find('.slide-order');

            // Update list index attribute
            jQuery(oElement).attr('data-index', iIndex);

            // Update slide order index attribute
            jQuery(oSlideOrder).attr('data-index', iIndex);

            // Update order text
            jQuery(oSlideOrder).find('h4').html('#' + (iIndex + 1));

            // Enable all sort anchors temporally
            jQuery(oSlideOrder).find('a').removeClass('disabled');

            if (iIndex == 0) {
                // Disable asc sort anchor to first position
                jQuery(oSlideOrder).find('a.sort-slide:first-child').addClass('disabled');
            } else if (iIndex == parseInt({{ count($aEnabledSlides) }})-1) {
                // Disable desc sort anchor to last position
                jQuery(oSlideOrder).find('a.sort-slide:last-child').addClass('disabled');
            }
        });
    }
    @endif
});
</script>
@endpush