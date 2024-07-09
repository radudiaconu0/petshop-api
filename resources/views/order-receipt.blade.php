<!DOCTYPE html>
<html>
<head>
    <title>Order Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
        }
        .header, .footer {
            text-align: center;
            padding: 10px;
        }
        .order-details, .products, .totals {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        .order-details td, .products td, .products th, .totals td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .products th {
            background: #f4f4f4;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>PetShop</h1>
        <p>Date: {{ \Carbon\Carbon::now()->format('d-m-Y') }}</p>
        <p>Invoice #: {{ $order->uuid }}</p>
    </div>

    <div class="section-title">Customer Details:</div>
    <table class="order-details">
        <tr>
            <td>Name: {{ $order->user->name }}</td>
            <td>Billing: {{ json_decode($order->address)->billing }}</td>
        </tr>
        <tr>
            <td>Email: {{ $order->user->email }}</td>
            <td>Shipping: {{ json_decode($order->address)->shipping }}</td>
        </tr>
        <tr>
            <td>Phone number: {{ $order->user->phone_number }}</td>
            <td>Payment method: {{ strtoupper($order->payment->type) }}</td>
        </tr>
    </table>

    <div class="section-title">Items:</div>
    <table class="products">
        <thead>
        <tr>
            <th class="text-center">#</th>
            <th class="text-center">ID</th>
            <th>Item Name</th>
            <th class="text-center">Quantity</th>
            <th class="text-right">Unit Price</th>
            <th class="text-right">Price</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($order->products as $index => $product)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $product->uuid }}</td>
                <td>{{ $product->title }}</td>
                <td class="text-center">{{ $product->quantity }}</td>
                <td class="text-right">${{ number_format($product->price, 2) }}</td>
                <td class="text-right">${{ number_format($product->price * $product->quantity, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="section-title">Summary:</div>
    <table class="totals">
        <tr>
            <td class="text-right"><strong>Subtotal:</strong></td>
            <td class="text-right">${{ number_format($order->amount, 2) }}</td>
        </tr>
        <tr>
            <td class="text-right"><strong>Delivery Fee:</strong></td>
            <td class="text-right">${{ number_format($order->delivery_fee, 2) }}</td>
        </tr>
        <tr>
            <td class="text-right"><strong>TOTAL:</strong></td>
            <td class="text-right"><strong>${{ number_format($order->amount + $order->delivery_fee, 2) }}</strong></td>
        </tr>
    </table>
</div>
</body>
</html>
