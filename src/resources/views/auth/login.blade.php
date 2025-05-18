<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <title>ログイン</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}" />
</head>

<body>

    <!-- ヘッダー -->
    <header class="header">
        <div class="header-inner">
            <a href="{{ route('products.index') }}">
                <img src="{{ asset('images/logo.svg') }}" alt="ロゴ" class="logo" />
            </a>
            <nav class="header-nav">
                <a href="{{ route('register') }}">会員登録</a>
                <a href="{{ route('login') }}">ログイン</a>
            </nav>
        </div>
    </header>

    <!-- メイン中央揃え -->
    <main class="login-main">
        <div class="login-container">
            <h2>ログイン</h2>

            <form action="{{ route('login.submit') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" />
                    @error('email')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" name="password" id="password" />
                    @error('password')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-login">ログインする</button>
            </form>

            <div class="register-link">
                <a href="{{ route('register') }}" class="btn-register">会員登録はこちら</a>
            </div>
        </div>
    </main>
</body>

</html>