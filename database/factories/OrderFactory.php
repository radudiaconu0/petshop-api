<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => $this->faker->randomNumber(),
            'order_status_id' => $this->faker->randomNumber(),
            'payment_id' => $this->faker->randomNumber(),
            'uuid' => $this->faker->uuid(),
            'products' => $this->faker->words(),
            'address' => $this->faker->address(),
            'delivery_fee' => $this->faker->randomFloat(),
            'amount' => $this->faker->randomFloat(),
            'shipped_at' => Carbon::now(),
        ];
    }
}
