<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductPageController extends Controller
{

    public function show($id)
    {
        $product = Product::with('brand', 'category', 'product_images')->findOrFail($id);
    
        return Inertia::render('User/ProductPage', [
            'product' => $product,
        ]);
    }
    
    public function index()
    {

        $products = Product::with('category', 'brand', 'product_images')->get();
        $brands = Brand::get();
        $categories = Category::get();

        return Inertia::render(
            'User/ProductPage', 
            [
                'products' => $products, 
                'brands' => $brands, 
                'categories' => $categories
            ]
        );
    }
}


