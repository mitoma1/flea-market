@extends('layouts.app')

@section('title', 'プロフィール設定')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-center">プロフィール設定</h2>

    <form action="{{ route('profile.setup.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="flex flex-col items-center mb-6">
            <img id="avatarPreview"
                src="{{ asset('images/default-avatar.png') }}"
                class="w-32 h-32 rounded-full object-cover border mb-3"
                alt="プロフィール画像">
            <input type="file"
                name="avatar"
                accept="image/*"
                onchange="previewImage(event)"
                class="text-sm border rounded p-2 w-full">
            @error('avatar')
            <div class="text-red-600 mt-1 text-sm">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="username" class="block mb-1 font-medium">ユーザー名</label>
            <input type="text"
                name="username"
                value="{{ old('username') }}"
                class="w-full border rounded px-3 py-2">
            @error('username')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="postal_code" class="block mb-1 font-medium">郵便番号</label>
            <input type="text"
                name="postal_code"
                value="{{ old('postal_code') }}"
                class="w-full border rounded px-3 py-2">
            @error('postal_code')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="address" class="block mb-1 font-medium">住所</label>
            <input type="text"
                name="address"
                value="{{ old('address') }}"
                class="w-full border rounded px-3 py-2">
            @error('address')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-6">
            <label for="building" class="block mb-1 font-medium">建物名</label>
            <input type="text"
                name="building"
                value="{{ old('building') }}"
                class="w-full border rounded px-3 py-2">
            @error('building')
            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit"
            class="w-full bg-black hover:bg-gray-800 text-white font-semibold py-3 rounded">
            更新する
        </button>
    </form>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            document.getElementById('avatarPreview').src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection