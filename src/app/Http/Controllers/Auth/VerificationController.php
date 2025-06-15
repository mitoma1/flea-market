<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails; // ✅ メール認証ロジックを提供するトレイト
use Illuminate\Http\Request; // ✅ 必要に応じてRequestクラスをインポート

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that registered with the application. Emails may also be re-sent
    | if the user didn't receive the original email notification.
    |
    */

    use VerifiesEmails; // ✅ VerifiesEmailsトレイトを使用

    /**
     * 認証後にユーザーがリダイレクトされる場所。
     *
     * @var string
     */
    protected $redirectTo = '/profile/setup'; // ✅ ここをあなたのプロフィール設定画面のURLに変更

    /**
     * 新しいコントローラーインスタンスを作成します。
     *
     * @return void
     */
    public function __construct()
    {
        // ✅ 認証済みユーザーのみアクセス可能にするミドルウェア
        $this->middleware('auth');

        // ✅ 認証URLが署名済みであることを確認するミドルウェア（セキュリティ強化）
        $this->middleware('signed')->only('verify');

        // ✅ メール再送リクエストを制限するミドルウェア
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }
}
