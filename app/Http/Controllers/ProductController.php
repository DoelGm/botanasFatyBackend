<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;



class ProductController extends Controller
{
    // GET /api/products
    public function index()
    {

        $products = Product::with('images')->get()->all();

        return response()->json($products);
    }

    // POST /api/products
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Producto creado con éxito',
            'product' => $product
        ], 201);
    }

    public function showAll()
    {
        $products = Product::all();
        return response()->json($products);
    }
    public function showByCategory($id)
    {
        $products = Product::with('images')->where('category_id', $id)->get();

        return response()->json($products);
    }


    // GET /api/products/{id}
    public function show($id)
    {
        $product = Product::findOrFail($id);

        return response()->json($product);
    }

    // PUT /api/products/{id}
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric',
            'discount' => 'nullable|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'stock' => 'sometimes|integer|min:0',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp',
        ]);

        $product = Product::findOrFail($id);
        $product->update($validated);

        return response()->json([
            'message' => 'Producto actualizado correctamente',
            'product' => $product
        ]);
    }

    // DELETE /api/products/{id}
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return response()->json(['message' => 'Producto e imágenes eliminadas correctamente']);
    }

}
