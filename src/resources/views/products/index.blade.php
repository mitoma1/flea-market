@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4" x-data="{ tab: 'recommended' }">
    <h1 class="text-2xl font-bold mb-6">商品一覧</h1>

    <!-- タブ切り替え -->
    <div class="flex space-x-6 border-b-2 mb-6">
        <button
            class="pb-2 border-b-2"
            :class="tab === 'recommended' ? 'border-black font-bold' : 'text-gray-400'"
            @click="tab = 'recommended'">
            おすすめ
        </button>
        <button
            class="pb-2 border-b-2"
            :class="tab === 'favorites' ? 'border-black font-bold text-red-500' : 'text-gray-400'"
            @click="tab = 'favorites'">
            マイリスト
        </button>
    </div>

    <!-- おすすめ一覧 -->
    <div x-show="tab === 'recommended'" class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @forelse ($products as $product)
        <div class="relative bg-white rounded shadow p-4 overflow-hidden">
            <a href="{{ route('products.show', $product->id) }}" class="block">
                <div class="relative">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                        class="w-full h-40 object-cover rounded mb-2">
                    @if ($product->isSold())
                    <div class="absolute inset-0 bg-black bg-opacity-60 flex justify-center items-center z-20">
                        <span class="text-white text-4xl font-extrabold tracking-widest select-none">SOLD</span>
                    </div>
                    @endif
                </div>
                <p class="text-lg font-semibold">{{ $product->name }}</p>
                <p class="text-gray-600">{{ number_format($product->price) }} 円</p>
            </a>
        </div>
        @empty
        <p>商品が見つかりませんでした。</p>
        @endforelse
    </div>

    <!-- マイリスト -->
    <div x-show="tab === 'favorites'" class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @auth
        @forelse ($favorites as $product)
        <div class="relative bg-white rounded shadow p-4 overflow-hidden">
            <a href="{{ route('products.show', $product->id) }}" class="block">
                <div class="relative">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                        class="w-full h-40 object-cover rounded mb-2">
                    @if ($product->isSold())
                    <div class="absolute inset-0 bg-black bg-opacity-60 flex justify-center items-center z-20">
                        <span class="text-white text-4xl font-extrabold tracking-widest select-none">SOLD</span>
                    </div>
                    @endif
                </div>
                <p class="text-lg font-semibold">{{ $product->name }}</p>
                <p class="text-gray-600">{{ number_format($product->price) }} 円</p>
            </a>
        </div>
        @empty
        <p>マイリストに商品がありません。</p>
        @endforelse
        @else
        <p>マイリストを表示するにはログインしてください。</p>
        @endauth
    </div>

    <!-- ページネーション（おすすめのみ） -->
    <div class="mt-6" x-show="tab === 'recommended'">
        {{ $products->links() }}
    </div>
</div>
@endsection