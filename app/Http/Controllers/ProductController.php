<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar master obat beserta informasi stok riil per batch (FEFO).
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'batches' => function ($q) {
            $q->where('stock', '>', 0)
              ->orderBy('expired_date', 'asc');
        }])->orderBy('name');

        // Filter pencarian berdasarkan nama obat atau kategori
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'like', "%{$searchTerm}%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Endpoint API pencarian obat cepat untuk antarmuka kasir (POS).
     */
    public function searchApi(Request $request): JsonResponse
    {
        $search = $request->query('q');

        $products = Product::with(['activeFefoBatches'])
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->get()
            ->filter(function ($product) {
                return $product->total_stock > 0;
            })
            ->values()
            ->map(function ($product) {
                $nearestBatch = $product->activeFefoBatches->first();
                return [
                    'id'            => $product->id,
                    'name'          => $product->name,
                    'unit'          => $product->unit,
                    'category'      => $product->category?->name ?? 'Umum',
                    'total_stock'   => $product->total_stock,
                    'sell_price'    => $nearestBatch ? (float) $nearestBatch->sell_price : 0,
                    'nearest_batch' => $nearestBatch ? [
                        'batch_number' => $nearestBatch->batch_number,
                        'expired_date' => $nearestBatch->expired_date->format('d/m/Y'),
                    ] : null,
                ];
            });

        return response()->json($products);
    }
}

