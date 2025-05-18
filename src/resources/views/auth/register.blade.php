<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>会員登録</title>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>

<body>

    <!-- ヘッダー -->
    <header class="header">
        <div class="header-inner">
            <a href="{{ route('products.index') }}">
                <img src="{{ asset('images/logo.svg') }}" alt="ロゴ" class="logo">
            </a>
        </div>
    </header>

    <!-- フォームを中央に置くためのラッパー -->
    <div class="register-main">
        <div class="register-container">
            <h2>会員登録</h2>

            <form action="{{ route('register.submit') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">ユーザー名</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}">
                    @error('name')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}">
                    @error('email')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" name="password" id="password">
                    @error('password')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">確認用パスワード</label>
                    <input type="password" name="password_confirmation" id="password_confirmation">
                    @error('password_confirmation')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-register">登録する</button>
            </form>

            <div class="login-link">
                <a href="{{ route('login') }}" class="btn-login">ログインはこちら</a>
            </div>
        </div>
    </div>

</body>

</html>