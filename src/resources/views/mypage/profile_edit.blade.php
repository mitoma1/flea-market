@extends('layouts.app')

@section('content')
<div class="form-container max-w-lg mx-auto bg-white p-8 rounded shadow-md">
    <h2 class="text-2xl font-semibold mb-6">プロフィール設定</h2>

    <form action="{{ route('profile.setup.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="avatar-wrapper mb-6 flex flex-col items-center">
            <img id="avatarPreview" src="{{ asset('images/default-avatar.png') }}" alt="プロフィール画像" class="avatar-preview w-32 h-32 rounded-full object-cover mb-4 border border-gray-300" />
            <input type="file" name="avatar" accept="image/*" onchange="previewImage(event)" class="border rounded p-2 w-full" />
            @error('avatar')
            <div class="text-red-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <label for="username" class="block font-medium mb-1">ユーザー名</label>
        <input
            type="text"
            name="username"
            id="username"
            value="{{ old('username') }}"
            class="w-full border border-gray-300 rounded px-3 py-2 mb-2" />
        @error('username')
        <div class="text-red-600 mb-4">{{ $message }}</div>
        @enderror

        <label for="postal_code" class="block font-medium mb-1">郵便番号</label>
        <input
            type="text"
            name="postal_code"
            id="postal_code"
            value="{{ old('postal_code') }}"
            class="w-full border border-gray-300 rounded px-3 py-2 mb-2"
            placeholder="123-4567" />
        @error('postal_code')
        <div class="text-red-600 mb-4">{{ $message }}</div>
        @enderror

        <label for="address" class="block font-medium mb-1">住所</label>
        <input
            type="text"
            name="address"
            id="address"
            value="{{ old('address') }}"
            class="w-full border border-gray-300 rounded px-3 py-2 mb-2" />
        @error('address')
        <div class="text-red-600 mb-4">{{ $message }}</div>
        @enderror

        <label for="building" class="block font-medium mb-1">建物名</label>
        <input
            type="text"
            name="building"
            id="building"
            value="{{ old('building') }}"
            class="w-full border border-gray-300 rounded px-3 py-2 mb-4" />
        @error('building')
        <div class="text-red-600 mb-4">{{ $message }}</div>
        @enderror

        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded w-full">
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