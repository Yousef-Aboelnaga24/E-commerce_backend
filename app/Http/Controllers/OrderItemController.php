<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Http\Controllers\Controller;
// Request
use App\Http\Requests\OrderItem\StoreOrderItemRequest;
use App\Http\Requests\OrderItem\UpdateOrderItemRequest;
// Resource
use App\Http\Resources\OrderItemResource;
// Service
use App\Services\OrderItemService;

class OrderItemController extends Controller
{
    protected $orderItemService;
    public function __construct(OrderItemService $orderItemService)
    {
        $this->orderItemService = $orderItemService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orderItems = $this->orderItemService->getAll();

        return OrderItemResource::collection($orderItems);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderItemRequest $request)
    {
        $data = $request->validated();

        $orderItem = $this->orderItemService->create($data);

        return new OrderItemResource($orderItem);
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderItem $orderItem)
    {
        return new OrderItemResource($orderItem);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderItemRequest $request, OrderItem $orderItem)
    {
        $data = $request->validated();

        $updateOrderItem = $this->orderItemService->update($orderItem, $data);

        return new OrderItemResource($updateOrderItem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderItem $orderItem)
    {
        $this->orderItemService->delete($orderItem);

        return response()->json([
            'success' => true,
            'message' => 'Order Item deleted successfully'
        ],200);
    }
}
