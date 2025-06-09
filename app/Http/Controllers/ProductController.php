<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;



class ProductController extends Controller
{
    // GET /api/products
    public function index()
    {
        $products = Product::all();
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
        ]);

        // Crear el producto
        $product = Product::create($validated);

        return response()->json([
            'message' => 'Producto creado con éxito',
            'product' => $product
        ], 201);
    }

    public function showByCategory($id)
    {
        $products = Product::with('images')->where('category_id', $id)->get();

        return response()->json($products);
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
