<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'category_uuid' => $this->faker->uuid(),
            'uuid' => $this->faker->uuid(),
            'title' => $this->faker->word(),
            'price' => $this->faker->randomFloat(),
            'description' => $this->faker->text(),
            'metadata' => $this->faker->words(),
        ];
    }
}
