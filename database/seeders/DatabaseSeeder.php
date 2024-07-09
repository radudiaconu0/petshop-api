<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\Post;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(1)->create([
            'email' => 'admin@buckhill.co.uk',
            'is_admin' => true,
            'password' => bcrypt('admin')
        ]);
        Brand::factory(10)->create();
        Category::factory(10)->create();
        Post::factory(40)->create();
        User::factory(10)->create();

        User::all()->each(function ($user) {
            $user->orders()->saveMany(Order::factory(20)->make([
                'payment_id' => Payment::factory()->create()->id,
                'order_status_id' => OrderStatus::factory()->create()->id,
            ]));
        });
    }
}
