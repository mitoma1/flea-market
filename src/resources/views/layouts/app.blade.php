<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name', 'COACHTECH') }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- オリジナルCSS -->
    <link rel="stylesheet" href="{{ asset('css/profile_create.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/address.css') }}" />
    @stack('styles')

    <!-- Alpine.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>

<body class="bg-gray-100 text-gray-800">
    <header class="bg-black text-white">
        <div class="container mx-auto flex items-center justify-between px-4 py-3">
            <div class="text-xl font-bold flex items-center space-x-2">
                <img src="{{ asset('images/logo.svg') }}" alt="ロゴ" class="logo" />
            </div>

            <form action="#" method="GET" class="flex-1 mx-4 max-w-md">
                <input
                    type="text"
                    name="search"
                    placeholder="なにをお探しですか？"
                    class="w-full px-3 py-2 rounded border text-black" />
            </form>

            <div class="space-x-4">
                <!-- ログアウト用フォーム（非表示） -->
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>

                <!-- ログアウトリンク（クリックでPOST送信） -->
                <a
                    href="#"
                    class="hover:underline"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    ログアウト
                </a>

                <a href="{{ route('mypage.profile') }}" class="hover:underline">マイページ</a>
                <a href="{{ route('products.create') }}" class="hover:underline">出品</a>
            </div>
        </div>
    </header>

    <main class="py-6">
        @yield('content')
    </main>
</body>

</html>