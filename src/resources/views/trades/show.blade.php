@extends('layouts.app')

@section('content')
<div class="flex flex-col md:flex-row min-h-screen">
    {{-- 左側のサイドバー --}}
    <div class="w-full md:w-1/4 bg-gray-200 p-4 hidden md:block">
        <h3 class="font-bold text-lg mb-4">その他の取引</h3>
        @if(isset($sidebarTrades) && $sidebarTrades->count())
        <ul class="space-y-2">
            @foreach($sidebarTrades as $t)
            <li>
                <a href="{{ route('trades.show', $t->id) }}" class="block bg-white p-3 rounded-lg shadow hover:bg-gray-100">
                    <div class="flex items-center">
                        <img src="{{ $t->product->image ? asset('images/' . basename($t->product->image)) : asset('images/default-product.png') }}"
                            alt="商品画像"
                            class="w-12 h-12 rounded mr-3 object-cover flex-shrink-0">
                        <div>
                            <p class="font-semibold text-sm truncate">{{ $t->product->name ?? '未設定商品' }}</p>
                            <p class="text-xs text-gray-600 truncate">購入者: {{ $t->buyer->name ?? '不明' }}</p>
                        </div>
                    </div>
                </a>
            </li>
            @endforeach
        </ul>
        @else
        <p class="text-sm text-gray-600">進行中の取引はありません</p>
        @endif
    </div>

    {{-- メインコンテンツ --}}
    <div class="w-full md:w-3/4 container mx-auto px-4 py-6 flex flex-col">
        {{-- 取引相手 --}}
        <div class="flex items-center mb-6">
            <div class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center mr-3 overflow-hidden flex-shrink-0">
                <img src="{{ $partner->profile_image ? asset('images/' . basename($partner->profile_image)) : asset('images/default-profile.png') }}"
                    alt="相手画像"
                    class="w-full h-full object-cover">
            </div>
            <p class="font-bold text-lg truncate">{{ $partner->name ?? '未設定ユーザー' }}さんとの取引画面</p>
        </div>

        {{-- 商品情報 --}}
        <div class="flex flex-col md:flex-row items-start md:items-center border-b pb-6 mb-6">
            <img src="{{ $product->image ? asset('images/' . basename($product->image)) : asset('images/default-product.png') }}"
                alt="商品画像"
                class="w-full md:w-32 h-32 object-cover mr-0 md:mr-4 rounded-lg shadow flex-shrink-0 mb-4 md:mb-0">
            <div class="flex-1">
                <h2 class="font-bold text-2xl mb-1 truncate">{{ $product->name ?? '未設定商品' }}</h2>
                <p class="text-gray-700 text-lg">¥{{ number_format($product->price ?? 0) }}</p>
            </div>

            <div class="mt-4 md:mt-0 md:ml-auto flex-shrink-0">
                @php
                $isBuyer = auth()->id() === $trade->buyer_id;
                $isSeller = auth()->id() === $trade->product->user_id;
                @endphp

                {{-- 取引完了ボタン --}}
                @if(($isBuyer && !$trade->buyer_completed) || ($isSeller && $trade->buyer_completed && !$trade->seller_completed))
                <button type="button" id="completeTradeButton"
                    class="bg-red-500 text-white px-5 py-3 rounded hover:bg-red-600 font-semibold">
                    取引を完了する
                </button>
                @endif
            </div>
        </div>

        {{-- メッセージ一覧 --}}
        <div class="border rounded p-4 mb-4 flex-1 overflow-y-auto h-96 flex flex-col space-y-2">
            @foreach($trade->messages as $message)
            @php
            $sender = $message->user;
            $isMe = $message->user_id === auth()->id();
            @endphp
            <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} items-start mb-2">
                @if(!$isMe)
                <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center mr-2 overflow-hidden flex-shrink-0">
                    <img src="{{ $sender->profile_image ? asset('images/' . basename($sender->profile_image)) : asset('images/default-profile.png') }}"
                        alt="アイコン"
                        class="w-full h-full object-cover">
                </div>
                @endif

                <div class="flex flex-col {{ $isMe ? 'items-end' : '' }}">
                    <p class="text-sm font-bold mb-1 truncate">{{ $sender->name }}</p>
                    <div class="p-2 rounded-lg max-w-xs bg-gray-300 text-gray-900">
                        <p class="text-sm break-words">{{ $message->body }}</p>
                        @if($message->image)
                        <div class="mt-2">
                            <img src="{{ asset('images/' . basename($message->image)) }}"
                                alt="メッセージ画像"
                                class="rounded-lg max-h-40 object-cover">
                        </div>
                        @endif
                    </div>

                    @if($isMe)
                    <div class="text-xs text-black mt-1 flex space-x-2">
                        <button class="edit-message-btn" data-id="{{ $message->id }}" data-body="{{ $message->body }}">編集</button>
                        <form action="{{ route('messages.destroy', $message->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit">削除</button>
                        </form>
                    </div>
                    @endif
                </div>

                @if($isMe)
                <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center ml-2 overflow-hidden flex-shrink-0">
                    <img src="{{ auth()->user()->profile_image ? asset('images/' . basename(auth()->user()->profile_image)) : asset('images/default-profile.png') }}"
                        alt="自分のアイコン"
                        class="w-full h-full object-cover">
                </div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- メッセージ入力フォーム --}}
        <form action="{{ route('messages.store', $trade->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row items-start md:items-center mt-auto">
            @csrf
            <div class="flex-1 w-full md:mr-2 mb-2 md:mb-0">
                <input
                    type="text"
                    name="body"
                    id="chatBody"
                    value="{{ old('body') }}"
                    placeholder="取引メッセージを記入してください"
                    class="w-full border rounded px-3 py-2 {{ $errors->has('body') ? 'border-red-500' : 'border-gray-300' }} text-black">
                <span id="errorBody" class="text-red-500 text-sm">{{ $errors->first('body') }}</span>
            </div>

            <label class="bg-gray-200 px-3 py-2 rounded cursor-pointer mr-2 mb-2 md:mb-0 hover:bg-gray-300">
                画像を追加
                <input type="file" name="image" class="hidden">
            </label>

            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">送信</button>
        </form>
    </div>
