@extends('layouts.admin_header')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_attendance_detail.css') }}">
@endsection
@section('content')
<div class="attendance-detail">
    <h1 class="attendance-detail__title">勤怠詳細</h1>
    <form action="{{ route('admin.attendance.detail.update', ['id' => $attendance->id]) }}" method="POST">
        @csrf
        @method('PUT')
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
                        <input class="detail-input" type="time" name="clock_in"
                            value="{{old('clock_in', \Carbon\Carbon::parse($attendance->clock_in)->format('H:i'))  }}">
                    </td>
                    <td>〜</td>
                    <td>
                        <input class="detail-input" type="time" name="clock_out"
                            value="{{ old('clock_out', \Carbon\Carbon::parse($attendance->clock_out)->format('H:i')) }}">
                    </td>
                    <td></td>
                </tr>
                @if ($errors->has('clock_in'))
                    <tr>
                        <td colspan="3">
                            <div class="form__error">{{ $errors->first('clock_in') }}</div>
                        </td>
                    </tr>
                @endif
                @if ($errors->has('clock_out'))
                    <tr>
                        <td colspan="3">
                            <div class="form__error">{{ $errors->first('clock_out') }}</div>
                        </td>
                    </tr>
                @endif
                @php $count = 0; @endphp
                @forelse($attendance->break_times as $break_time)
                    @php $count++; @endphp
                    <tr class="attendance-detail__tr">
                        <td class="attendance-detail__td">休憩 {{ $count === 1 ? '' : $count }}</td>
                        <td class="attendance-detail__td">
                            <input class="detail-input" type="time" name="start_time{{ $break_time->id }}"
                                value="{{ \Carbon\Carbon::parse($break_time->start_time)->format('H:i') }}">
                        </td>
                        <td>〜</td>
                        <td>
                            <input class="detail-input" type="time" name="end_time{{ $break_time->id }}"
                                value="{{ \Carbon\Carbon::parse($break_time->end_time)->format('H:i') }}">
                        </td>
                        <td></td>
                    </tr>
                    @if ($errors->has('start_time' . $break_time->id))
                        <tr>
                            <td colspan="3">
                                <div class="form__error">{{ $errors->first('start_time' . $break_time->id) }}</div>
                            </td>
                        </tr>
                    @endif
                    @if ($errors->has('end_time' . $break_time->id))
                        <tr>
                            <td colspan="3">
                                <div class="form__error">{{ $errors->first('end_time' . $break_time->id) }}</div>
                            </td>
                        </tr>
                    @endif
                @empty
                @endforelse
                <tr class="attendance-detail__tr">
                    <td class="attendance-detail__td">休憩 {{ $count === 0 ? '' : $count + 1 }}</td>
                    <td class="attendance-detail__td">
                        <input class="detail-input" type="time" name="new_start_time" value="{{ old('new_start_time') }}">
                    </td>
                    <td>〜</td>
                    <td>
                        <input class="detail-input" type="time" name="new_end_time" value="{{ old('new_end_time') }}">
                    </td>
                    <td></td>
                </tr>
                @if ($errors->has('new_start_time'))
                    <tr>
                        <td colspan="3">
                            <div class="form__error">{{ $errors->first('new_start_time') }}</div>
                        </td>
                    </tr>
                @endif
                @if ($errors->has('new_end_time'))
                    <tr>
                        <td colspan="3">
                            <div class="form__error">{{ $errors->first('new_end_time') }}</div>
                        </td>
                    </tr>
                @endif
                <tr class="detail-remarks-row no-column-width">
                    <td class="attendance-detail__td detail-remarks-label">備考</td>
                    <td colspan="3" class="attendance-detail__td">
                        <textarea name="remarks" class="attendance-detail__textarea">{{ $attendance->remarks }}</textarea>
                    </td>
                    <td class="detail-remarks"></td>
                </tr>
                @if ($errors->has('remarks'))
                    <tr>
                        <td colspan="3">
                            <div class="form__error">{{ $errors->first('remarks') }}</div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="button-container">
            <button type="submit" class="btn btn-primary">修正</button>
        </div>
    </form>
</div>
@endsection
