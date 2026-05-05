@extends('layouts.admin_header')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_approval_of_amendment_application.css') }}">
@endsection
@section('content')
<div class="attendance-detail">
    <h1 class="attendance-detail__title">勤怠詳細</h1>
    <table class="attendance-detail__table">
        <tbody>
            <tr class="attendance-detail__tr">
                <td class="attendance-detail__td detail-label">名前</td>
                <td class="attendance-detail__td">{{ $attendance->user->name }}</td>
                <td></td>
                <td></td>
                <td class="detail-value"></td>
            </tr>
            <tr class="attendance-detail__tr">
                <td class="attendance-detail__td">日付</td>
                <td class="attendance-detail__td detail-date">
                    {{ \Carbon\Carbon::parse($attendance->date)->format('Y') }}年
                </td>
                <td></td>
                <td>{{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}</td>
                <td></td>
            </tr>
            <tr class="attendance-detail__tr">
                <td class="attendance-detail__td">出勤・退勤</td>
                <td class="attendance-detail__td">
                   {{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') }}
                </td>
                <td>〜</td>
                <td>
                    {{ \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') }}
                </td>
                <td></td>
            </tr>
            @php $count = 0; @endphp
            @forelse($attendance->break_times as $break_time)
                @php $count++; @endphp
                <tr class="attendance-detail__tr">
                    <td class="attendance-detail__td">休憩 {{ $count === 1 ? '' : $count }}</td>
                    <td class="attendance-detail__td">
                        {{ \Carbon\Carbon::parse($break_time->start_time)->format('H:i') }}
                    </td>
                    <td>〜</td>
                    <td>
                        {{ \Carbon\Carbon::parse($break_time->end_time)->format('H:i') }}
                    </td>
                    <td></td>
                </tr>
            @empty
            @endforelse
            <tr class="detail-remarks-row no-column-width">
                <td class="attendance-detail__td detail-remarks-label">備考</td>
                <td colspan="3" class="attendance-detail__td">
                   {{ $attendance->remarks }}
                </td>
                <td class="detail-remarks"></td>
            </tr>
        </tbody>
    </table>

    <div class="button-container">
        @if($attendance->approval_status === '承認済み')
            <button class="btn btn-approved" disabled>承認済み</button>
        @else
            <form action="{{ route('admin.approval.approve', ['attendance_correct_request_id' => $attendance->id]) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-primary">承認</button>
            </form>
        @endif
    </div>
</div>
@endsection
