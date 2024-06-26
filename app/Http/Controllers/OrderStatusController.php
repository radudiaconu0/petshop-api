<?php

namespace App\Http\Controllers;

use App\Models\OrderStatus;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    public function index()
    {
        return OrderStatus::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'uuid' => ['required'],
            'title' => ['required'],
        ]);

        return OrderStatus::create($data);
    }

    public function show(OrderStatus $orderStatus)
    {
        return $orderStatus;
    }

    public function update(Request $request, OrderStatus $orderStatus)
    {
        $data = $request->validate([
            'uuid' => ['required'],
            'title' => ['required'],
        ]);

        $orderStatus->update($data);

        return $orderStatus;
    }

    public function destroy(OrderStatus $orderStatus)
    {
        $orderStatus->delete();

        return response()->json();
    }
}
