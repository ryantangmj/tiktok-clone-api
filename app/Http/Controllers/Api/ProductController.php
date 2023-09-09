<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\FileService;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    // Create a new product
    public function create(Request $request)
    {
         //Validate the incoming request data
         $request->validate([
             'name' => 'required|string',
             'description' => 'required|string',
             'price' => 'required|numeric',
             'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
         ]);

        try {
            $product = new Product;
            $product->name = $request->input('name');
            $product->description = $request->input('description');
            $product->price = $request->input('price');
            $product->user_id = auth()->user()->id;
            $product = (new FileService)->addProduct($product, $request);
            $product->save();
            return response()->json(['message' => 'Product created successfully', 'product' => $product], 201);
         }
        catch (\Exception $e) {
            return response()->json(['message' => 'Product creation failed!', 'error' => $e->getMessage()], 409);
        }
    }

    // Get product information by ID
    public function show($id)
    {
        // Find the product by ID
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Return a JSON response with the product information
        return response()->json(['product' => $product], 200);
    }

    public function list() {
        $products = Product::all();
        return response()->json(['products' => $products], 200);
    }
}