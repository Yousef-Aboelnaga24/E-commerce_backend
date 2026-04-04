<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
// Request
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
// Resource
use App\Http\Resources\OrderResource;
// Service
use App\Services\OrderService;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        $orders = $this->orderService->getAll();
        return OrderResource::collection($orders);
    }

    public function store(StoreOrderRequest $request)
    {
        $order = $this->orderService->create($request->validated());
        return new OrderResource($order);
    }

    public function show(Order $order)
    {
        return new OrderResource($order->load('items.product'));
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order = $this->orderService->update($order, $request->validated());
        return new OrderResource($order);
    }

    public function destroy(Order $order)
    {
        $this->orderService->delete($order);

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully'
        ]);
    }
}