@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}">
@endsection
@section('content')
<div class="attendance-list">
    <h1 class="attendance-list__title">勤怠一覧</h1>

    <div class="attendance-list__month-nav">
        <a class="attendance-list__month-nav-prev" href="{{ route('attendance.list', ['month' => $prev_month]) }}">← 前月</a>
        <span class="attendance-list__month-nav-current">
            <i class="fa-regular fa-calendar-days"></i>
            {{ $month->format('Y') }}年 
            {{ $month->format('n') }}月
        </span>
        <a class="attendance-list__month-nav-next" href="{{ route('attendance.list', ['month' => $next_month]) }}">翌月 →</a>
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
            @foreach($attendances as $attendance)
            <tr class="attendance-list__tr">
                <td class="attendance-list__td">{{ \Carbon\Carbon::parse($attendance->date)->format('m/d') }}({{ \Carbon\Carbon::parse($attendance->date)->isoFormat('dd') }})</td>
                <td class="attendance-list__td">{{ $attendance->clock_in ? substr($attendance->clock_in, 0, 5) : '-' }}</td>
                <td class="attendance-list__td">{{ $attendance->clock_out ? substr($attendance->clock_out, 0, 5) : '-' }}</td>
                <td class="attendance-list__td">{{ $attendance->break_time ?? '-' }}</td>
                <td class="attendance-list__td">{{ $attendance->total_time ?? '-' }}</td>
                <td class="attendance-list__td">
                    <a class="attendance-list__detail-link" href="#">詳細</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
