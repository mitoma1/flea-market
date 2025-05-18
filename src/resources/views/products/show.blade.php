<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>{{ $product->name }} - 商品詳細</title>
    <link rel="stylesheet" href="{{ asset('css/product_show.css') }}">
</head>

<body>
    <header>
        <div class="header-logo">
            <img src="{{ asset('images/logo.svg') }}" alt="ロゴ" class="logo">
        </div>

        <nav>
            @auth
            <a href="{{ route('mypage') }}">マイページ</a>
            <a href="{{ route('products.index') }}">商品一覧</a>
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
        <div class="product-detail">
            <div class="product-image">
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
            </div>
            <div class="product-info">
                <h2 class="product-name">{{ $product->name }}</h2>
                <p class="product-price">¥{{ number_format($product->price) }}</p>

                <div class="action-buttons">
                    <form action="{{ route('recommend', $product->id) }}" method="POST">
                        @csrf
                        <button type="submit">★ いいね</button>
                    </form>
                    <a href="#comment-form">
                        <button type="button">💬 コメント</button>
                    </a>
                    <form action="{{ route('purchase', $product->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="buy-button">購入手続き</button>
                    </form>
                </div>

                <div class="product-description">
                    <h3>商品説明</h3>
                    <p>{{ $product->description }}</p>
                </div>

                <div class="product-meta">
                    <h3>商品情報</h3>
                    <dl>
                        <dt>カテゴリー：</dt>
                        <dd>{{ $product->category->name ?? '未設定' }}</dd>
                        <dt>商品状態：</dt>
                        <dd>{{ $product->condition ?? '不明' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="comment-section">
            <h3>コメント</h3>

            @forelse ($product->comments ?? [] as $comment)
            <div class="comment">
                <strong>{{ $comment->user->name ?? 'ユーザー' }}</strong>：
                <p>{{ $comment->content }}</p>
            </div>
            @empty
            <p>コメントはまだありません。</p>
            @endforelse

            @auth
            <div class="comment-form" id="comment-form">
                <form action="{{ route('comments.store', $product->id) }}" method="POST">
                    @csrf
                    <textarea name="content" placeholder="コメントを入力してください..." required></textarea>
                    <br>
                    <button type="submit">コメントを送信</button>
                </form>
            </div>
            @else
            <p><a href="{{ route('login') }}">ログイン</a>してコメントしましょう！</p>
            @endauth
        </div>
    </main>
</body>

</html>