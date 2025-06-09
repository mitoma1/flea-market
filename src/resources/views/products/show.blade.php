@extends('layouts.app')

@section('title', $product->name . ' - 商品詳細')

@section('content')
<link rel="stylesheet" href="{{ asset('css/product_show.css') }}">

<div class="product-detail-wrapper">
    <div class="product-detail-main">
        <div class="product-image-area">
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-main-image">
        </div>

        <div class="product-info-area">
            <h1 class="product-name">{{ $product->name }}</h1>
            <p class="product-price">¥{{ number_format($product->price) }} <span class="tax-info">(税込)</span></p>

            <div class="product-actions">
                {{-- いいねボタンフォーム --}}
                <form id="favorite-form" action="{{ route('favorites.toggle', $product->id) }}" method="POST">
                    @csrf
                    <button type="submit" id="favorite-button" class="action-button favorite-button {{ auth()->user() && auth()->user()->favoriteProducts->contains($product->id) ? 'liked' : '' }}">
                        <span class="icon">★</span> いいね
                    </button>
                    <span id="likes-count" class="likes-count">{{ $product->likedUsers->count() }}</span>
                </form>

                <a href="#comment-form" class="action-button comment-button">
                    <span class="icon">💬</span> コメント
                </a>
            </div>

            <a href="{{ route('products.purchase.show', ['id' => $product->id]) }}" class="purchase-button-link">
                <button type="button" class="purchase-button">購入手続きへ</button>
            </a>

            <div class="product-description-area">
                <h3 class="section-title">商品説明</h3>
                <p class="description-text">{{ $product->description }}</p>
            </div>

            <div class="product-meta-area">
                <h3 class="section-title">商品情報</h3>
                <dl class="product-meta-list">
                    <dt>カテゴリー：</dt>
                    <dd>{{ $product->category->name ?? '未設定' }}</dd>

                    <dt>商品状態：</dt>
                    <dd>{{ $product->condition ?? '不明' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- コメントセクション --}}
    <div class="comment-section mt-10">
        <h3 class="section-title">コメント（{{ $product->comments->count() }}）</h3>

        {{-- コメント一覧 --}}
        @forelse ($product->comments ?? [] as $comment)
        <div class="comment flex items-start bg-gray-100 p-4 rounded-lg mb-4">
            {{-- ユーザーアイコンの代用 --}}
            <div class="w-10 h-10 bg-gray-300 rounded-full flex-shrink-0 mr-4"></div>
            <div>
                <p class="font-semibold text-gray-800">{{ $comment->user->name ?? 'ユーザー' }}</p>
                <p class="text-gray-600 mt-1">{{ $comment->comment }}</p>
            </div>
        </div>
        @empty
        <p class="no-comments-message text-gray-500">コメントはまだありません。</p>
        @endforelse

        {{-- コメント投稿フォーム --}}
        @auth
        <div class="comment-form-area mt-6" id="comment-form">
            <form action="{{ route('comments.store', $product->id) }}" method="POST" class="comment-form">
                @csrf
                <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">商品へのコメント</label>
                <textarea name="comment" id="comment" rows="4" class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-red-400" placeholder="コメントを入力してください...">{{ old('comment') }}</textarea>
                @error('comment')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 mt-2">
                    コメントを送信
                </button>
            </form>
        </div>
        @else
        <p class="login-prompt mt-4">
            <a href="{{ route('login') }}" class="text-blue-600 underline">ログイン</a>してコメントしましょう！
        </p>
        @endauth
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const favoriteForm = document.getElementById('favorite-form');
        const favoriteButton = document.getElementById('favorite-button');
        const likesCount = document.getElementById('likes-count');

        if (favoriteForm) {
            favoriteForm.addEventListener('submit', function(e) {
                e.preventDefault();

                fetch(favoriteForm.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('通信エラー');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'liked') {
                            favoriteButton.classList.add('liked');
                        } else {
                            favoriteButton.classList.remove('liked');
                        }
                        likesCount.textContent = data.likes_count;
                    })
                    .catch(error => {
                        console.error('エラー:', error);
                        alert('通信に失敗しました');
                    });
            });
        }
    });
</script>
@endsection