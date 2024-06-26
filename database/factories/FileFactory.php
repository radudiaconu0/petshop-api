<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'uuid' => $this->faker->uuid(),
            'name' => $this->faker->name(),
            'path' => $this->faker->word(),
            'size' => $this->faker->word(),
            'type' => $this->faker->word(),
        ];
    }
}
