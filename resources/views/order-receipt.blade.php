<!DOCTYPE html>
<html>
<head>
    <title>Order Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 60%;
            margin: auto;
        }
        .header, .footer {
            text-align: center;
            padding: 10px;
        }
        .order-details, .products, .totals {
            width: 100%;
            margin: 20px 0;
        }
        .order-details td, .products td, .totals td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .products th {
            padding: 10px;
            background: #f4f4f4;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Order Receipt</h1>
        <p>Thank you for your order!</p>
    </div>
    <div class="order-details">
        <table>
            <tr>
                <td>Order ID:</td>
                <td>{{ $order->id }}</td>
            </tr>
            <tr>
                <td>User:</td>
                <td>{{ $order->user->first_name }} {{ $order->user->last_name }}</td>
            </tr>
            <tr>
                <td>Order Status:</td>
                <td>{{ $order->order_status->title }}</td>
            </tr>
            <tr>
                <td>Payment ID:</td>
                <td>{{ $order->payment->uuid }}</td>
            </tr>
            <tr>
                <td>Order Date:</td>
                <td>{{ $order->created_at->format('d-m-Y H:i:s') }}</td>
            </tr>
            <tr>
                <td>Billing Address:</td>
                <td>{{ $order->address->billing }}</td>
            </tr>
            <tr>
                <td>Shipping Address:</td>
                <td>{{ $order->address->shipping }}</td>
            </tr>
        </table>
    </div>
    <div class="products">
        <h2>Products</h2>
        <table>
            <thead>
            <tr>
                <th>Title</th>
                <th>Price</th>
                <th>Quantity</th>
            </tr>
            </thead>
            <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->title }}</td>
                    <td>{{ number_format($product->price, 2) }}</td>
                    <td>{{ collect(json_decode($order->products))->firstWhere('product', $product->uuid)->quantity }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="totals">
        <table>
            <tr>
                <td>Delivery Fee:</td>
                <td>{{ number_format($order->delivery_fee, 2) }}</td>
            </tr>
            <tr>
                <td>Total Amount:</td>
                <td>{{ number_format($order->amount, 2) }}</td>
            </tr>
        </table>
    </div>
    <div class="footer">
        <p>If you have any questions, please contact our support team.</p>
    </div>
</div>
</body>
</html>
