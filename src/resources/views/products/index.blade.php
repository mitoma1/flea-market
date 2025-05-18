<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>商品一覧</title>
    <link rel="stylesheet" href="{{ asset('css/products.css') }}">
</head>

<body>
    <header>
        <div class="header-logo">
            <img src="{{ asset('images/logo.svg') }}" alt="ロゴ" class="logo">
        </div>

        <nav>
            @auth
            <a href="{{ route('mypage') }}">マイページ</a>
            <a href="{{ route('products.create') }}">+ 商品出品</a>
            <a href="{{ route('recommend') }}">おすすめ</a>
            <a href="{{ route('mylist') }}">マイリスト</a>
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                ログアウト
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            @else
            <a href="{{ route('login') }}">ログイン</a>
            <a href="{{ route('register') }}">新規登録</a>
            @endauth
        </nav>
    </header>

    <main>
        <div class="product-list">
            @foreach ($products as $product)
            <div class="product-card">
                <a href="{{ route('products.show', $product->id) }}">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" width="200">
                    <p>{{ $product->name }}</p>
                </a>
            </div>
            @endforeach

            @if($products->isEmpty())
            <p>商品が見つかりませんでした。</p>
            @endif
        </div>
    </main>
</body>

</html>