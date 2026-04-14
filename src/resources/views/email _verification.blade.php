@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/email_verification.css') }}">
@endsection
@section('content')
<div class="verification">
    <div class="verification__message">
        <p class="verification__text">登録いただいたメールアドレスに認証メールを送付いたしました。<br>メール認証を完了してください。</p>
    </div>
    <div class="verification__actions">
        <a class="verification__button--primary" href="http://localhost:8025" target="_blank">認証はこちらから</a>
    </div>
    <div>
        <form class="verification__form" action="{{ route('verification.send') }}" method="post">
            @csrf
            <button class="verification__button">認証メールを再送する</button>
        </form>
    </div>
</div>
@endsection