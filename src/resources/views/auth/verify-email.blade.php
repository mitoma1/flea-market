@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/verify.css') }}">

<div class="verify-container">
    <h1 class="verify-message">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </h1>


    {{-- プロフィール設定ページへのリンクボタン --}}
    <div style="margin-top: 20px;">
        <a href="{{ route('profile.setup') }}" class="verify-button">
            認証はこちらから
        </a>
    </div>

    {{-- 再送リンク（必要であれば残す） --}}
    <p class="resend-link">
        <a href="{{ route('verification.send') }}">認証メールを再送する</a>
    </p>
</div>
@endsection