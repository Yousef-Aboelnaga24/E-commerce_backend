<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    public function getAll()
    {
        return Order::with('items')->latest()->paginate(5);
    }


    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            $total = 0;
            $items = [];

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ];

                $total += $product->price * $item['quantity'];
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'total_price' => $total,
                'status' => 'pending'
            ]);

            $order->items()->createMany($items);

            return $order->load('items.product');
        });
    }

    public function update(Order $order, array $data)
    {
        return DB::transaction(function () use ($order, $data) {
            if (isset($data['items'])) {
                $order->items()->delete();
                $order->items()->createMany($data['items']);
                $data['total_price'] = collect($data['items'])->sum(fn($item) => $item['price'] * $item['quantity']);
            }

            $order->update($data);
            return $order->load('items.product');
        });
    }

    public function delete(Order $order)
    {
        return $order->delete();
    }
}
