<?php

namespace Database\Factories;

use App\Models\Product1;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class Product1Factory extends Factory
{
    protected $model = Product1::class;

    public function definition(): array
    {
        // Indexed Array
        $categories = [
            'Hardware',
            'Electrical',
            'Plumbing'
        ];

        // Multidimensional Array
        $products = [
            [
                'name' => 'hammer',
                'price' => 250
            ],
            [
                'name' => 'extension wire',
                'price' => 450
            ],
            [
                'name' => 'water pipe',
                'price' => 180
            ]
        ];

        $product = $this->faker->randomElement($products);

        $name = ucwords(
            strtolower(
                trim($product['name'])
            )
        );

        $slug = Str::slug($name);

        // Make the slug unique
        $slug = $slug . '-' . $this->faker->unique()->numberBetween(1, 10000);

        return [
            'name' => $name,
            'slug' => $slug,
            'category' => $this->faker->randomElement($categories),
            'price' => $product['price'],
            'quantity' => $this->faker->numberBetween(1, 100),
            'status' => 'Available',
        ];
    }
}