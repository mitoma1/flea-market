<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileSetupController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\ProductController;;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\TradeController;
use App\Http\Controllers\RatingController;


// 登録・ログイン関連
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/profile/setup');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// プロフィール初期設定（メール認証後のみアクセス可能にしたい場合は 'verified' ミドルウェア追加）
Route::get('/profile/setup', [ProfileSetupController::class, 'create'])->name('profile.setup');
Route::post('/profile/setup', [ProfileSetupController::class, 'store'])->name('profile.setup.store');

// 商品関連
Route::resource('products', ProductController::class);

// マイページ
Route::get('/mypage', [ProfileController::class, 'show'])->name('mypage');
Route::get('/mypage/profile', [ProfileController::class, 'show'])->name('mypage.profile');
Route::get('/mypage/profile/edit', [ProfileController::class, 'edit'])->name('mypage.profile.edit');
Route::post('/mypage/profile/update', [ProfileController::class, 'update'])->name('mypage.profile.update');

// マイリスト・お気に入りなど
Route::get('/mylist', [ProductController::class, 'myList'])->name('mylist');

// 商品詳細・購入ページ
Route::get('/products/{id}/purchase', [ProductController::class, 'showPurchase'])->name('products.purchase.show');
Route::get('/purchase/confirm', [ProductController::class, 'purchaseConfirm'])->name('purchase.confirm');

// ✅ 購入処理は PurchaseController へ移動
Route::post('/purchase/{product}', [PurchaseController::class, 'store'])->name('purchase.store');

// お気に入り登録・コメント
Route::post('/products/{product}/favorite', [ProductController::class, 'toggleFavorite'])->name('favorites.toggle');
Route::post('/products/{product}/comments', [ProductController::class, 'commentStore'])->name('comments.store');

// 購入キャンセル
Route::post('/mypage/cancel/{product}', [ProductController::class, 'cancelPurchase'])->name('mypage.cancel');

// 住所変更
Route::get('/address/edit', [ProductController::class, 'editAddress'])->name('address.edit');
Route::post('/address/update', [ProductController::class, 'updateAddress'])->name('address.update');

// おすすめページ（仮）
Route::get('/recommend', fn() => 'おすすめ商品ページ')->name('recommend.index');
/// 取引チャット画面
Route::get('/trades/{trade}', [TradeController::class, 'show'])->name('trades.show');

// メッセージ送信
Route::post('/trades/{trade}/messages', [TradeController::class, 'storeMessage'])->name('messages.store');

// 取引完了
Route::post('/trades/{trade}/complete', [TradeController::class, 'complete'])->name('trades.complete');
// メッセージ編集画面
Route::get('/messages/{message}/edit', [TradeController::class, 'editMessage'])->name('messages.edit');

// メッセージ更新
Route::put('/messages/{message}', [TradeController::class, 'updateMessage'])->name('messages.update');

// メッセージ削除
Route::delete('/messages/{message}', [TradeController::class, 'destroyMessage'])->name('messages.destroy');
Route::get('/mypage/rating/latest', [RatingController::class, 'latest'])->name('mypage.rating.latest');