</div>

{{-- 取引完了モーダル --}}
<div id="completionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-sm w-full text-center">
        <h3 class="font-bold text-xl mb-4">取引を完了する</h3>
        <p class="text-gray-700 mb-6">今回の取引を完了し、相手を評価してください。</p>

        {{-- 星評価 --}}
        <div class="flex justify-center mb-6" id="starRating">
            @for ($i = 1; $i <= 5; $i++)
                <svg class="w-10 h-10 cursor-pointer star-icon {{ $i <= 3 ? 'text-yellow-400' : 'text-gray-300' }} fill-current"
                data-rating="{{ $i }}" viewBox="0 0 24 24">
                <path d="M12 .587l3.668 7.568 8.332 1.151-6.064 5.828 1.48 8.279-7.416-3.908-7.416 3.908 1.48-8.279-6.064-5.828 8.332-1.151z" />
                </svg>
                @endfor
        </div>

        <button id="sendReviewButton" class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 font-semibold shadow-md transition duration-300 ease-in-out">
            送信する
        </button>

        <form id="tradeCompletionForm" action="{{ route('trades.complete', $trade->id) }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="rating" id="ratingInput" value="3">
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const completeTradeButton = document.getElementById('completeTradeButton');
        const completionModal = document.getElementById('completionModal');
        const sendReviewButton = document.getElementById('sendReviewButton');
        const tradeCompletionForm = document.getElementById('tradeCompletionForm');
        const starIcons = document.querySelectorAll('#starRating .star-icon');
        const ratingInput = document.getElementById('ratingInput');

        let currentRating = 3;

        function updateStarRating(rating) {
            starIcons.forEach((star, index) => {
                star.classList.toggle('text-yellow-400', index < rating);
                star.classList.toggle('text-gray-300', index >= rating);
            });
            ratingInput.value = rating;
            currentRating = rating;
        }

        updateStarRating(currentRating);

        starIcons.forEach(star => {
            star.addEventListener('click', function() {
                updateStarRating(parseInt(this.dataset.rating));
            });
        });

        if (completeTradeButton) {
            completeTradeButton.addEventListener('click', () => {
                completionModal.classList.remove('hidden');
            });
        }

        if (sendReviewButton) {
            sendReviewButton.addEventListener('click', function() {
                tradeCompletionForm.submit();
            });
        }

        if (completionModal) {
            completionModal.addEventListener('click', function(e) {
                if (e.target === completionModal) {
                    completionModal.classList.add('hidden');
                }
            });
        }

        // メッセージ編集
        document.querySelectorAll('.edit-message-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const newBody = prompt('メッセージを編集:', this.dataset.body);
                if (newBody !== null && newBody !== '') {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/messages/${this.dataset.id}`;
                    form.innerHTML = `
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" name="_method" value="PUT">
<input type="hidden" name="body" value="${newBody}">
`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // 本文ローカルストレージ保持
        const chatBody = document.getElementById('chatBody');
        const storageKey = 'chat_body_' + "{{ $trade->id }}";
        if (localStorage.getItem(storageKey)) chatBody.value = localStorage.getItem(storageKey);
        chatBody.addEventListener('input', () => localStorage.setItem(storageKey, chatBody.value));
        chatBody.closest('form').addEventListener('submit', () => localStorage.removeItem(storageKey));
    });
</script>
@endsection