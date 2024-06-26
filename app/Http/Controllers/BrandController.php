<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        return Brand::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'uuid' => ['required'],
            'title' => ['required'],
            'slug' => ['required'],
        ]);

        return Brand::create($data);
    }

    public function show(Brand $brand)
    {
        return $brand;
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'uuid' => ['required'],
            'title' => ['required'],
            'slug' => ['required'],
        ]);

        $brand->update($data);

        return $brand;
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();

        return response()->json();
    }
}
