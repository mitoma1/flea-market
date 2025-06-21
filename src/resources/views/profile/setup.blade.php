@extends('layouts.app') {{-- 親レイアウトを継承 --}}

@section('title', 'プロフィール設定') {{-- ページのタイトル --}}

@section('content')
<div class="form-container max-w-lg mx-auto bg-white p-8 rounded shadow-md">
    <h2 class="text-2xl font-semibold mb-6 text-center">プロフィール設定</h2> {{-- タイトルを中央寄せ --}}

    {{-- プロフィール更新フォーム --}}
    <form action="{{ route('profile.setup.store') }}" method="POST" enctype="multipart/form-data">
        @csrf {{-- CSRF保護 --}}

        <div class="avatar-wrapper mb-6 flex flex-col items-center">
            {{-- 画像表示とファイル入力 --}}
            {{-- ユーザーが既にプロフィール画像を持っている場合はそれを表示、なければデフォルト画像 --}}
            {{-- old('avatar') は、フォーム送信失敗時に以前選択した画像があればそのURLを保持する --}}
            <img id="avatarPreview"
                src="{{ old('avatar', $user->profile_image ? Storage::url($user->profile_image) : asset('images/default-avatar.png')) }}"
                alt="プロフィール画像"
                class="avatar-preview w-32 h-32 rounded-full object-cover mb-4 border border-gray-300" />
            <input type="file"
                name="avatar" {{-- コントローラーのバリデーションに合わせて 'avatar' に変更 --}}
                accept="image/*"
                onchange="previewImage(event)"
                class="border rounded p-2 w-full" />
            @error('avatar')
            <div class="text-red-600 mt-1 text-sm">{{ $message }}</div> {{-- エラーメッセージの文字サイズを小さく --}}
            @enderror
        </div>

        {{-- ユーザー名 --}}
        <label for="username" class="block font-medium mb-1">ユーザー名</label>
        <input
            type="text"
            name="username" {{-- あなたのHTMLに合わせて 'username' を使用 --}}
            id="username"
            value="{{ old('username', $user->name) }}" {{-- $user->name をデフォルト値に設定 --}}
            class="w-full border border-gray-300 rounded px-3 py-2 mb-2" />
        @error('username')
        <div class="text-red-600 mb-4 text-sm">{{ $message }}</div> {{-- エラーメッセージの文字サイズを小さく --}}
        @enderror

        {{-- 郵便番号 --}}
        <label for="postal_code" class="block font-medium mb-1">郵便番号</label>
        <input
            type="text"
            name="postal_code" {{-- あなたのHTMLに合わせて 'postal_code' を使用 --}}
            id="postal_code"
            value="{{ old('postal_code', $user->postcode) }}" {{-- $user->postcode をデフォルト値に設定 --}}
            class="w-full border border-gray-300 rounded px-3 py-2 mb-2"
            placeholder="123-4567" />
        @error('postal_code')
        <div class="text-red-600 mb-4 text-sm">{{ $message }}</div> {{-- エラーメッセージの文字サイズを小さく --}}
        @enderror

        {{-- 住所 --}}
        <label for="address" class="block font-medium mb-1">住所</label>
        <input
            type="text"
            name="address" {{-- あなたのHTMLに合わせて 'address' を使用 --}}
            id="address"
            value="{{ old('address', $user->address) }}" {{-- $user->address をデフォルト値に設定 --}}
            class="w-full border border-gray-300 rounded px-3 py-2 mb-2" />
        @error('address')
        <div class="text-red-600 mb-4 text-sm">{{ $message }}</div> {{-- エラーメッセージの文字サイズを小さく --}}
        @enderror

        {{-- 建物名 --}}
        <label for="building" class="block font-medium mb-1">建物名 (任意)</label> {{-- (任意)を追加 --}}
        <input
            type="text"
            name="building" {{-- あなたのHTMLに合わせて 'building' を使用 --}}
            id="building"
            value="{{ old('building', $user->building) }}" {{-- $user->building をデフォルト値に設定 --}}
            class="w-full border border-gray-300 rounded px-3 py-2 mb-4" />
        @error('building')
        <div class="text-red-600 mb-4 text-sm">{{ $message }}</div> {{-- エラーメッセージの文字サイズを小さく --}}
        @enderror

        {{-- 更新ボタン --}}
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