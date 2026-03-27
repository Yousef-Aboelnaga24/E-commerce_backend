<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
        ]);
        $users = User::factory(10)->create();

        $categories = Category::factory(5)->create();

        $categories->each(function ($category) {
            Product::factory(10)->for($category)->create();
        });

        $users->each(function ($user) {
            $orders = Order::factory(rand(1, 3))->for($user)->create();

            $orders->each(function ($order) {
                $items = OrderItem::factory(rand(1, 5))->create([
                    'order_id' => $order->id,
                    'product_id' => Product::inRandomOrder()->first()->id
                ]);

                $total = $items->sum(function ($item) {
                    return $item->price * $item->quantity;
                });
                $order->update(['total_price' => $total]);
            });
        });
    }
}
