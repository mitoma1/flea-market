<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileSetupController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    // プロフィール初期設定
    Route::get('/profile/setup', [ProfileSetupController::class, 'create'])->name('profile.setup');
    Route::post('/profile/setup', [ProfileSetupController::class, 'store'])->name('profile.setup.store');

    // 商品一覧（明示的なルートは削除）
    Route::resource('products', ProductController::class);

    // マイページトップ
    Route::get('/mypage', [ProfileController::class, 'show'])->name('mypage');

    // プロフィール表示（詳細表示）
    Route::get('/mypage/profile', [ProfileController::class, 'show'])->name('mypage.profile');

    // プロフィール編集フォーム
    Route::get('/mypage/profile/edit', [ProfileController::class, 'edit'])->name('mypage.profile.edit');

    // プロフィール更新処理
    Route::post('/mypage/profile/update', [ProfileController::class, 'update'])->name('mypage.profile.update');

    // マイリスト（お気に入りや出品など）
    Route::get('/mylist', [ProductController::class, 'myList'])->name('mylist');
    // 商品詳細・購入・おすすめ・コメント
    Route::get('/products/{id}/purchase', [ProductController::class, 'showPurchase'])->name('products.purchase.show');
    Route::post('/purchase', [ProductController::class, 'purchaseStore'])->name('purchase');
    Route::post('/products/{product}/favorite', [ProductController::class, 'toggleFavorite'])->name('favorites.toggle');
    Route::post('/products/{product}/comments', [ProductController::class, 'commentStore'])->name('comments.store');
    Route::get('/purchase/confirm', [ProductController::class, 'purchaseConfirm'])->name('purchase.confirm');
    // 住所変更
    Route::get('/address/edit', [ProductController::class, 'editAddress'])->name('address.edit');
    Route::post('/address/update', [ProductController::class, 'updateAddress'])->name('address.update');
});

Route::get('/recommend', fn() => 'おすすめ商品ページ')->name('recommend.index');
