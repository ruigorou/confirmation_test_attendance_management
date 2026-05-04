@extends('layouts.admin_header')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_staff_list.css') }}">
@endsection
@section('content')
<div class="attendance-list">
    <h1 class="attendance-list__title">スタッフ一覧</h1>

    <table class="attendance-list__table">
        <thead>
            <tr>
                <th class="attendance-list__th attendance-list__th-date">名前</th>
                <th class="attendance-list__th">メールアドレス</th>
                <th class="attendance-list__th attendance-list__th-detail">月次勤怠</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr class="attendance-list__tr">
                <td class="attendance-list__td">{{ $user->name }}</td>
                <td class="attendance-list__td">{{ $user->email }}</td>
                <td class="attendance-list__td">
                    <a class="attendance-list__link" href="{{ route('admin.attendance.staff', ['id' => $user->id]) }}">詳細</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
