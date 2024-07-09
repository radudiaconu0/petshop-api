<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, HasUuids;

    protected function casts()
    {
        return [
            'uuid' => 'string',
            'products' => 'array',
            'address' => 'array',
            'shipped_at' => 'timestamp',
        ];
    }

    public function uniqueIds()
    {
        return ['uuid'];
    }

    public function findByUuid($uuid)
    {
        return $this->where('uuid', $uuid)->first();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order_status()
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function products()
    {
        $products = $this->products;
        // get uuid from products
        $uuids = array_column($products, 'uuid');
        $quantity = array_column($products, 'quantity');

        // get products from database
        $products = Product::whereIn('uuid', $uuids)->get();

        // add quantity to products

        foreach ($products as $key => $product) {

            $product->quantity = $quantity[$key];
        }

        return $products;
    }

    public function apiObject()
    {
        return [
            'uuid' => $this->uuid,
            'products' => $this->products,
            'payment' => $this->payment?->apiObject(),
            'order_status' => $this->order_status?->apiObject(),
            'address' => $this->address,
            'shipped_at' => $this->shipped_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
