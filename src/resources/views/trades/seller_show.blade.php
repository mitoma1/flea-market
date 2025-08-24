@extends('layouts.app')

@section('content')
<div class="flex"> {{-- 全体を囲むflexコンテナを追加 --}}
    {{-- 左側のグレーの囲い (その他の取引) --}}
    <div class="w-1/4 bg-gray-200 p-4 min-h-screen hidden md:block"> {{-- PCビューでのみ表示 --}}
        <h3 class="font-bold text-lg mb-4">その他の取引</h3>
        {{-- ここにその他の取引のリストなどを追加できます --}}
        <div class="bg-gray-300 h-16 rounded-lg mb-2"></div>
        <div class="bg-gray-300 h-16 rounded-lg mb-2"></div>
        <div class="bg-gray-300 h-16 rounded-lg"></div>
    </div>

    {{-- メインコンテンツ --}}
    <div class="w-full md:w-3/4 container mx-auto px-4 py-6">

        {{-- 取引相手 --}}
        <div class="flex items-center mb-6">
            <img src="{{ asset('storage/' . $partner->profile_image) }}" alt="相手画像" class="w-16 h-16 rounded-full mr-3">
            <p class="font-bold text-lg">{{ $partner->name }}さんとの取引画面 (出品者)</p> {{-- ここが出品者側であることを示します --}}
        </div>

        {{-- 商品情報 --}}
        <div class="flex items-center border-b pb-6 mb-6">
            <img src="{{ asset('storage/' . $product->image) }}" alt="商品画像" class="w-32 h-32 object-cover mr-4 rounded-lg shadow">
            <div>
                <h2 class="font-bold text-2xl mb-1">{{ $product->name }}</h2>
                <p class="text-gray-700 text-lg">¥{{ number_format($product->price) }}</p>
            </div>
            <div class="ml-auto">
                {{-- 取引完了ボタン (モーダルを開くトリガー) --}}
                <button type="button" id="completeTradeButton" class="bg-red-500 text-white px-5 py-3 rounded hover:bg-red-600 font-semibold">
                    取引を完了する
                </button>
            </div>
        </div>

        {{-- メッセージ一覧 --}}
        <div class="border rounded p-4 mb-4 h-96 overflow-y-auto">
            @foreach ($messages as $message)
            <div class="flex {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }} mb-2">
                <div class="px-3 py-2 rounded {{ $message->user_id === auth()->id() ? 'bg-red-100' : 'bg-gray-200' }}">
                    <p>{{ $message->content }}</p>
                    @if($message->image)
                    <img src="{{ asset('storage/' . $message->image) }}" alt="添付画像" class="w-32 h-32 object-cover mt-1">
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- メッセージ入力 --}}
        <form action="{{ route('messages.store', $product->id) }}" method="POST" enctype="multipart/form-data" class="flex items-center">
            @csrf
            <input type="text" name="content" placeholder="取引メッセージを記入してください" class="flex-1 border rounded px-3 py-2 mr-2">
            <input type="file" name="image" class="mr-2">
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                送信
            </button>
        </form>
    </div>
</div>

{{-- 取引完了モーダル --}}
<div id="completionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-sm w-full text-center">
        <h3 class="font-bold text-xl mb-4">取引が完了しました。</h3>
        <p class="text-gray-700 mb-6">今回の取引相手はどうでしたか？</p>

        {{-- 星評価 --}}
        <div class="flex justify-center mb-6" id="starRating">
            @for ($i = 1; $i <= 5; $i++)
                <svg class="w-10 h-10 cursor-pointer star-icon {{ $i <= 3 ? 'text-yellow-400' : 'text-gray-300' }} fill-current" data-rating="{{ $i }}" viewBox="0 0 24 24">
                <path d="M12 .587l3.668 7.568 8.332 1.151-6.064 5.828 1.48 8.279-7.416-3.908-7.416 3.908 1.48-8.279-6.064-5.828 8.332-1.151z" /></svg>
                @endfor
        </div>

        <button id="sendReviewButton" class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 font-semibold shadow-md transition duration-300 ease-in-out">
            送信する
        </button>
        <form id="tradeCompletionForm" action="{{ route('trades.complete', $product->id) }}" method="POST" class="hidden">
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
                if (index < rating) {
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-300');
                }
            });
            ratingInput.value = rating;
            currentRating = rating;
        }

        updateStarRating(currentRating);

        starIcons.forEach(star => {
            star.addEventListener('click', function() {
                const selectedRating = parseInt(this.dataset.rating);
                updateStarRating(selectedRating);
            });
        });

        if (completeTradeButton) {
            completeTradeButton.addEventListener('click', function() {
                completionModal.classList.remove('hidden');
            });
        }

        if (sendReviewButton && tradeCompletionForm) {
            sendReviewButton.addEventListener('click', function() {
                completionModal.classList.add('hidden');
                tradeCompletionForm.submit();
            });
        }

        if (completionModal) {
            completionModal.addEventListener('click', function(event) {
                if (event.target === completionModal) {
                    completionModal.classList.add('hidden');
                }
            });
        }
    });
</script>
@endsection