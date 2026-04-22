@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}">
@endsection
@section('content')
<div class="attendance-list">
    <h1 class="attendance-list__title">勤怠一覧</h1>

    <div class="attendance-list__month-nav">
        <a class="attendance-list__month-nav-prev" href="{{ route('attendance.list', ['month' => $prev_month]) }}">
            <img class="left-img" src="{{ asset('image/left.png') }}" alt="Previous Month"> 前月
        </a>
        <span class="attendance-list__month-nav-current">
            <img class="calendar-img" src="{{ asset('image/calendar.png') }}" alt="Calendar">
            {{ $month->format('Y') }}年 
            {{ $month->format('n') }}月
        </span>
        <a class="attendance-list__month-nav-next" href="{{ route('attendance.list', ['month' => $next_month]) }}">
            翌月 <img class="right-img" class="right-img" src="{{ asset('image/right.png') }}" alt="Next Month">
        </a>
    </div>

    <table class="attendance-list__table">
        <thead>
            <tr>
                <th class="attendance-list__th attendance-list__th-date">日付</th>
                <th class="attendance-list__th">出勤</th>
                <th class="attendance-list__th">退勤</th>
                <th class="attendance-list__th">休憩</th>
                <th class="attendance-list__th">合計</th>
                <th class="attendance-list__th attendance-list__th-detail">詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dates as $date)
            <tr class="attendance-list__tr">
                <td class="attendance-list__td">
                    {{ \Carbon\Carbon::parse($date)->format('m/d') }}
                </td>
                @php
                    $key = \Carbon\Carbon::parse($date)->format('Y-m-d');
                    $attendance = $attendances[$key] ?? null;
                @endphp 
                    <td class="attendance-list__td">
                        {{ $attendance ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '' }}
                    </td>
                    <td class="attendance-list__td">
                        {{ $attendance ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '' }}
                    </td>
                    <td class="attendance-list__td">
                        {{ optional($attendance)->break_time ?? '' }}
                    </td>
                    <td class="attendance-list__td">{{ $attendance ? $attendance->total_time : '' }}</td>
                <td class="attendance-list__td">
                    <a class="attendance-list__detail-link" href="{{ $attendance ?route('attendance.detail', ['id' => $attendance->id]) : '#' }}">詳細</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
