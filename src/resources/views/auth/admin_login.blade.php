@extends('layouts.admin_header')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_login.css') }}">
@endsection
@section('content')
    <form action="{{ route('admin.login') }}" method="post" class="form">
        @csrf
        <div class="form__header">
            <h1 class="form__title">管理者ログイン</h1>
        </div>
        <div class="form__group">
            <div>
                <label class="form__label">メールアドレス</label>
            </div>
            <div>
                <input type="text" name="email" class="form__input" value="{{ old('email') }}">
            </div>
        </div>
        <div class="form__error">
            @if($errors->has('email'))
                <div class="error">{{ $errors->first('email') }}</div>
            @endif
        </div>
        <div class="form__group">
            <div>
                <label class="form__label">パスワード</label>
            </div>
            <div>
                <input type="password" name="password" class="form__input" value="">
            </div>
        </div>
        <div class="form__error">
            @if($errors->has('password'))
                <div class="error">{{ $errors->first('password') }}</div>
            @endif
        </div>
        <div class="form__actions">
            <button class="form__button">管理者ログインする</button>
        </div>
    </form>
@endsection
