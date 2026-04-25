<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance managementy</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>
<body>
    <header class="header">
        <div  class="header__logo">
            <a href="">
                <img class="header__logo-image" src="{{ asset('image/COACHTECHヘッダーロゴ.png') }}" alt="ロゴ">
            </a>
        </div>
        @auth
            <div class="header__nav">
                <div>
                    <a class="header__nav-item" href="{{ route('attendance.show') }}">勤怠</a>
                </div>
                <div>
                    <a class="header__nav-item" href="{{ route('attendance.list') }}">勤怠一覧</a>
                </div>
                <div>
                    <a class="header__nav-item" href="{{ route('attendance.application.list') }}">申請</a>
                </div>
                <div>
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button class="header__nav-logout" type="submit">ログアウト</button>
                    </form>
                </div>
            </div>
        @endauth
    </header>
    <main>
        @yield('content')
    </main>
</body>
</html>