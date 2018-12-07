<header>
    <div class="row mx-auto p-1">
        <div class="col-2 text-center">
            @if ($oLimitPrevNav->format('Y-m-d')<$oRequestDateTime->format('Y-m-d') && $oLimitPrevNav->format('m')+1!=$oToday->format('m'))
                <h2>
                    <a data-year="{{ $oLimitPrevNav->format('Y') }}" data-month="{{  $oLimitPrevNav->format('m') }}" class="prev-month">
                        <i class="arrow left"></i>
                    </a>
                </h2>
            @endif
        </div>
        <div class="col-8">
            <h2 class="text-center">{{ $oRequestDateTime->format('F Y') }}</h2>
        </div>
        <div class="col-2 text-center">
            @if ($oLimitNextNav->format('Y-m-d')>$oToday->format('Y-m-d') && $oLimitNextNav->format('Y-m-d')<$oLimitDate->format('Y-m-d'))
                <h2>
                    <a data-year="{{ $oLimitNextNav->format('Y') }}" data-month="{{ $oLimitNextNav->format('m') }}" class="next-month">
                        <i class="arrow right"></i>
                    </a>
                </h2>
            @endif
        </div>
    </div>
    <div class="row mx-auto d-none d-sm-flex p-1 bg-dark text-white">
        @for ($iWeekDay=0; $iWeekDay<7; $iWeekDay++)
            <h5 class="col-sm p-1 mb-0 text-center">{{ $oHeaderDateTime->format('l') }}</h5>

            @php
            $oHeaderDateTime->modify('+1 day');
            @endphp
        @endfor
    </div>
</header>

<div class="row mx-auto border border-right-0 border-bottom-0">
    @php
    $iDays = 1;

    do {
    @endphp

    @for ($iWeekDay=0; $iWeekDay<7; $iWeekDay++)
        @if (
            $oToday->format('Y-m-d')>$oDateTime->format('Y-m-d') ||
            $oDateTime->format('Y-m-d')>$oLimitDate->format('Y-m-d') ||
            $oDateTime->format('Y-m')!=$oRequestDateTime->format('Y-m')
        )
            @php
            $sClasses = 'd-sm-inline-block bg-light text-muted';
            @endphp
        @else
            @if (in_array($iWeekDay, $aNonWorkingDays))
                @php
                $sClasses = 'weekend-day text-muted';
                @endphp
            @elseif (validateDateInExceptions($oDateTime->format('Y-m-d'), $aExceptions))
                @php
                $sClasses = 'exception-day bg-light text-muted';
                @endphp
            @else
                @php
                $sClasses = 'bookable-day';
                @endphp
            @endif
        @endif

        @php
        $sClasses .= ($oToday->format('Y-m-d')>$oDateTime->format('Y-m-d') && ($oDateTime->format('Y-m')!=$oToday->format('Y-m') || $oRequestDateTime->format('Y-m')==$oToday->format('Y-m'))) ? ' d-none d-md-block' : (($oToday->format('Y-m-d') == $oDateTime->format('Y-m-d')) ? ' current-day' : '');
        @endphp

        <div class="day col-sm p-2 border border-left-0 border-top-0 text-truncate {{ $sClasses }}"
             @if (strpos($sClasses, 'bookable-day') !== false)
                data-year="{{ $oDateTime->format('Y') }}"
                data-month="{{ $oDateTime->format('m') }}"
                data-day="{{ $oDateTime->format('d') }}"
                data-month-label="{{ $oDateTime->format('F') }}"
                data-target="#appointmentModal"
                data-toggle="modal"
            @endif
        >
            <h5 class="row align-items-center">
                <span class="date col-1">{{ $oDateTime->format('j') }}</span>
                <small class="col d-sm-none text-center text-muted">{{ $oDateTime->format('l') }}</small>
                <span class="col-1"></span>
            </h5>
        </div>

        @if ($iWeekDay == 6)
            <div class="w-100"></div>
        @endif

        @php
        $oDateTime->modify('+1 day');

        $iDays++;
        @endphp
    @endfor

    @php
    } while ($iDays <= 42);
    @endphp

</div>