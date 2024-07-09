<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'limit' => ['integer'],
            'sort' => ['string', 'in:asc,desc'],
            'sort_by' => ['string'],
        ]);

        $categories = Category::query();

        if ($request->has('sort_by')) {
            $categories->orderBy($request->sort_by, $request->sort ?? 'desc');
        }

        return ResponseHelper::success($categories->paginate($request->limit ?? 10));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required'],
            'slug' => ['required'],
        ]);

        return ResponseHelper::success(Category::create($data));
    }

    public function show(Category $category)
    {
        return ResponseHelper::success($category);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'uuid' => ['required'],
            'title' => ['required'],
            'slug' => ['required'],
        ]);

        $category->update($data);

        return $category;
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json();
    }
}
