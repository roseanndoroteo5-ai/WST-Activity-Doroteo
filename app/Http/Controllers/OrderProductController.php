<?php

namespace App\Http\Controllers;

use App\Models\OrderProduct;
use Illuminate\Http\Request;

class OrderProductController extends Controller
{
    public function index()
    {
        return response()->json(
            OrderProduct::with('order', 'product')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $orderProduct = OrderProduct::create($validated);

        return response()->json(
            $orderProduct->load('order', 'product'),
            201
        );
    }

    public function show(OrderProduct $orderProduct)
    {
        return response()->json(
            $orderProduct->load('order', 'product')
        );
    }

    public function update(Request $request, OrderProduct $orderProduct)
    {
        $validated = $request->validate([
            'order_id' => 'sometimes|required|exists:orders,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'quantity' => 'sometimes|required|integer|min:1',
        ]);

        $orderProduct->update($validated);

        return response()->json(
            $orderProduct->load('order', 'product')
        );
    }

    public function destroy(OrderProduct $orderProduct)
    {
        $orderProduct->delete();

        return response()->json([
            'message' => 'Order product deleted successfully'
        ]);
    }
}