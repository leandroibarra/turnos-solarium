<div class="row sticky-wrapper">
    <div class="col-12 col-md-4 text-center sticky-content">
        <h5 class="mb-2 sticky-item">{{ __('Morning') }}</h5>

        @if ((bool) $aMorning)
            @php
            $iCount = 0;
            @endphp

            @foreach ($aMorning as $iHour)
                @php
                $oRequestDate->hour($iHour);
                @endphp

                @for ($iMinute=0; $iMinute<$iAppointmentsPerHour; $iMinute++)
                    @php
                    $oRequestDate->minute($iMinute * $iAppointmentMinutes);
                    @endphp

                    @if (
                        !validateGrantedAppointments($oRequestDate->format('H:i'), $aGrantedAppointments, $iAppointmentsByTime) &&
                        !validateDateTimeInException($oRequestDate->format('Y-m-d H:i:s'), $aExceptions) &&
                        (
                            !(bool) $aAppointmentToExclude || $aAppointmentToExclude['date']!=$oRequestDate->format('Y-m-d') ||
                            ((bool) $aAppointmentToExclude && $aAppointmentToExclude['date']==$oRequestDate->format('Y-m-d') && $aAppointmentToExclude['time']!=$oRequestDate->format('H:i:s'))
                        ) &&
                        (
                            ($oToday->format('Y-m-d') < $oRequestDate->format('Y-m-d')) ||
                            ($oToday->format('Y-m-d')==$oRequestDate->format('Y-m-d') && $oToday->format('H:i')<$oRequestDate->format('H:i'))
                        ) &&
                        $oRequestDate->format('H:i:s') <= $sUntil &&
                        $oRequestDate->format('H:i:s') >= $sFrom
                    )
                        @php
                        $iCount++;
                        @endphp

                        <div class="mb-2 p-1 appointment-hour"
                             data-year="{{ $oRequestDate->format('Y') }}"
                             data-month="{{ $oRequestDate->format('m') }}"
                             data-month-label="{{ $oRequestDate->format('F') }}"
                             data-day="{{ $oRequestDate->format('d') }}"
                             data-hour="{{ $oRequestDate->format('H:i a') }}"
                         >{{ $oRequestDate->format('H:i a') }}</div>
                    @endif
                @endfor
            @endforeach

            @if ($iCount == 0)
                <div class="text-center">{{ __('There are no more available hours') }}</div>
            @endif
        @else
            <div class="text-center">{{ __('There are no more available hours') }}</div>
        @endif
    </div>

    <div class="col-12 col-md-4 text-center mt-2 mt-md-0 sticky-content">
        <h5 class="mb-2 sticky-item">{{ __('Afternoon') }}</h5>

        @if ((bool) $aAfternoon)
            @php
            $iCount = 0;
            @endphp

            @foreach ($aAfternoon as $iHour)
                @php
                $oRequestDate->hour($iHour);
                @endphp

                @for ($iMinute=0; $iMinute<$iAppointmentsPerHour; $iMinute++)
                    @php
                    $oRequestDate->minute($iMinute * $iAppointmentMinutes);
                    @endphp

                    @if (
                        !validateGrantedAppointments($oRequestDate->format('H:i'), $aGrantedAppointments, $iAppointmentsByTime) &&
                        !validateDateTimeInException($oRequestDate->format('Y-m-d H:i:s'), $aExceptions) &&
                        (
                            !(bool) $aAppointmentToExclude || $aAppointmentToExclude['date']!=$oRequestDate->format('Y-m-d') ||
                            ((bool) $aAppointmentToExclude && $aAppointmentToExclude['date']==$oRequestDate->format('Y-m-d') && $aAppointmentToExclude['time']!=$oRequestDate->format('H:i:s'))
                        ) &&
                        (
                            ($oToday->format('Y-m-d') < $oRequestDate->format('Y-m-d')) ||
                            ($oToday->format('Y-m-d')==$oRequestDate->format('Y-m-d') && $oToday->format('H:i')<$oRequestDate->format('H:i'))
                        ) &&
                        $oRequestDate->format('H:i:s') <= $sUntil &&
                        $oRequestDate->format('H:i:s') >= $sFrom
                    )
                        @php
                        $iCount++;
                        @endphp

                        <div class="mb-2 p-1 appointment-hour"
                             data-year="{{ $oRequestDate->format('Y') }}"
                             data-month="{{ $oRequestDate->format('m') }}"
                             data-month-label="{{ $oRequestDate->format('F') }}"
                             data-day="{{ $oRequestDate->format('d') }}"
                             data-hour="{{ $oRequestDate->format('H:i a') }}"
                        >{{ $oRequestDate->format('H:i a') }}</div>
                    @endif
                @endfor
            @endforeach

            @if ($iCount == 0)
                <div class="text-center">{{ __('There are no more available hours') }}</div>
            @endif
        @else
            <div class="text-center">{{ __('There are no more available hours') }}</div>
        @endif
    </div>

    <div class="col-12 col-md-4 text-center mt-2 mt-md-0 sticky-content">
        <h5 class="mb-2 sticky-item">{{ __('Night') }}</h5>

        @if ((bool) $aNight)
            @php
            $iCount = 0;
            @endphp

            @foreach ($aNight as $iHour)
                @php
                $oRequestDate->hour($iHour);
                @endphp

                @for ($iMinute=0; $iMinute<$iAppointmentsPerHour; $iMinute++)
                    @php
                    $oRequestDate->minute($iMinute * $iAppointmentMinutes);
                    @endphp

                    @if (
                        !validateGrantedAppointments($oRequestDate->format('H:i'), $aGrantedAppointments, $iAppointmentsByTime) &&
                        !validateDateTimeInException($oRequestDate->format('Y-m-d H:i:s'), $aExceptions) &&
                        (
                            !(bool) $aAppointmentToExclude || $aAppointmentToExclude['date']!=$oRequestDate->format('Y-m-d') ||
                            ((bool) $aAppointmentToExclude && $aAppointmentToExclude['date']==$oRequestDate->format('Y-m-d') && $aAppointmentToExclude['time']!=$oRequestDate->format('H:i:s'))
                        ) &&
                        (
                            ($oToday->format('Y-m-d') < $oRequestDate->format('Y-m-d')) ||
                            ($oToday->format('Y-m-d')==$oRequestDate->format('Y-m-d') && $oToday->format('H:i')<$oRequestDate->format('H:i'))
                        ) &&
                        $oRequestDate->format('H:i:s') <= $sUntil &&
                        $oRequestDate->format('H:i:s') >= $sFrom
                    )
                        @php
                        $iCount++;
                        @endphp

                    <div class="mb-2 p-1 appointment-hour"
                         data-year="{{ $oRequestDate->format('Y') }}"
                         data-month="{{ $oRequestDate->format('m') }}"
                         data-month-label="{{ $oRequestDate->format('F') }}"
                         data-day="{{ $oRequestDate->format('d') }}"
                         data-hour="{{ $oRequestDate->format('H:i a') }}"
                    >{{ $oRequestDate->format('H:i a') }}</div>
                    @endif
                @endfor
            @endforeach

            @if ($iCount == 0)
                <div class="text-center">{{ __('There are no more available hours') }}</div>
            @endif
        @else
            <div class="text-center">{{ __('There are no more available hours') }}</div>
        @endif
    </div>
</div>