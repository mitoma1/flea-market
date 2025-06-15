<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExhibitionRequest;
use App\Http\Requests\CommentRequest;
use App\Models\Product;
use App\Models\Category;
use App\Models\Comment;

class ProductController extends Controller
{
    // 商品一覧表示（ページネーションあり）
    public function index()
    {
        $products = Product::paginate(8);
        $favorites = auth()->check()
            ? auth()->user()->favoriteProducts()->get()
            : collect();

        return view('products.index', compact('products', 'favorites'));
    }

    // 出品フォーム表示
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    // 出品保存
    public function store(ExhibitionRequest $request)
    {
        $validated = $request->validated();

        $imageName = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/images');
            $imageName = basename($path);
        }

        // 商品登録（category_idは多対多なので除外）
        $product = Product::create([
            'user_id'     => auth()->id(),
            'image'       => $imageName,
            'condition'   => $validated['condition'],
            'name'        => $validated['name'],
            'brand'       => $validated['brand'],
            'description' => $validated['description'],
            'price'       => $validated['price'],
        ]);

        // カテゴリー多対多の中間テーブルに保存（category_idは配列想定）
        if (isset($validated['category_id'])) {
            // category_idが単数なら配列に変換するか sync でそのまま
            $categories = is_array($validated['category_id']) ? $validated['category_id'] : [$validated['category_id']];
            $product->categories()->sync($categories);
        }

        return redirect()->route('products.index')->with('status', '商品を出品しました！');
    }

    // 商品詳細表示（categoriesを eager load）
    public function show($id)
    {
        $product = Product::with('categories')->findOrFail($id);
        return view('products.show', compact('product'));
    }

    // 商品購入画面表示（住所セッション対応）
    public function showPurchase($id)
    {
        $product = Product::findOrFail($id);

        if ($product->buyer_id !== null) {
            return redirect()->back()->withErrors(['status' => 'この商品はすでに購入されています']);
        }

        $address = session('address') ?? [
            'postal_code' => 'XXX-YYYY',
            'address'     => 'ここには住所と建物が入ります',
            'building'    => '',
            'name'        => '',
        ];

        session(['product_id' => $id]);

        return view('purchase', compact('product', 'address'));
    }

    // 購入処理
    public function purchaseStore(Request $request)
    {
        $request->validate([
            'product_id'     => 'required|exists:products,id',
            'payment_method' => 'required|in:convenience,credit',
        ], [
            'product_id.required'     => '商品が選択されていません',
            'payment_method.required' => '支払い方法を選択してください',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->buyer_id !== null) {
            return back()->withErrors(['status' => 'この商品はすでに購入されています']);
        }

        // 購入者情報を更新（buyer_id）
        $product->buyer_id = auth()->id();
        $product->save();

        return redirect()->route('products.index')->with('status', '購入が完了しました！');
    }

    // コメント投稿処理
    public function commentStore(CommentRequest $request, $productId)
    {
        Comment::create([
            'user_id'    => auth()->id(),
            'product_id' => $productId,
            'comment'    => $request->input('comment'),
        ]);

        return redirect()->route('products.show', $productId)
            ->with('message', 'コメントを投稿しました！');
    }

    // いいねトグル処理（Ajax）
    public function toggleFavorite(Product $product)
    {
        $user = Auth::user();
        $isLiked = $product->likedUsers()->where('user_id', $user->id)->exists();

        if ($isLiked) {
            $product->likedUsers()->detach($user->id);
        } else {
            $product->likedUsers()->attach($user->id);
        }

        return response()->json([
            'status'      => $isLiked ? 'unliked' : 'liked',
            'likes_count' => $product->likedUsers()->count(),
        ]);
    }

    // マイページ（出品商品と購入商品を表示）
    public function mypage()
    {
        $user = Auth::user();

        $sellingProducts = Product::where('user_id', $user->id)
            ->latest()
            ->paginate(8);

        $purchasedProducts = Product::where('buyer_id', $user->id)
            ->latest()
            ->paginate(8);

        return view('mypage.index', compact('user', 'sellingProducts', 'purchasedProducts'));
    }

    // 購入キャンセル
    public function cancelPurchase(Product $product)
    {
        if ($product->buyer_id !== auth()->id()) {
            abort(403, '不正な操作です');
        }

        $product->buyer_id = null;
        $product->save();

        return redirect()->route('mypage')->with('status', '購入をキャンセルしました');
    }

    // 住所編集画面表示
    public function editAddress()
    {
        $address = session('address', [
            'postal_code' => '',
            'address'     => '',
            'building'    => '',
        ]);

        return view('edit_address', compact('address'));
    }

    // 住所更新処理
    public function updateAddress(Request $request)
    {
        $validated = $request->validate([
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address'     => 'required|string|max:255',
            'building'    => 'nullable|string|max:255',
        ], [
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex'    => '郵便番号は「123-4567」形式で入力してください',
            'address.required'     => '住所を入力してください',
        ]);

        session(['address' => $validated]);

        $productId = session('product_id');
        return redirect()->route('products.purchase.show', $productId)
            ->with('address_changed_message', '住所が変更されました');
    }
}
