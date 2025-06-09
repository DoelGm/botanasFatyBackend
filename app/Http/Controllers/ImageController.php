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
        'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    $product = Product::findOrFail($productId);
    $uploadedImages = [];

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            try {
                // Subir imagen a Cloudinary
                $path = Storage::disk('cloudinary')->putFile('products/' . $productId, $image);
                $url = Storage::disk('cloudinary')->url($path);

                // Guardar imagen en la base de datos
                $uploadedImages[] = $product->images()->create([
                    'cloudinary_url' => $url,
                    'cloudinary_public_id' => $path, // Guardamos el path como public_id para poder borrarlo después
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
    $product = Product::with('images')->get()->findOrFail($productId);
    return response()->json($product);
}
public function showAll()
{
    $product = Product::with('images')->get();
    return response()->json($product);
}
public function updateImages(Request $request, $productId)
{
    $product = Product::findOrFail($productId);

    // Eliminar imágenes existentes en Cloudinary y en la base de datos
    foreach ($product->images as $img) {
        try {
            Storage::disk('cloudinary')->delete($img->public_id);
        } catch (\Exception $e) {
            \Log::warning("No se pudo borrar la imagen de Cloudinary: " . $e->getMessage());
        }
        $img->delete();
    }

    // Subir nuevas imágenes
    return $this->uploadImages($request, $productId);
}


public function deleteImage($imageId)
{
    $image = ProductImage::findOrFail($imageId);
    Storage::disk('cloudinary')->delete($image->public_id);
    $image->delete();
    return response()->json(['message' => 'Imagen eliminada']);
}


}
