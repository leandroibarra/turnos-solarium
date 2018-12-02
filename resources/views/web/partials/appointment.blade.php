<div class="row">
    <div class="col-12 col-md-4 text-center">
        <h5 class="mb-2">{{ __('Morning') }}</h5>

        @if ((bool) $aMorning)
            @php
            $iCount = 0;
            @endphp

            @foreach ($aMorning as $iHour)
                @php
                $oRequestDateTime->hour($iHour);
                @endphp

                @for ($iMinute=0; $iMinute<6; $iMinute++)
                    @php
                    $oRequestDateTime->minute($iMinute * $iAppointmentMinutes);
                    @endphp

                    @if (
                        !validateGrantedAppointments($oRequestDateTime->format('H:i'), $aGrantedAppointments) &&
                        (
                            !(bool) $aAppointmentToExclude || $aAppointmentToExclude['date']!=$oRequestDateTime->format('Y-m-d') ||
                            ((bool) $aAppointmentToExclude && $aAppointmentToExclude['date']==$oRequestDateTime->format('Y-m-d') && $aAppointmentToExclude['time']!=$oRequestDateTime->format('H:i:s'))
                        ) &&
                        (
                            ($oToday->format('Y-m-d') < $oRequestDateTime->format('Y-m-d')) ||
                            ($oToday->format('Y-m-d')==$oRequestDateTime->format('Y-m-d') && $oToday->format('H:i')<$oRequestDateTime->format('H:i'))
                        )
                    )
                        @php
                        $iCount++;
                        @endphp

                        <div class="mb-2 p-1 appointment-hour"
                             data-year="{{ $oRequestDateTime->format('Y') }}"
                             data-month="{{ $oRequestDateTime->format('m') }}"
                             data-month-label="{{ $oRequestDateTime->format('F') }}"
                             data-day="{{ $oRequestDateTime->format('d') }}"
                             data-hour="{{ $oRequestDateTime->format('H:i a') }}"
                         >{{ $oRequestDateTime->format('H:i a') }}</div>
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

    <div class="col-12 col-md-4 text-center mt-2 mt-md-0">
        <h5 class="mb-2">{{ __('Afternoon') }}</h5>

        @if ((bool) $aAfternoon)
            @php
            $iCount = 0;
            @endphp

            @foreach ($aAfternoon as $iHour)
                @php
                $oRequestDateTime->hour($iHour);
                @endphp

                @for ($iMinute=0; $iMinute<6; $iMinute++)
                    @php
                    $oRequestDateTime->minute($iMinute * $iAppointmentMinutes);
                    @endphp

                    @if (
                        !validateGrantedAppointments($oRequestDateTime->format('H:i'), $aGrantedAppointments) &&
                        (
                            !(bool) $aAppointmentToExclude || $aAppointmentToExclude['date']!=$oRequestDateTime->format('Y-m-d') ||
                            ((bool) $aAppointmentToExclude && $aAppointmentToExclude['date']==$oRequestDateTime->format('Y-m-d') && $aAppointmentToExclude['time']!=$oRequestDateTime->format('H:i:s'))
                        ) &&
                        (
                            ($oToday->format('Y-m-d') < $oRequestDateTime->format('Y-m-d')) ||
                            ($oToday->format('Y-m-d')==$oRequestDateTime->format('Y-m-d') && $oToday->format('H:i')<$oRequestDateTime->format('H:i'))
                        )
                    )
                        @php
                        $iCount++;
                        @endphp

                        <div class="mb-2 p-1 appointment-hour"
                             data-year="{{ $oRequestDateTime->format('Y') }}"
                             data-month="{{ $oRequestDateTime->format('m') }}"
                             data-month-label="{{ $oRequestDateTime->format('F') }}"
                             data-day="{{ $oRequestDateTime->format('d') }}"
                             data-hour="{{ $oRequestDateTime->format('H:i a') }}"
                        >{{ $oRequestDateTime->format('H:i a') }}</div>
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

    <div class="col-12 col-md-4 text-center mt-2 mt-md-0">
        <h5 class="mb-2">{{ __('Night') }}</h5>

        @if ((bool) $aNight)
            @php
            $iCount = 0;
            @endphp

            @foreach ($aNight as $iHour)
                @php
                $oRequestDateTime->hour($iHour);
                @endphp

                {{--Non process last hour of working day (non inclusive)--}}
                @if ($oRequestDateTime->format('H') == end($aNight))
                    @php
                    continue;
                    @endphp
                @endif

                @for ($iMinute=0; $iMinute<6; $iMinute++)
                    @php
                    $oRequestDateTime->minute($iMinute * $iAppointmentMinutes);
                    @endphp

                    @if (
                        !validateGrantedAppointments($oRequestDateTime->format('H:i'), $aGrantedAppointments) &&
                        (
                            !(bool) $aAppointmentToExclude || $aAppointmentToExclude['date']!=$oRequestDateTime->format('Y-m-d') ||
                            ((bool) $aAppointmentToExclude && $aAppointmentToExclude['date']==$oRequestDateTime->format('Y-m-d') && $aAppointmentToExclude['time']!=$oRequestDateTime->format('H:i:s'))
                        ) &&
                        (
                            ($oToday->format('Y-m-d') < $oRequestDateTime->format('Y-m-d')) ||
                            ($oToday->format('Y-m-d')==$oRequestDateTime->format('Y-m-d') && $oToday->format('H:i')<$oRequestDateTime->format('H:i'))
                        )
                    )
                        @php
                        $iCount++;
                        @endphp

                    <div class="mb-2 p-1 appointment-hour"
                         data-year="{{ $oRequestDateTime->format('Y') }}"
                         data-month="{{ $oRequestDateTime->format('m') }}"
                         data-month-label="{{ $oRequestDateTime->format('F') }}"
                         data-day="{{ $oRequestDateTime->format('d') }}"
                         data-hour="{{ $oRequestDateTime->format('H:i a') }}"
                    >{{ $oRequestDateTime->format('H:i a') }}</div>
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