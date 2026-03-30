<?php

namespace App\Services;

use App\Models\OrderItem;

class OrderItemService
{
    public function getAll()
    {
        return OrderItem::with('product', 'order')->get();
    }

    public function create(array $data) {
        return OrderItem::create($data);
    }

    public function update(OrderItem $orderItem, array $data)
    {
        $orderItem->update($data);
        return $orderItem->load('product');
    }

    public function delete(OrderItem $orderItem)
    {
        return $orderItem->delete();
    }
}
