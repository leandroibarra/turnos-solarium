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
                {{ __('Prices') }}

                @can (['admin.price.create', 'admin.price.store'])
                <a href="{{ route('price.create') }}" class="btn btn-sm btn-primary float-right">
                    <i class="fas fa-plus"></i>
                    {{ __('Create') }}
                </a>
                @endcan
            </h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12 prices-container">
            @if (!$aEnabledPrices->isEmpty())
                <ul class="list-group">
                @foreach ($aEnabledPrices as $iKey=>$aPrice)
                    <li class="list-group-item p-2" data-index="{{ $iKey }}">
                        <div class="row">
                            <div class="col-12 col-md-10 align-self-top text-center text-md-left">
                                <div class="row">
                                    @can ('admin.price.sort')
                                    <div class="col-2 text-center price-order" data-index="{{ $iKey }}" data-price-id="{{ $aPrice->id }}">
                                        <a href="javascript:void(0);" class="sort-price {{ ($iKey == 0) ? 'disabled' : '' }}" data-sort-type="desc" title="{{ __('Sort') }}">
                                            <i class="fas fa-chevron-up"></i>
                                        </a>
                                        <h4 class="my-0">{{ $aPrice->order }}</h4>
                                        <a href="javascript:void(0);" class="sort-price {{ ($iKey == count($aEnabledPrices)-1) ? 'disabled' : '' }}" data-sort-type="asc" title="{{ __('Sort') }}">
                                            <i class="fas fa-chevron-down"></i>
                                        </a>
                                    </div>
                                    @endcan
                                    <div class="col">
                                        <div class="row">
                                            <div class="col-12 col-md-5 text-center text-md-left">
                                                <label class="text-muted mb-0 mr-1">{{ __('Title') }}:</label>{{ $aPrice->title }}
                                            </div>
                                            <div class="col-12 col-md-7 text-center text-md-left">
                                                <label class="text-muted mb-0 mr-1">{{ __('Price') }}:</label>&dollar; {{ $aPrice->price }}
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 text-center text-md-left">
                                                <label class="text-muted mb-0 mr-1">{{ __('Desc.') }}:</label><span class="description">{{ $aPrice->description }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-2 align-self-top text-center text-md-right mt-2 mt-md-0">
                                @can (['admin.price.edit', 'admin.price.update'])
                                <a href="{{ route('price.edit', ['id' => $aPrice->id ]) }}" class="btn btn-sm btn-secondary" title="{{ __('Edit') }}" role="button">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                @endcan

                                @can ('admin.price.delete')
                                <button class="btn btn-sm btn-danger" title="{{ __('Delete') }}"
                                    data-price-id="{{ $aPrice->id }}"
                                    data-target="#deleteModal"
                                    data-toggle="modal"
                                >
                                    <i class="fas fa-trash-alt"></i>
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

@can ('admin.price.delete')
<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">{{ __('Price deletion') }}</h6>
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

@section('page-scripts')
<script type="text/javascript">
jQuery(document).ready(function() {
    @can ('admin.price.delete')
    // Open delete modal
    jQuery('#deleteModal').on('show.bs.modal', function(event) {
        var oTarget = jQuery(event.relatedTarget);
        var oModal = jQuery(this);

        var sContent = '' +
            '{{ __('Are you sure you want to delete the price?') }}' +
            '<form method="POST">' +
            '   <input type="hidden" name="price-id" id="price-id" value="' + oTarget.data('price-id') + '" />' +
            '</form>';

        oModal.find('.modal-body').html(sContent);
    });

    // Send request and close delete modal
    jQuery('#readyDelete').on('click', function(event) {
        event.preventDefault();
        event.stopPropagation();

        var iPriceId = jQuery('#price-id').val();

        // Close modal
        jQuery('#deleteModal').modal('hide');

        // Show spinner
        jQuery('.spinner-container').removeClass('d-none');

        // Ajax request time
        var iRequest = new Date().getTime();

        // Make request
        jQuery.ajax({
            type: 'PUT',
            url: '/admin/prices/' + iPriceId + '/delete',
            data: {
                _token: '{{ csrf_token() }}',
                price_id: iPriceId
            },
            success: function(result) {
                var sClass = 'danger';

                if (result.status == 'success') {
                    sClass = 'success';

                    // Remove element from list
                    jQuery('button[data-price-id=' + iPriceId + ']').closest('li.list-group-item').remove();
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
                ).prependTo(jQuery('.prices-container'));

                // Update index and order for each price
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

    @can ('admin.price.sort')
    jQuery('.prices-container').on('click', '.sort-price:not(.disabled)', function(event) {
        event.preventDefault();
        event.stopPropagation();

        var aData = new Array();

        // Obtain parameter from clicked element
        var iPriceId = jQuery(this).closest('.price-order').data('price-id');
        var iIndex = jQuery(this).closest('.price-order').data('index');
        var sSortType = jQuery(this).data('sort-type');

        if (sSortType != 'asc' && sSortType != 'desc') {
            console.log('Sort type is not valid');
        } else if (sSortType == 'desc' && iIndex == 0) {
            console.log('Sort asc on this element not allowed');
        } else if (sSortType == 'asc' && iIndex == parseInt({{ count($aEnabledPrices) }})-1) {
            console.log('Sort desc on this element not allowed');
        } else {
            // Calculate this order and other index
            var iOrderThis = (sSortType == 'asc') ? iIndex + 2 : iIndex;
            var iIndexOther = (sSortType == 'asc') ? iIndex + 1 : iIndex - 1;

            // Build data to send
            aData.push(
                {
                    id: iPriceId,
                    order: iOrderThis
                },
                {
                    id: jQuery('.list-group-item[data-index=' + iIndexOther + ']').find('.price-order').data('price-id'),
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
                url: '/admin/prices/sort',
                data: {
                    _token: '{{ csrf_token() }}',
                    prices: aData
                },
                success: function(result) {
                    var sClass = 'danger';

                    if (result.status == 'success') {
                        sClass = 'success';

                        // Build and sort array of index
                        var aIndex = new Array(iIndex, iIndexOther).sort();

                        // Obtain contents to switch
                        var sContentToMajor = jQuery('.list-group-item:eq(' + aIndex[0] + ')').html();
                        var sContentToMinor = jQuery('.list-group-item:eq(' + aIndex[1] + ')').html();

                        // Switch content between list elements by index
                        jQuery('.list-group-item:eq(' + aIndex[0] + ')').html(sContentToMinor);
                        jQuery('.list-group-item:eq(' + aIndex[1] + ')').html(sContentToMajor);

                        // Update index and order for each price
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

    @if (Auth::user()->can('admin.price.sort') || Auth::user()->can('admin.price.delete'))
    function updateIndexAndOrder() {
        jQuery('.prices-container .list-group-item').each(function(iIndex, oElement) {
            var oPriceOrder = jQuery(oElement).find('.price-order');

            // Update list index attribute
            jQuery(oElement).attr('data-index', iIndex);

            // Update price order index attribute
            jQuery(oPriceOrder).attr('data-index', iIndex);

            // Update order text
            jQuery(oPriceOrder).find('h4').html(iIndex + 1);

            // Enable all sort anchors temporally
            jQuery(oPriceOrder).find('a').removeClass('disabled');

            if (iIndex == 0) {
                // Disable asc sort anchor to first position
                jQuery(oPriceOrder).find('a.sort-price:first-child').addClass('disabled');
            } else if (iIndex == parseInt({{ count($aEnabledPrices) }})-1) {
                // Disable desc sort anchor to last position
                jQuery(oPriceOrder).find('a.sort-price:last-child').addClass('disabled');
            }
        });
    }
    @endif
});
</script>
@endsection