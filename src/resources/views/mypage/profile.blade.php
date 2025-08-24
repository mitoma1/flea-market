@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- プロフィール --}}
    <div class="flex items-center justify-between border-b pb-6 mb-6">
        <div class="flex items-center space-x-6">
            <div class="w-24 h-24 rounded-full bg-gray-300 overflow-hidden">
                @if($user->profile_image && Storage::disk('public')->exists($user->profile_image))
                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像" class="w-full h-full object-cover">
                @else
                <img src="{{ asset('images/default-profile.png') }}" alt="デフォルト画像" class="w-full h-full object-cover">
                @endif
            </div>

            <div>
                <h2 class="text-xl font-bold mb-1">{{ $user->name }}</h2>
                <div class="flex items-center">
                    @php
                    $averageRatingRounded = round($averageRating);
                    @endphp
                    @for ($i = 1; $i <= 5; $i++)
                        <span class="{{ $i <= $averageRatingRounded ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                        @endfor

                        @if($ratingCount > 0)
                        <span class="ml-2 text-gray-600 text-sm">({{ $ratingCount }}件)</span>
                        @else
                        <span class="ml-2 text-gray-400 text-sm">まだ評価はありません</span>
                        @endif
                </div>
            </div>
        </div>

        <a href="{{ route('mypage.profile.edit') }}" class="border border-red-500 text-red-500 px-4 py-2 rounded hover:bg-red-50 transition">
            プロフィールを編集
        </a>
    </div>

    {{-- タブ --}}
    <div class="flex space-x-6 border-b mb-6">
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

    {{-- 出品商品 --}}
    <div id="selling-products" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
        @forelse ($sellingProducts as $product)
        <div class="block rounded overflow-hidden shadow hover:shadow-lg transition relative">
            <a href="{{ $product->trade ? route('trades.show', $product->trade->id) : '#' }}" class="{{ $product->trade ? '' : 'pointer-events-none opacity-50' }}">
                <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/default-product.png') }}" alt="商品画像" class="w-full h-48 object-cover">
                <div class="p-2">
                    <p class="font-semibold">{{ $product->name }}</p>
                    <p class="text-sm text-gray-600">¥{{ number_format($product->price) }}</p>
                </div>
            </a>
        </div>
        @empty
        <p class="text-center col-span-full text-gray-500">出品した商品はありません。</p>
        @endforelse
    </div>

    {{-- 購入商品 --}}
    <div id="purchased-products" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6 hidden">
        @forelse ($purchasedProducts as $product)
        <div class="block rounded overflow-hidden shadow hover:shadow-lg transition relative">
            <a href="{{ $product->trade ? route('trades.show', $product->trade->id) : '#' }}" class="{{ $product->trade ? '' : 'pointer-events-none opacity-50' }}">
                <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/default-product.png') }}" alt="商品画像" class="w-full h-48 object-cover">
                <div class="p-2">
                    <p class="font-semibold">{{ $product->name }}</p>
                    <p class="text-sm text-gray-600">¥{{ number_format($product->price) }}</p>
                </div>
            </a>
        </div>
        @empty
        <p class="text-center col-span-full text-gray-500">購入した商品はありません。</p>
        @endforelse
    </div>

    {{-- 取引中商品 --}}
    <div id="trading-products" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6 hidden">
        @forelse ($tradingProducts as $product)
        <div class="block rounded overflow-hidden shadow hover:shadow-lg transition relative">
            <a href="{{ $product->trade ? route('trades.show', $product->trade->id) : '#' }}" class="{{ $product->trade ? '' : 'pointer-events-none opacity-50' }}">
                <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/default-product.png') }}" alt="商品画像" class="w-full h-48 object-cover">
                @if($product->unread_messages_count > 0)
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
        <p class="text-center col-span-full text-gray-500">取引中の商品はありません。</p>
        @endforelse
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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
    });
</script>
@endsection