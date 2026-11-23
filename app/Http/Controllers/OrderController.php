<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return response()->json(
            Order::with('user', 'orderProducts.product')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
        ]);

        $order = Order::create($validated);

        return response()->json(
            $order->load('user'),
            201
        );
    }

    public function show(Order $order)
    {
        return response()->json(
            $order->load('user', 'orderProducts.product')
        );
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'date' => 'sometimes|required|date',
        ]);

        $order->update($validated);

        return response()->json(
            $order->load('user')
        );
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully'
        ]);
    }
}