@extends('layouts.web')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="text-center text-uppercase mt-0 mb-3">{{ __('Branches') }}</h2>
        </div>
    </div>

    <div class="row">
        @foreach (array_chunk($aEnabledBranches->toArray(), 4, true) as $aBranches)
            @php
            $sClassFirst = $sClassLast = '';
            $iKey = 0;

            switch (count($aBranches)) {
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
            @foreach ($aBranches as $iIndex=>$aBranch)
                @php
                $iKey++;
                @endphp
            <div class="col-12 col-lg-3 col-md-6 {{ ($iKey == 1) ? $sClassFirst : ((count($aBranches) == $iKey) ? $sClassLast : '') }} mb-3 mb-lg-0 h-100">
                <div class="card text-center">
                    <div class="card-body">
                        <form method="POST" action="{{ route('branch.set') }}" class="branchForm">
                            @csrf

                            <h5 class="card-title">{{ $aBranch['name'] }}</h5>

                            @if ($aBranch['city'] || $aBranch['country'])
                            <p class="my-0 text-nowrap">{{ implode(', ', [$aBranch['city'], $aBranch['country']]) }}</p>
                            @endif

                            <input type="hidden" name="branch_id" value="{{ $aBranch['id'] }}" />

                            <button type="submit" class="btn btn-gold mt-3" data-branch-id="{{ $aBranch['id'] }}">{{ __('Select') }}</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        @endforeach
    </div>
</div>
@endsection

@section('page-scripts')
<script type="text/javascript">
jQuery(document).ready(function() {
    // Prevent multiple clicks
    jQuery('.branchForm').submit(function() {
        jQuery('button[type=submit]', this)
            .html('{{ __('Processing') }}...')
            .attr('disabled', 'disabled');

        return true;
    });
});
</script>
@endsection