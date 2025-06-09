@extends('layouts.app')

@section('title', '商品出品')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/product_create.css') }}">
@endpush

@section('content')

<h1 class="page-title">商品出品フォーム</h1>

<form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="product-form">
    @csrf

    <div class="form-group">
        <label for="image">商品画像</label>
        <input type="file" name="image" id="image" onchange="previewImage(event)">
        @error('image')
        <div class="error-message">{{ $message }}</div>
        @enderror

        <!-- 画像プレビュー表示 -->
        <div id="preview" style="margin-top: 10px;">
            <img id="preview-img" src="" alt="プレビュー画像" style="max-width: 300px; display: none;">
        </div>
    </div>

    <div class="form-group">
        <label>カテゴリ（複数選択可）</label>
        <div class="category-buttons">
            @foreach ($categories as $category)
            <label class="category-button">
                <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                    {{ is_array(old('category_ids')) && in_array($category->id, old('category_ids')) ? 'checked' : '' }}>
                {{ $category->name }}
            </label>
            @endforeach
        </div>
        @error('category_ids')
        <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="condition">商品の状態</label>
        <select name="condition" id="condition">
            <option value="">選択してください</option>
            @foreach (['新品', '未使用に近い', '目立った傷や汚れなし', 'やや傷や汚れあり', '全体的に状態が悪い'] as $cond)
            <option value="{{ $cond }}" {{ old('condition') == $cond ? 'selected' : '' }}>
                {{ $cond }}
            </option>
            @endforeach
        </select>
        @error('condition')
        <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="name">商品名</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}">
        @error('name')
        <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="brand">ブランド名</label>
        <input type="text" name="brand" id="brand" value="{{ old('brand') }}">
        @error('brand')
        <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="description">商品の説明</label>
        <textarea name="description" id="description" rows="4">{{ old('description') }}</textarea>
        @error('description')
        <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="price">価格（円）</label>
        <input type="number" name="price" id="price" value="{{ old('price') }}" min="0">
        @error('price')
        <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="submit-button">出品する</button>
</form>

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        const previewImg = document.getElementById('preview-img');
        if (file) {
            previewImg.src = URL.createObjectURL(file);
            previewImg.style.display = 'block';
        } else {
            previewImg.src = '';
            previewImg.style.display = 'none';
        }
    }
</script>

@endsection