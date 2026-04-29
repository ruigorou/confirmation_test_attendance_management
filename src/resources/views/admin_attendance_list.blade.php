@extends('layouts.admin_header')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_attendance_list.css') }}">
@endsection
@section('content')
<div class="attendance-list">
    <h1 class="attendance-list__title">{{ $date->format('Y年n月j日') }}の勤怠</h1>

    <div class="attendance-list__month-nav">
        <a class="attendance-list__month-nav-prev" href="{{ route('admin.attendance.list', ['date' => $prev_date]) }}">
            <img class="left-img" src="{{ asset('image/left.png') }}" alt="Previous Day"> 前日
        </a>
        <span class="attendance-list__month-nav-current">
            <img class="calendar-img" src="{{ asset('image/calendar.png') }}" alt="Calendar">
            {{ $date->format('Y/m/d') }}
        </span>
        <a class="attendance-list__month-nav-next" href="{{ route('admin.attendance.list', ['date' => $next_date]) }}">
            翌日 <img class="right-img" src="{{ asset('image/right.png') }}" alt="Next Day">
        </a>
    </div>

    <table class="attendance-list__table">
        <thead>
            <tr>
                <th class="attendance-list__th attendance-list__th-date">名前</th>
                <th class="attendance-list__th">出勤</th>
                <th class="attendance-list__th">退勤</th>
                <th class="attendance-list__th">休憩</th>
                <th class="attendance-list__th">合計</th>
                <th class="attendance-list__th attendance-list__th-detail">詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $attendance)
            <tr class="attendance-list__tr">
                <td class="attendance-list__td">{{ $attendance->user->name }}</td>
                <td class="attendance-list__td">
                    {{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '' }}
                </td>
                <td class="attendance-list__td">
                    {{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '' }}
                </td>
                <td class="attendance-list__td">{{ $attendance->break_time ?? '' }}</td>
                <td class="attendance-list__td">{{ $attendance->total_time ?? '' }}</td>
                <td class="attendance-list__td">
                    <a class="attendance-list__detail-link" href="{{ route('admin.attendance.detail', ['id' => $attendance->id]) }}">詳細</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
