<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $orders = Order::with('items')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return OrderResource::collection($orders);
    }

    public function store(Request $request): OrderResource
    {
        $validated = $request->validate([
            'notes'              => ['nullable', 'string'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $total = 0;
        $itemsData = [];

        foreach ($validated['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $unitPrice = $product->price;
            $total += $unitPrice * $item['quantity'];
            $itemsData[] = [
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'unit_price' => $unitPrice,
            ];
        }

        $order = Order::create([
            'user_id' => $request->user()->id,
            'status'  => 'pending',
            'total'   => $total,
            'notes'   => $validated['notes'] ?? null,
        ]);

        $order->items()->createMany($itemsData);

        return new OrderResource($order->load('items'));
    }

    public function show(Request $request, Order $order): OrderResource
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        return new OrderResource($order->load('items'));
    }

    public function update(Request $request, Order $order): OrderResource
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'status' => ['sometimes', 'in:pending,processing,completed,cancelled'],
            'notes'  => ['nullable', 'string'],
        ]);

        $order->update($validated);

        return new OrderResource($order->load('items'));
    }

    public function destroy(Request $request, Order $order): JsonResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
