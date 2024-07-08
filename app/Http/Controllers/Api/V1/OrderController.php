<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

class OrderController extends Controller
{
    public function index()
    {
        return Order::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer'],
            'order_status_id' => ['required', 'integer'],
            'payment_id' => ['required', 'integer'],
            'uuid' => ['required'],
            'products' => ['required'],
            'address' => ['required'],
            'delivery_fee' => ['required', 'numeric'],
            'amount' => ['required', 'numeric'],
            'shipped_at' => ['nullable', 'date'],
        ]);

        return Order::create($data);
    }

    public function show(Order $order)
    {
        return $order;
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer'],
            'order_status_id' => ['required', 'integer'],
            'payment_id' => ['required', 'integer'],
            'uuid' => ['required'],
            'products' => ['required'],
            'address' => ['required'],
            'delivery_fee' => ['required', 'numeric'],
            'amount' => ['required', 'numeric'],
            'shipped_at' => ['nullable', 'date'],
        ]);

        $order->update($data);

        return $order;
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json();
    }

    public function downloadOrderReceipt(Order $order)
    {

        $products = $order->products();

        $pdf = Pdf::loadView('order-receipt', compact('order', 'products'));

        return $pdf->download(sprintf('order-receipt-%s.pdf', $order->uuid));
    }
}
