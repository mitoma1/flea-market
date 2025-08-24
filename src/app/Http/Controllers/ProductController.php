<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ExhibitionRequest;
use App\Http\Requests\CommentRequest;
use App\Models\Product;
use App\Models\Category;
use App\Models\Comment;

class ProductController extends Controller
{
    /**
     * 商品一覧（検索 + ページネーション）
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // 🔍 検索：商品名部分一致
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // ページネーション（検索語も保持）
        $products = $query->paginate(8)->appends($request->query());

        // お気に入り取得（ログイン中のみ）
        $favorites = auth()->check()
            ? auth()->user()->favoriteProducts()->get()
            : collect();

        return view('products.index', compact('products', 'favorites'));
    }

    /**
     * 出品フォーム
     */
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    /**
     * 出品保存処理
     */
    public function store(ExhibitionRequest $request)
    {
        $validated = $request->validated();

        // 画像アップロード
        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = $request->file('image')->store('images', 'public');
        }

        // 商品保存
        $product = Product::create([
            'user_id'     => auth()->id(),
            'image'       => $imageName,
            'condition'   => $validated['condition'],
            'name'        => $validated['name'],
            'brand'       => $validated['brand'] ?? null,
            'description' => $validated['description'],
            'price'       => $validated['price'],
            'status'      => 'selling', // 初期ステータス
        ]);

        // カテゴリ関連付け
        if (isset($validated['category_ids'])) {
            $categories = is_array($validated['category_ids'])
                ? $validated['category_ids']
                : [$validated['category_ids']];
            $product->categories()->sync($categories);
        }

        return redirect()->route('products.index')->with('status', '商品を出品しました！');
    }

    /**
     * 商品詳細
     */
    public function show($id)
    {
        $product = Product::with('categories', 'messages')->findOrFail($id);
        $partner = null;

        // 取引相手を取得
        if ($product->buyer_id && $product->buyer_id !== auth()->id()) {
            $partner = $product->buyer;
        } elseif ($product->user_id !== auth()->id()) {
            $partner = $product->user;
        }

        $messages = $product->messages()->with('user')->latest()->get();

        return view('products.show', compact('product', 'messages', 'partner'));
    }

    /**
     * 購入画面
     */
    public function showPurchase($id)
    {
        $product = Product::findOrFail($id);

        // 仮の住所（本番ではDB or ユーザー情報から取得）
        $address = session('address') ?? [
            'postal_code' => 'XXX-YYYY',
            'address'     => 'ここには住所と建物が入ります',
            'building'    => '',
            'name'        => '',
        ];

        session(['product_id' => $id]);

        return view('purchase', compact('product', 'address'));
    }

    /**
     * 購入処理（即取引中に反映）
     */
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

        // 購入情報を反映
        $product->buyer_id = Auth::id();
        $product->status   = 'trading'; // 取引中に更新
        $product->save();

        return redirect()->route('mypage.index')
            ->with('status', '購入が完了しました！取引中商品に反映されました。');
    }

    /**
     * コメント投稿
     */
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

    /**
     * いいねトグル（Ajax）
     */
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

    /**
     * マイページ
     */
    public function mypage()
    {
        $user = Auth::user()->load('receivedRatings'); // 評価リレーションもロード

        // 出品した商品
        $sellingProducts = Product::where('user_id', $user->id)
            ->where('status', 'selling')
            ->latest()
            ->get();

        // 購入した商品
        $purchasedProducts = Product::where('buyer_id', $user->id)
            ->latest()
            ->get();

        // 取引中の商品（出品者 or 購入者） + 未読メッセージ件数
        $tradingProducts = Product::withCount([
            'messages as unread_messages_count' => function ($query) use ($user) {
                $query->where('user_id', '!=', $user->id) // 自分以外のメッセージ
                    ->where('is_read', false);          // 未読
            }
        ])
            ->where('status', 'trading')
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('buyer_id', $user->id);
            })
            ->latest()
            ->get();

        // ビューに渡す
        return view('mypage.profile', compact(
            'user',
            'sellingProducts',
            'purchasedProducts',
            'tradingProducts'
        ));
    }

    /**
     * 住所編集
     */
    public function editAddress()
    {
        $address = session('address', [
            'postal_code' => '',
            'address'     => '',
            'building'    => '',
        ]);

        return view('edit_address', compact('address'));
    }

    /**
     * 住所更新
     */
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
