@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            @include('flash::message')
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <ul class="list-group">
            @php
            $sCurrentDate = '';
            @endphp

            @foreach ($aGrantedAppointments as $iKey=>$aAppointment)
                @php
                if ($sCurrentDate != $aAppointment['date']) {
                    $sCurrentDate = $aAppointment['date'];
                @endphp
                <li class="list-group-item list-group-item-secondary font-weight-bold text-center p-2 mt-3">{{ Date::createFromFormat('Y-m-d', $aAppointment['date'])->format(__('l j \\of F')) }}</li>
                @php
                }
                @endphp
                <li class="list-group-item p-2">{{ Date::createFromFormat('H:i:s', $aAppointment['time'])->format('H:i a') }} | {{ $aAppointment['name'] }}</li>
            @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection