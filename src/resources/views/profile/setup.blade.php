<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>プロフィール設定</title>
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
</head>

<body>
    <div class="form-container">
        <h2>プロフィール設定</h2>

        <form action="{{ route('profile.setup.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="avatar-wrapper">
                <img id="avatarPreview" src="{{ asset('images/default-avatar.png') }}" class="avatar-preview" alt="プロフィール画像">
                <input type="file" name="avatar" accept="image/*" onchange="previewImage(event)">
            </div>

            <label for="username">ユーザー名</label>
            <input type="text" name="username" value="{{ old('username') }}">
            @error('username')
            <div class="error">{{ $message }}</div>
            @enderror

            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code') }}">
            @error('postal_code')
            <div class="error">{{ $message }}</div>
            @enderror

            <label for="address">住所</label>
            <input type="text" name="address" value="{{ old('address') }}">
            @error('address')
            <div class="error">{{ $message }}</div>
            @enderror

            <label for="building">建物名</label>
            <input type="text" name="building" value="{{ old('building') }}">
            @error('building')
            <div class="error">{{ $message }}</div>
            @enderror

            <button type="submit" class="submit-btn">更新する</button>
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
</body>

</html>