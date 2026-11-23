<?php

namespace App\Http\Controllers;

use App\Models\Product1;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Product1Controller extends Controller
{
    // INDEX
    public function index()
    {
        $products = Product1::all();

        $result = [];

        foreach ($products as $product) {
            $result[] = [
                'id' => $product->id,
                'name' => $this->formatProductName($product->name),
                'slug' => $product->slug,
                'category' => $product->category,
                'price' => $product->price,
                'quantity' => $product->quantity,
                'status' => $product->status,
            ];
        }

       
        $numbers = [];

        for ($i = 1; $i <= 5; $i++) {
            $numbers[] = $i;
        }

        return response()->json([
            'status' => true,
            'numbers' => $numbers,
            'data' => $result
        ], 200);
    }


    public function show(string $slug)
    {
        $product = Product1::where('slug', $slug)->first();

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Product retrieved successfully.',
            'data' => [
                'id' => $product->id,
                'name' => $this->formatProductName($product->name),
                'slug' => $product->slug,
                'category' => $product->category,
                'price' => $product->price,
                'quantity' => $product->quantity,
                'status' => $product->status,
            ]
        ], 200);
    }

  
    private function formatProductName($name)
    {
        return ucwords(
            strtolower(
                trim($name)
            )
        );
    }
}