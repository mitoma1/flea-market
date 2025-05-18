<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileSetupController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MypageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ユーザー登録
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// ログイン
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// プロフィール初期設定（認証ユーザーのみ）
Route::get('/profile/setup', [ProfileSetupController::class, 'create'])->name('profile.setup')->middleware('auth');
Route::post('/profile/setup', [ProfileSetupController::class, 'store'])->name('profile.setup.store')->middleware('auth');

// 商品出品ページ（認証ユーザーのみ）
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create')->middleware('auth');

// マイページ（認証ユーザーのみ）
Route::get('/mypage', [MypageController::class, 'index'])->name('mypage')->middleware('auth');

// 商品一覧（認証ユーザーのみ）
Route::middleware(['auth'])->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
});

// 商品詳細（IDは item_id に統一）
Route::get('/item/{item_id}', [ProductController::class, 'show'])->name('item.show');

// 商品おすすめ処理
Route::post('/products/{product}/recommend', [ProductController::class, 'recommend'])->name('recommend');

// 商品購入処理
Route::post('/products/{product}/purchase', [ProductController::class, 'purchase'])->name('purchase');

// コメント投稿
Route::post('/products/{product}/comments', [CommentController::class, 'store'])->name('comments.store');

// ログアウト
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

// おすすめページ（仮）
Route::get('/recommend', function () {
    return 'おすすめ商品ページ';
})->name('recommend');

// マイリストページ（仮）
Route::get('/mylist', function () {
    return 'マイリストページ';
})->name('mylist');
