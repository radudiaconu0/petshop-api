<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index()
    {
        return Promotion::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'uuid' => ['required'],
            'title' => ['required'],
            'content' => ['required'],
            'metadata' => ['required'],
        ]);

        return Promotion::create($data);
    }

    public function show(Promotion $promotion)
    {
        return $promotion;
    }

    public function update(Request $request, Promotion $promotion)
    {
        $data = $request->validate([
            'uuid' => ['required'],
            'title' => ['required'],
            'content' => ['required'],
            'metadata' => ['required'],
        ]);

        $promotion->update($data);

        return $promotion;
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

        return response()->json();
    }
}
