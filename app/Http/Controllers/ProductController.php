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

        foreach ($products as $product) {
            $product->image_urls = $product->image_urls; // usa el accessor del modelo
        }

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

        // Crear el producto
        $product = Product::create($validated);

        // Guardar imágenes (hasta 3)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                if ($index < 3) {
                    $this->saveImage($image, $product->id, $index + 1);
                }
            }
        }

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

    // GET /api/products/{id}
    public function show($id)
    {
        $product = Product::findOrFail($id);
        
        $product->image_urls = $product->image_urls; // usa el accessor del modelo

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

        // Si llegan imágenes nuevas, se reemplazan
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                if ($index < 3) {
                    $this->saveImage($image, $id, $index + 1);
                }
            }
        }

        return response()->json([
            'message' => 'Producto actualizado correctamente',
            'product' => $product
        ]);
    }

    // DELETE /api/products/{id}
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Eliminar imágenes
        for ($i = 1; $i <= 3; $i++) {
            $this->deleteImage($id, $i);
        }

        $product->delete();

        return response()->json(['message' => 'Producto e imágenes eliminadas correctamente']);
    }

    // GET /api/image/product/{id}/{num}
    public function getImage($id, $num)
    {
        $filename = "product_{$id}_{$num}.webp";
        $path = storage_path("app/images/{$filename}");

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    // Guardar imagen con formato webp
    protected function saveImage($image, $id, $num)
    {
        $img = Image::make($image)->encode('webp', 90);
        $filename = "product_{$id}_{$num}.webp";
        $path = storage_path("app/images/{$filename}");
        $img->save($path);
    }

    // Eliminar imagen
    protected function deleteImage($id, $num)
    {
        $filename = "product_{$id}_{$num}.webp";
        $path = storage_path("app/images/{$filename}");

        if (file_exists($path)) {
            unlink($path);
        }
    }
}
