<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductResource;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    use ApiResponses;

    /**
     * Display a paginated list of products with their category.
     * Accepts ?search= (name, description or category name) and ?per_page= (default 10, max 50).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%")
                    ->orWhereHas('category', function ($c) use ($searchTerm) {
                        $c->where('name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        $perPage = min(max((int) $request->input('per_page', 10), 1), 50);

        $products = $query->latest()->latest('id')->paginate($perPage);

        return $this->ok(
            'Products retrieved successfully',
            ProductResource::collection($products)->response()->getData(true)
        );
    }

    /**
     * Store a newly created product (multipart form data, optional photo upload).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('products', 'public');
            $validated['photo'] = asset('storage/'.$path);
        }

        $product = Product::create($validated)->load('category');

        return $this->success('Product created successfully', new ProductResource($product), 201);
    }

    /**
     * Display one product.
     */
    public function show(Product $product): JsonResponse
    {
        $product->load('category');

        return $this->ok(
            'Product retrieved successfully',
            new ProductResource($product)
        );
    }

    /**
     * Update the given product.
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'sometimes|required|exists:categories,id',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
        ]);

        $product->update($validated);
        $product->load('category');

        return $this->ok('Product updated successfully', new ProductResource($product));
    }

    /**
     * Delete the given product.
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return $this->ok('Product deleted successfully');
    }

    /**
     * Run the Eloquent query examples from Activity 5 and return all results.
     */
    public function queries(): JsonResponse
    {
        // =========================================================
        // A. QUERYING TABLES
        // =========================================================

        // Get the name and price columns from the table.
        $nameAndPrice = Product::select('name', 'price')->get();

        // Load products with their category (avoids N+1 queries).
        $productsWithCategory = Product::with('category')->get();

        // Get the product id and name, ordered by price ascending.
        $productsOrderedByPrice = Product::select('id', 'name')
            ->orderBy('price', 'asc')
            ->get();

        // Get the first 5 rows from the products table.
        $firstFiveProducts = Product::limit(5)->get();

        // =========================================================
        // B. FILTERING DATA
        // =========================================================

        // Get products with a price more than or equal to 3.
        $priceGreaterThanOrEqual = Product::where('price', '>=', 3)->get();

        // Get products in the 'Electronics' category.
        $electronics = Product::whereHas('category', function ($q) {
            $q->where('name', 'Electronics');
        })->get();

        // =========================================================
        // C. AGGREGATING DATA
        // =========================================================

        // Get the total price across all products.
        $totalPrice = Product::sum('price');

        // Get the average price per product.
        $averagePrice = Product::avg('price');

        // Get the product with the highest price.
        $highestPrice = Product::max('price');

        // Get the product with the lowest price.
        $lowestPrice = Product::min('price');

        // =========================================================
        // D. GROUPING, FILTERING, AND SORTING
        // =========================================================

        // Get the total price of products for each category.
        $totalPricePerCategory = Product::join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(products.price) as total_price'))
            ->groupBy('categories.name')
            ->get();

        // Get the average price for each category.
        $averagePricePerCategory = Product::join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('AVG(products.price) as avg_price'))
            ->groupBy('categories.name')
            ->get();

        // Get the product with the lowest price per category.
        $lowestPricePerCategory = Product::join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('MIN(products.price) as min_price'))
            ->groupBy('categories.name')
            ->get();

        // Get the average price per category, sorted ascending.
        $averagePriceSorted = Product::join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('AVG(products.price) as avg_price'))
            ->groupBy('categories.name')
            ->orderBy('avg_price', 'asc')
            ->get();

        // Get the average price for 'Electronics' and 'Books' categories.
        $electronicsAndBooks = Product::join('categories', 'products.category_id', '=', 'categories.id')
            ->whereIn('categories.name', ['Electronics', 'Books'])
            ->select('categories.name', DB::raw('AVG(products.price) as avg_price'))
            ->groupBy('categories.name')
            ->get();

        // =========================================================
        // RETURN ALL RESULTS AS JSON
        // =========================================================

        $queryResults = [
            'name_and_price' => $nameAndPrice,
            'products_with_category' => $productsWithCategory,
            'products_ordered_by_price' => $productsOrderedByPrice,
            'first_five_products' => $firstFiveProducts,

            'price_greater_than_or_equal' => $priceGreaterThanOrEqual,
            'electronics' => $electronics,

            'total_price' => $totalPrice,
            'average_price' => $averagePrice,
            'highest_price' => $highestPrice,
            'lowest_price' => $lowestPrice,

            'total_price_per_category' => $totalPricePerCategory,
            'average_price_per_category' => $averagePricePerCategory,
            'lowest_price_per_category' => $lowestPricePerCategory,
            'average_price_sorted' => $averagePriceSorted,
            'electronics_and_books' => $electronicsAndBooks,
        ];

        return $this->success('Query results retrieved successfully', $queryResults);
    }

    /**
     * Return real aggregate numbers for the dashboard.
     *
     * The schema has no stock column, so figures are based on the catalog
     * (products.price) and on what was ordered (order_products.quantity).
     */
    public function stats(): JsonResponse
    {
        $recentProducts = Product::with('category')
            ->latest()
            ->latest('id')
            ->limit(5)
            ->get();

        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'total_units_ordered' => (int) OrderProduct::sum('quantity'),
            'catalog_value' => round((float) Product::sum('price'), 2),
            'sales_value' => round((float) OrderProduct::join('products', 'order_products.product_id', '=', 'products.id')
                ->sum(DB::raw('order_products.quantity * products.price')), 2),
            'recent_products' => ProductResource::collection($recentProducts),
            'categories' => $this->categoryBreakdown(),
            'sales_by_month' => $this->salesByMonth(),
        ];

        return $this->ok('Product stats retrieved successfully', $stats);
    }

    /**
     * Per category: how many products it has, units ordered, and sales value.
     */
    private function categoryBreakdown(): array
    {
        $productCounts = Product::select('category_id', DB::raw('COUNT(*) as total'))
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $sales = OrderProduct::join('products', 'order_products.product_id', '=', 'products.id')
            ->select(
                'products.category_id',
                DB::raw('SUM(order_products.quantity) as units'),
                DB::raw('SUM(order_products.quantity * products.price) as sales')
            )
            ->groupBy('products.category_id')
            ->get()
            ->keyBy('category_id');

        return Category::orderBy('id')->get()->map(fn (Category $category) => [
            'id' => $category->id,
            'name' => ucwords($category->name),
            'products' => (int) ($productCounts[$category->id] ?? 0),
            'units' => (int) ($sales[$category->id]->units ?? 0),
            'sales' => round((float) ($sales[$category->id]->sales ?? 0), 2),
        ])->all();
    }

    /**
     * Orders and sales value for each of the last 12 months (months with no orders are 0).
     */
    private function salesByMonth(): array
    {
        $start = now()->startOfMonth()->subMonths(11);

        $rows = Order::join('order_products', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->where('orders.date', '>=', $start->toDateString())
            ->select(
                DB::raw("DATE_FORMAT(orders.date, '%Y-%m') as month"),
                DB::raw('COUNT(DISTINCT orders.id) as orders'),
                DB::raw('SUM(order_products.quantity * products.price) as sales')
            )
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $month = $start->copy()->addMonths($i);
            $key = $month->format('Y-m');
            $months[] = [
                'month' => $key,
                'label' => $month->format('M'),
                'orders' => (int) ($rows[$key]->orders ?? 0),
                'sales' => round((float) ($rows[$key]->sales ?? 0), 2),
            ];
        }

        return $months;
    }
}
