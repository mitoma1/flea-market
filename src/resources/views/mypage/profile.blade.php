@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- 上部プロフィールエリア --}}
    <div class="flex flex-col items-center mb-8">
        {{-- ユーザーアイコン --}}
        <div class="w-24 h-24 rounded-full bg-gray-300 mb-4 overflow-hidden">
            @if($user->profile_image && Storage::disk('public')->exists($user->profile_image))
            <img src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像" class="w-full h-full object-cover">
            @else
            <img src="{{ asset('images/default-profile.png') }}" alt="デフォルト画像" class="w-full h-full object-cover">
            @endif
        </div>

        {{-- ユーザー名 --}}
        <h2 class="text-xl font-bold mb-2">{{ $user->name }}</h2>

        {{-- 編集ボタン --}}
        <a href="{{ route('mypage.profile.edit') }}" class="border border-red-500 text-red-500 px-4 py-1 rounded hover:bg-red-100">
            プロフィールを編集
        </a>
    </div>

    {{-- タブ切り替えリンク（UIそのまま） --}}
    <div class="flex justify-center mb-4 space-x-4">
        <button id="selling-tab" class="text-red-500 font-bold border-b-2 border-red-500 px-4 py-2">
            出品した商品
        </button>
        <button id="purchased-tab" class="text-gray-600 px-4 py-2 hover:text-black border-b-2 border-transparent">
            購入した商品
        </button>
    </div>

    {{-- 出品した商品 --}}
    <div id="selling-products" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        @forelse ($sellingProducts as $product)
        <div class="border rounded-lg overflow-hidden shadow hover:shadow-lg transition">
            <img src="{{ asset('storage/' . $product->image) }}" alt="商品画像" class="w-full h-48 object-cover">
            <div class="p-2">
                <p class="font-semibold">{{ $product->name }}</p>
                <p class="text-sm text-gray-600">¥{{ number_format($product->price) }}</p>
            </div>
        </div>
        @empty
        <p class="text-center col-span-full text-gray-500">出品した商品はありません。</p>
        @endforelse
    </div>

    {{-- 購入した商品 --}}
    <div id="purchased-products" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 hidden">
        @forelse ($purchasedProducts as $product)
        <div class="border rounded-lg overflow-hidden shadow hover:shadow-lg transition">
            <img src="{{ asset('storage/' . $product->image) }}" alt="商品画像" class="w-full h-48 object-cover">
            <div class="p-2">
                <p class="font-semibold">{{ $product->name }}</p>
                <p class="text-sm text-gray-600">¥{{ number_format($product->price) }}</p>
            </div>
        </div>
        @empty
        <p class="text-center col-span-full text-gray-500">購入した商品はありません。</p>
        @endforelse
    </div>
</div>

{{-- JavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sellingTab = document.getElementById('selling-tab');
        const purchasedTab = document.getElementById('purchased-tab');
        const sellingProducts = document.getElementById('selling-products');
        const purchasedProducts = document.getElementById('purchased-products');

        sellingTab.addEventListener('click', function() {
            sellingProducts.classList.remove('hidden');
            purchasedProducts.classList.add('hidden');

            sellingTab.classList.add('text-red-500', 'font-bold', 'border-red-500');
            sellingTab.classList.remove('text-gray-600', 'border-transparent');

            purchasedTab.classList.remove('text-red-500', 'font-bold', 'border-red-500');
            purchasedTab.classList.add('text-gray-600', 'border-transparent');
        });

        purchasedTab.addEventListener('click', function() {
            purchasedProducts.classList.remove('hidden');
            sellingProducts.classList.add('hidden');

            purchasedTab.classList.add('text-red-500', 'font-bold', 'border-red-500');
            purchasedTab.classList.remove('text-gray-600', 'border-transparent');

            sellingTab.classList.remove('text-red-500', 'font-bold', 'border-red-500');
            sellingTab.classList.add('text-gray-600', 'border-transparent');
        });
    });
</script>
@endsection