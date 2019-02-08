<header>
    <div class="row mx-auto p-1 calendar-navigation">
        <div class="col-2 text-center">
            @if ($oLimitPrevNav->format('Y-m-d')<$oRequestDate->format('Y-m-d') && $oLimitPrevNav->format('m')+1!=$oToday->format('m'))
            <h2>
                <a data-year="{{ $oLimitPrevNav->format('Y') }}" data-month="{{  $oLimitPrevNav->format('m') }}" class="prev-month">
                    <i class="arrow left"></i>
                </a>
            </h2>
            @endif
        </div>
        <div class="col-8">
            <h2 class="text-center">{{ $oRequestDate->format('F Y') }}</h2>
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
    <div class="row mx-auto p-1 bg-dark text-white">
        @for ($iWeekDay=0; $iWeekDay<7; $iWeekDay++)
            <h5 class="col p-1 mb-0 text-center">
                <span class="d-none d-lg-block">{{ $oHeaderDateTime->format('l') }}</span>
                <span class="d-block d-lg-none">{{ $oHeaderDateTime->format('D') }}</span>
            </h5>

            @php
            $oHeaderDateTime->modify('+1 day');
            @endphp
        @endfor
    </div>
</header>

<div class="row mx-auto border border-right-0 border-bottom-0 calendar-content">
    @php
    $iDays = 1;

    do {
    @endphp

    @for ($iWeekDay=0; $iWeekDay<7; $iWeekDay++)
        @if (
            $oToday->format('Y-m-d')>$oDate->format('Y-m-d') ||
            $oDate->format('Y-m-d')>$oLimitDate->format('Y-m-d') ||
            $oDate->format('Y-m')!=$oRequestDate->format('Y-m')
        )
            @php
            $sClasses = 'd-sm-inline-block bg-light text-muted';
            @endphp
        @else
            @if (in_array($iWeekDay, $aNonWorkingDays))
                @php
                $sClasses = 'weekend-day text-muted';
                @endphp
            @elseif (validateDateInExceptions($oDate->format('Y-m-d'), $aExceptions))
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
        $sClasses .= ($oToday->format('Y-m-d') == $oDate->format('Y-m-d')) ? ' current-day' : '';
        @endphp

        <div class="day col p-2 border border-left-0 border-top-0 text-truncate {{ $sClasses }}"
             @if (strpos($sClasses, 'bookable-day') !== false)
                data-year="{{ $oDate->format('Y') }}"
                data-month="{{ $oDate->format('m') }}"
                data-day="{{ $oDate->format('d') }}"
                data-month-label="{{ $oDate->format('F') }}"
                data-target="#appointmentModal"
                data-toggle="modal"
            @endif
        >
            <h5 class="row align-items-center">
                <span class="date col-1 {{ (($oDate->format('j') < 10) ? 'text-truncate' : '') }}">{{ $oDate->format('j') }}</span>
                <span class="col-1"></span>
            </h5>
        </div>

        @if ($iWeekDay == 6)
            <div class="w-100"></div>
        @endif

        @php
        $oDate->modify('+1 day');

        $iDays++;
        @endphp
    @endfor

    @php
    } while ($iDays <= 42);
    @endphp

</div>