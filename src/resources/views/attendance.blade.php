@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection
@section('content')
   <div class="attendance">
        <div class="attendance__status"> 
            <label  class="attendance__status-label">{{ $attendance ? $attendance->status : '勤務外' }}</label>
        </div>
        <div class="attendance__date">
            <p class="attendance__date-text">
                {{ $now->format('Y') }}年
                {{ $now->format('n') }}月
                {{ $now->format('j') }}日
                ({{ $now->isoFormat('dd') }})
            </p>
        </div>
        <div class="attendance__time">
            <p class="attendance__time-text" id="clock">{{ $now->format('H:i') }}</p>
        </div>
        @if($attendance?->status === '退勤済')
            <div class="attendance__clock-out-time">
                 <p class="clock_out-text">お疲れ様でした。</p>
            </div>
        @endif
        @php
            $is_today = isset($attendance) && \Carbon\Carbon::parse($attendance->date)->isToday();
        @endphp
        <div class="attendance__action">
            @if(!isset($attendance) || !$is_today)
                <form action="{{ route('attendance.clock_in') }}" method="POST">
                    @csrf
                    <input type="hidden" name="date" value="{{ $now }}">
                    <button class="attendance__button" type="submit">出勤</button>
                </form>
            @elseif($attendance->status === '出勤中')
                <form action="{{ route('attendance.clock_out') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="date" value="{{ $now }}">
                    <button class="attendance__button" type="submit">退勤</button>
                </form>
                <form action="{{ route('attendance.break_start') }}" method="POST" style="display: inline;">
                    @csrf
                    <button class="attendance__button break__button" type="submit">休憩入</button>
                </form>
            @elseif($attendance->status === '休憩中')
                <form action="{{ route('attendance.break_end') }}" method="POST">
                    @csrf
                    <button class="attendance__button break__button" type="submit">休憩戻</button>
                </form>
            @endif
        </div>
   </div>
   <script src="{{ asset('js/time_count.js') }}"></script>
@endsection