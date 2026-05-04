@extends('layouts.admin_header')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_attendance_staff.css') }}">
@endsection
@section('content')
<div class="attendance-list">
    <h1 class="attendance-list__title">{{ $user->name }}さんの勤怠</h1>

    <div class="attendance-list__month-nav">
        <a class="attendance-list__month-nav-prev" href="{{ route('admin.attendance.staff', ['id' => $user->id, 'month' => $prev_month]) }}">
            <img class="left-img" src="{{ asset('image/left.png') }}" alt="Previous Month"> 前月
        </a>
        <span class="attendance-list__month-nav-current">
            <img class="calendar-img" src="{{ asset('image/calendar.png') }}" alt="Calendar">
            {{ $month->format('Y') }}年
            {{ $month->format('n') }}月
        </span>
        <a class="attendance-list__month-nav-next" href="{{ route('admin.attendance.staff', ['id' => $user->id, 'month' => $next_month]) }}">
            翌月 <img class="right-img" src="{{ asset('image/right.png') }}" alt="Next Month">
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
            @php
                $key = $date->format('Y-m-d');
                $attendance = $attendances[$key] ?? null;
            @endphp
            <tr class="attendance-list__tr">
                <td class="attendance-list__td">
                    {{ $date->isoformat('MM/DD(ddd)') }}
                </td>
                <td class="attendance-list__td">
                    {{ $attendance ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '' }}
                </td>
                <td class="attendance-list__td">
                    {{ $attendance ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '' }}
                </td>
                <td class="attendance-list__td">{{ optional($attendance)->break_time ?? '' }}</td>
                <td class="attendance-list__td">{{ $attendance ? $attendance->total_time : '' }}</td>
                <td class="attendance-list__td">
                    <a class="attendance-list__detail-link"
                       href="{{ $attendance ? route('admin.attendance.detail', ['id' => $attendance->id]) : '#' }}">詳細</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="attendance-list__csv">
        <a class="attendance-list__csv-link" href="{{ route('admin.export.csv', ['id' => $user->id,  'month' => $month->format('Y-m')]) }}">CSV出力</a>
    </div>
</div>
@endsection
