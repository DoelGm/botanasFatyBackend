<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    try {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'imgs' => 'nullable|array',
            'imgs.*' => 'url',
        ]);

        $product = new Product();
        $product->name = $validatedData['name'];
        $product->price = $validatedData['price'];
        $product->description = $validatedData['description'] ?? null;
        $product->category_id = $validatedData['category_id'];
        $product->imgs = isset($validatedData['imgs']) ? json_encode($validatedData['imgs']) : json_encode([]);
        $product->save();

        return response()->json($product, 201);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error al crear el producto',
            'error' => $e->getMessage()
        ], 500); // Código 500 = Error interno del servidor
    }
}



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
    
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
            'description' => 'nullable|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'imgs' => 'nullable|array',
            'imgs.*' => 'url',
            'remove_imgs' => 'nullable|array', // Para eliminar imágenes
            'remove_imgs.*' => 'url', // Validación de las imágenes a eliminar
           
        ]);

        if (isset($validatedData['name'])) {
            $product->name = $validatedData['name'];
        }
    
        if (isset($validatedData['price'])) {
            $product->price = $validatedData['price'];
        }
    
        if (array_key_exists('description', $validatedData)) {
            $product->description = $validatedData['description'];
        }
        if (array_key_exists('category_id', $validatedData)) {
            $product->category_id = $validatedData['category_id'];
        }

        // Si se recibe nuevas imágenes (imgs), las agregamos
        if (isset($validatedData['imgs'])) {
            $existingImgs = json_decode($product->imgs ?? '[]', true);
    
            // Combinar las imágenes existentes con las nuevas imágenes
            $product->imgs = json_encode(array_merge($existingImgs, $validatedData['imgs']));
        }
    
        // Si se reciben imágenes a eliminar (remove_imgs), las eliminamos
        if (isset($validatedData['remove_imgs'])) {
            $existingImgs = json_decode($product->imgs ?? '[]', true);
            
            // Filtrar las imágenes que no están en el array de eliminación
            $product->imgs = json_encode(array_diff($existingImgs, $validatedData['remove_imgs']));
        }
    
        $product->save();
    
        return response()->json($product);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
    
        return response()->json(null, 204);
    }
}
