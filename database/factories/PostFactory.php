<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'uuid' => $this->faker->uuid(),
            'title' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'content' => $this->faker->word(),
            'metadata' => $this->faker->word(),
        ];
    }
}
