<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_uuid' => ['required'],
            'uuid' => ['required'],
            'title' => ['required'],
            'price' => ['required', 'numeric'],
            'description' => ['required'],
            'metadata' => ['required'],
        ]);

        return Product::create($data);
    }

    public function show(Product $product)
    {
        return $product;
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_uuid' => ['required'],
            'uuid' => ['required'],
            'title' => ['required'],
            'price' => ['required', 'numeric'],
            'description' => ['required'],
            'metadata' => ['required'],
        ]);

        $product->update($data);

        return $product;
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json();
    }
}
