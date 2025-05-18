<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all(); // ← DBから取得
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }
    public function show($id)
    {
        $product = Product::findOrFail($id); // ← 該当IDの商品を取得（なければ404）
        return view('products.show', compact('product'));
    }
}
