<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class OrderService
{
    public function getAll()
    {
        return Order::with('items.product')->latest()->paginate(5);
    }

    public function create(array $data)
    {
        if (!Auth::check()) {
            throw new Exception('User must be logged in.');
        }

        return DB::transaction(function () use ($data) {

            $total = 0;
            $items = [];

            $products = Product::whereIn('id', collect($data['items'])->pluck('product_id'))
                ->get()
                ->keyBy('id');

            foreach ($data['items'] as $item) {

                $product = $products[$item['product_id']] ?? null;

                if (!$product) {
                    throw new Exception('Product not found');
                }

                if ($product->stock < $item['quantity']) {
                    throw new Exception("{$product->name} out of stock");
                }

                $items[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $product->price,
                ];

                $total += $product->price * $item['quantity'];

                // تقليل المخزون
                $product->decrement('stock', $item['quantity']);
            }

            $order = Order::create([
                'user_id'     => Auth::id(),
                'total_price' => $total,
                'status'      => $data['status'],
            ]);

            $order->items()->createMany($items);

            return $order->load('items.product');
        });
    }

    public function update(Order $order, array $data)
    {
        return DB::transaction(function () use ($order, $data) {
            try {
                if (isset($data['items']) && is_array($data['items'])) {
                    $order->items()->delete();

                    $items = [];
                    $total = 0;

                    foreach ($data['items'] as $item) {
                        $product = Product::findOrFail($item['product_id']);
                        $items[] = [
                            'product_id' => $product->id,
                            'quantity'   => $item['quantity'],
                            'price'      => $product->price,
                        ];
                        $total += $product->price * $item['quantity'];
                    }

                    if (!empty($items)) {
                        $order->items()->createMany($items);
                    }

                    $data['total_price'] = $total;
                }

                $order->update($data);

                return $order->load('items.product');
            } catch (Exception $e) {
                Log::error('Order update failed: ' . $e->getMessage(), ['order_id' => $order->id, 'data' => $data]);
                throw $e;
            }
        });
    }

    public function delete(Order $order)
    {
        return DB::transaction(function () use ($order) {
            try {
                $order->items()->delete();
                return $order->delete();
            } catch (Exception $e) {
                Log::error('Order deletion failed: ' . $e->getMessage(), ['order_id' => $order->id]);
                throw $e;
            }
        });
    }
}
