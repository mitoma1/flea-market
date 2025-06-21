@extends('layouts.app')

@section('content')
<div class="purchase-container">
    <h2 class="page-title">商品購入画面</h2>

    <div class="product-box">
        <div class="product-info">
            <div class="product-image">
                @if (!empty($product) && $product->image_path)
                <img src="{{ asset('storage/' . $product->image_path) }}" alt="商品画像" style="max-width: 200px;">
                @elseif(!empty($product) && $product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-40 object-cover rounded mb-2">
                @endif
            </div>
            <div class="product-details">
                <h3 class="product-name">{{ $product->name ?? '' }}</h3>
                <p class="product-price">¥{{ number_format($product->price ?? 0) }}</p>
            </div>
        </div>

        <div class="purchase-summary">
            <table class="summary-table">
                <tr>
                    <td>商品代金</td>
                    <td>¥{{ number_format($product->price ?? 0) }}</td>
                </tr>
                <tr>
                    <td>支払い方法</td>
                    <td id="selected-payment-method">
                        @if(old('payment_method') === 'convenience')
                        コンビニ払い
                        @elseif(old('payment_method') === 'credit')
                        クレジットカード
                        @else
                        －
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <form method="POST" action="{{ route('purchase.store', ['product' => $product->id]) }}">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id ?? '' }}">

        <div class="form-section">
            <label for="payment">支払い方法</label>
            <select id="payment" name="payment_method" class="form-select" required>
                <option value="">選択してください</option>
                <option value="convenience" {{ old('payment_method') === 'convenience' ? 'selected' : '' }}>コンビニ払い</option>
                <option value="credit" {{ old('payment_method') === 'credit' ? 'selected' : '' }}>クレジットカード</option>
            </select>
            @error('payment_method')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="delivery-section">
            <h4>配送先</h4>
            @php
            $postalCode = $address['postal_code'] ?? '';
            $addr = $address['address'] ?? 'ここには住所と建物が入ります';
            $building = $address['building'] ?? '';
            $fullAddress = trim("〒{$postalCode} {$addr} {$building}");
            @endphp
            <p>
                {{ $fullAddress }}
                <br>
                <a href="{{ route('address.edit') }}" class="change-link">変更する</a>
            </p>

            @if(session('address_changed_message'))
            <p class="text-green-600 text-sm mt-2">
                {{ session('address_changed_message') }}
            </p>
            @endif

            {{-- hidden で住所情報を送信 --}}
            <input type="hidden" name="shipping_postal_code" value="{{ $postalCode }}">
            <input type="hidden" name="shipping_address" value="{{ $addr }}">
            <input type="hidden" name="shipping_building" value="{{ $building }}">

            @error('shipping_address')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="button-section">
            <button type="submit" class="purchase-button">購入する</button>
        </div>
    </form>
</div>

{{-- JavaScriptで支払い方法を反映 --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentSelect = document.getElementById('payment');
        const paymentDisplay = document.getElementById('selected-payment-method');

        const displayMap = {
            'convenience': 'コンビニ払い',
            'credit': 'クレジットカード'
        };

        paymentSelect.addEventListener('change', function() {
            const selectedValue = this.value;
            paymentDisplay.textContent = displayMap[selectedValue] || '－';
        });
    });
</script>
@endsection