@extends('layouts.app')

@section('content')
<div class="max-w-screen-xl mx-auto px-4 py-6">

    {{-- プロフィール --}}
    <div class="flex flex-col md:flex-row items-center justify-between border-b pb-6 mb-6">
        <div class="flex items-center space-x-6">
            {{-- プロフィール画像 --}}
            <div class="w-24 h-24 rounded-full bg-gray-300 overflow-hidden flex-shrink-0">
                <img src="{{ $user->profile_image ? asset('images/' . basename($user->profile_image)) : asset('images/default-profile.png') }}"
                    alt="プロフィール画像" class="w-full h-full object-cover">
            </div>

            <div class="mt-4 md:mt-0">
                <h2 class="text-xl font-bold mb-1">{{ $user->name }}</h2>

                {{-- 総合評価（件数なし） --}}
                <div id="user-stars" class="flex items-center flex-wrap mb-1">
                    @php
                    $stars = $user->averageRating();
                    @endphp
                    @for ($i = 1; $i <= 5; $i++)
                        <span class="star {{ $i <= $stars ? 'text-yellow-400' : 'text-gray-300' }}" data-value="{{ $i }}">★</span>
                        @endfor
                </div>
            </div>
        </div>

        {{-- プロフィール編集 --}}
        <a href="{{ route('mypage.profile.edit') }}" class="mt-4 md:mt-0 border border-red-500 text-red-500 px-4 py-2 rounded hover:bg-red-50 transition">
            プロフィールを編集
        </a>
    </div>

    {{-- タブ --}}
    <div class="flex flex-wrap space-x-4 border-b mb-6">
        <button id="selling-tab" class="pb-2 border-b-2 border-red-500 text-red-500 font-bold">出品商品</button>
        <button id="purchased-tab" class="pb-2 border-b-2 border-transparent text-gray-600 hover:text-black">購入商品</button>
        <button id="trading-tab" class="pb-2 border-b-2 border-transparent text-gray-600 hover:text-black flex items-center">
            取引中商品
            @if($tradingProducts->sum('unread_messages_count') > 0)
            <span class="ml-1 text-xs bg-red-500 text-white px-2 py-0.5 rounded-full">
                {{ $tradingProducts->sum('unread_messages_count') }}
            </span>
            @endif
        </button>
    </div>

    {{-- 商品表示 --}}
    @foreach (['selling' => $sellingProducts, 'purchased' => $purchasedProducts, 'trading' => $tradingProducts] as $key => $products)
    <div id="{{ $key }}-products" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6 {{ $key !== 'selling' ? 'hidden' : '' }}">
        @forelse ($products as $product)
        <div class="block rounded overflow-hidden shadow hover:shadow-lg transition relative">
            <a href="{{ $product->trade ? route('trades.show', $product->trade->id) : '#' }}" class="{{ $product->trade ? '' : 'pointer-events-none opacity-50' }}">
                <img src="{{ $product->image ? asset('images/' . basename($product->image)) : asset('images/default-product.png') }}"
                    alt="商品画像" class="w-full h-48 object-cover">

                @if($key === 'trading' && $product->unread_messages_count > 0)
                <span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">
                    {{ $product->unread_messages_count }}
                </span>
                @endif

                <div class="p-2">
                    <p class="font-semibold">{{ $product->name }}</p>
                    <p class="text-sm text-gray-600">¥{{ number_format($product->price) }}</p>
                </div>
            </a>
        </div>
        @empty
        <p class="text-center col-span-full text-gray-500">
            {{ $key === 'selling' ? '出品した商品はありません' : ($key === 'purchased' ? '購入した商品はありません' : '取引中の商品はありません') }}。
        </p>
        @endforelse
    </div>
    @endforeach

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // タブ切替
        const tabs = {
            selling: document.getElementById('selling-products'),
            purchased: document.getElementById('purchased-products'),
            trading: document.getElementById('trading-products')
        };
        const tabButtons = {
            selling: document.getElementById('selling-tab'),
            purchased: document.getElementById('purchased-tab'),
            trading: document.getElementById('trading-tab')
        };

        function activate(tab) {
            Object.values(tabs).forEach(el => el.classList.add('hidden'));
            tabs[tab].classList.remove('hidden');

            Object.entries(tabButtons).forEach(([key, btn]) => {
                if (key === tab) {
                    btn.classList.add('border-red-500', 'text-red-500', 'font-bold');
                    btn.classList.remove('border-transparent', 'text-gray-600');
                } else {
                    btn.classList.remove('border-red-500', 'text-red-500', 'font-bold');
                    btn.classList.add('border-transparent', 'text-gray-600');
                }
            });
        }

        activate('selling');

        tabButtons.selling.addEventListener('click', () => activate('selling'));
        tabButtons.purchased.addEventListener('click', () => activate('purchased'));
        tabButtons.trading.addEventListener('click', () => activate('trading'));

        // 星のリアルタイム更新（例：サーバーから最新評価を取得）
        async function fetchLatestRating() {
            try {
                const response = await fetch("{{ route('mypage.rating.latest', $user->id) }}");
                const data = await response.json();
                const starElements = document.querySelectorAll('#user-stars .star');
                starElements.forEach((el, index) => {
                    if (index < data.averageRating) {
                        el.classList.add('text-yellow-400');
                        el.classList.remove('text-gray-300');
                    } else {
                        el.classList.remove('text-yellow-400');
                        el.classList.add('text-gray-300');
                    }
                });
            } catch (e) {
                console.error('最新評価の取得に失敗しました', e);
            }
        }

        fetchLatestRating();
    });
</script>
@endsection