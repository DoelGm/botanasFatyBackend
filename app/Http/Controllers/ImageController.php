<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class ImageController extends Controller
{

public function uploadImages(Request $request, $productId)
{
    if (!config('cloudinary.cloud_url')) {
        return response()->json(['error' => 'Cloudinary no está configurado'], 500);
    }

    $request->validate([
        'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5048',
    ]);

    $product = Product::findOrFail($productId);
    $uploadedImages = [];

    $positions = $request->input('positions', []); // <-- obtener posiciones

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $image) {
            try {
                $position = isset($positions[$index]) ? intval($positions[$index]) : $index;

                // Subir imagen a Cloudinary
                $path = Storage::disk('cloudinary')->putFile("products/$productId", $image);
                $url = Storage::disk('cloudinary')->url($path);

                // Guardar imagen en la base de datos
                $uploadedImages[] = $product->images()->create([
                    'cloudinary_url' => $url,
                    'cloudinary_public_id' => $path,
                    'position' => $position,
                ]);
            } catch (\Exception $e) {
                \Log::error("Error subiendo imagen a Cloudinary: " . $e->getMessage());
                return response()->json([
                    'error' => 'Error al subir la imagen: ' . $e->getMessage()
                ], 500);
            }
        }
    }

    return response()->json([
        'message' => 'Imágenes subidas correctamente',
        'images' => $uploadedImages
    ], 201);
}


public function show($productId)
{
    $product = Product::with('images')->findOrFail($productId);
    return response()->json($product);
}
public function showAll()
{
    $product = Product::with('images')->get();
    return response()->json($product);
}
public function updateImages(Request $request, Product $product)
{
    $request->validate([
        'images' => 'required|array',
        'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    $existingImages = $product->images->keyBy('position'); // Mapea por posición

    foreach ($request->file('images') as $position => $image) {
        if ($image) {
            $existing = $existingImages->get($position);

            $path = Storage::disk('cloudinary')->putFile("products/{$product->id}", $image);
            $url = Storage::disk('cloudinary')->url($path);

            if ($existing) {
                Storage::disk('cloudinary')->delete($existing->cloudinary_public_id);
                $existing->update([
                    'cloudinary_url' => $url,
                    'cloudinary_public_id' => $path
                ]);
            } else {
                $product->images()->create([
                    'cloudinary_url' => $url,
                    'cloudinary_public_id' => $path,
                    'position' => $position
                ]);
            }
        }
    }

    return response()->json([
        'message' => 'Imágenes actualizadas correctamente',
        'images' => $product->fresh()->images
    ]);
}




public function deleteImage($imageId)
{
   $product = Product::with('images')->findOrFail($imageId);

    // Borrar imágenes de Cloudinary primero
    foreach ($product->images as $image) {
        if ($image->cloudinary_public_id) {
            Storage::disk('cloudinary')->delete($image->cloudinary_public_id);
        }
    }
     return response()->json(['message' => 'Producto e imágenes eliminadas correctamente']);
}


}
