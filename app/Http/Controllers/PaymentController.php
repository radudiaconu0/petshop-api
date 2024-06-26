<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        return Payment::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'uuid' => ['required'],
            'type' => ['required'],
            'details' => ['required'],
        ]);

        return Payment::create($data);
    }

    public function show(Payment $payment)
    {
        return $payment;
    }

    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'uuid' => ['required'],
            'type' => ['required'],
            'details' => ['required'],
        ]);

        $payment->update($data);

        return $payment;
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return response()->json();
    }
}
