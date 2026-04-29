@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance_application_list.css') }}">
@endsection
@section('content')
<div class="application-list">
    <h1 class="application-list__title">申請一覧</h1>

    <div class="application-list__tabs">
        <a class="application-list__tab {{ $tab === '承認待ち' ? 'application-list__tab--active' : '' }}"
           href="{{ route('attendance.application.list', ['tab' => '承認待ち']) }}">承認待ち</a>
        <a class="application-list__tab {{ $tab === '承認済み' ? 'application-list__tab--active' : '' }}"
           href="{{ route('attendance.application.list', ['tab' => '承認済み']) }}">承認済み</a>
    </div>

    <table class="application-list__table">
        <thead>
            <tr>
                <th class="application-list__th application-list__th-first">状態</th>
                <th class="application-list__th">名前</th>
                <th class="application-list__th">対象日付</th>
                <th class="application-list__th">申請理由</th>
                <th class="application-list__th">申請日時</th>
                <th class="application-list__th application-list__th-last">詳細</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $attendance)
            <tr class="application-list__tr">
                <td class="application-list__td">{{ $attendance->approval_status }}</td>
                <td class="application-list__td">{{ $attendance->user->name }}</td>
                <td class="application-list__td">
                    {{ \Carbon\Carbon::parse($attendance->date)->format('Y/m/d') }}
                </td>
                <td class="application-list__td">{{ $attendance->remarks }}</td>
                <td class="application-list__td">
                    {{ \Carbon\Carbon::parse($attendance->updated_at)->format('Y/m/d') }}
                </td>
                <td class="application-list__td">
                    <a class="application-list__detail-link" href="{{ route('attendance.detail', ['id' => $attendance->id]) }}">詳細</a>
                </td>
            </tr>
            @empty
            <tr class="application-list__tr">
                <td class="application-list__td" colspan="6">申請データがありません</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
