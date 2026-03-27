<?php

namespace App\Services;

use App\Models\Order;

class OrderService
{
    public function getAll()
    {
        return Order::with('orderItems')->latest()->paginate(5);
    }

    public function create(array $data)
    {
        return Order::create($data);
    }

    public function update(Order $order, array $data)
    {
        $order->update($data);
        return $order;
    }

    public function delete(Order $order)
    {
        return $order->delete();
    }
}
