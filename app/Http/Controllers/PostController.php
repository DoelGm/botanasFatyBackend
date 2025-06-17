<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    // Obtener todos los posts con sus imágenes
    public function index()
    {
        $posts = Post::with('image')->get()->all();
        return response()->json($posts);
    }

    // Mostrar un solo post con sus imágenes
    public function show($id)
    {
        $post = Post::with('image')->findOrFail($id);
        return response()->json($post);
    }

    // Crear un nuevo post
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = Post::create($validated);

        return response()->json([
            'message' => 'Post creado exitosamente',
            'post' => $post
        ], 201);
    }

    // Subir imágenes a Cloudinary y asociarlas al post
    public function uploadImages(Request $request, $postId)
    {
        $request->validate([
            'image.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $post = Post::findOrFail($postId);
        if ($request->hasFile('image')) {
           
                try {
                    // Subir imagen a Cloudinary
                    $path = Storage::disk('cloudinary')->putFile("posts/$postId", $request->file('image'));
                    $url = Storage::disk('cloudinary')->url($path);

                    // Guardar en la base de datos
                  $urlImage =  $post->image()->create([
                        'cloudinary_url' => $url,
                        'cloudinary_public_id' => $path,
                    ]);
                    Log::info("Imagen guardada en BD para post $postId: $url");
                } catch (\Exception $e) {
                    Log::error("Error subiendo imagen: " . $e->getMessage());
                    return response()->json([
                        'error' => 'Error al subir la imagen'
                    ], 500);
                }
        }

        return response()->json([
            'message' => 'Imágenes subidas correctamente',
            'image' => $urlImage 
        ], 201);
    }

    // Actualizar post
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
        ]);

        $post = Post::findOrFail($id);
        $post->update($validated);

        return response()->json([
            'message' => 'Post actualizado correctamente',
            'post' => $post
        ]);
    }

    // Eliminar post y sus imágenes
    public function destroy($id)
    {
        $post = Post::with('images')->findOrFail($id);

        // Eliminar imágenes de Cloudinary y base de datos
        foreach ($post->images as $img) {
            try {
                Storage::disk('cloudinary')->delete($img->cloudinary_public_id);
            } catch (\Exception $e) {
                Log::warning("No se pudo borrar la imagen de Cloudinary: " . $e->getMessage());
            }
            $img->delete();
        }

        $post->delete();

        return response()->json(['message' => 'Post e imágenes eliminados correctamente']);
    }

    // Eliminar solo una imagen
    public function deleteImage($imageId)
    {
        $image = PostImage::findOrFail($imageId);

        try {
            Storage::disk('cloudinary')->delete($image->cloudinary_public_id);
        } catch (\Exception $e) {
            Log::warning("No se pudo borrar la imagen de Cloudinary: " . $e->getMessage());
        }

        $image->delete();

        return response()->json(['message' => 'Imagen eliminada correctamente']);
    }


public function updateImages(Request $request, $postId)
{
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    $image = $request->file('image');

    $post = Post::with('image')->findOrFail($postId);

    // Subir la nueva imagen a Cloudinary
    $path = Storage::disk('cloudinary')->putFile("posts/$postId", $image);
    $url = Storage::disk('cloudinary')->url($path);

    // Obtener la imagen existente (hasOne)
    $existingImage = $post->image;

    Log::info('Post ID: ' . $postId);
    Log::info('Cloudinary path: ' . $path);
    Log::info('Imagen relacionada:', [$existingImage]);

    if ($existingImage) {
        Log::info('Imagen existente encontrada. ID: ' . $existingImage->id);

        // Eliminar la imagen anterior de Cloudinary
        Storage::disk('cloudinary')->delete($existingImage->cloudinary_public_id);

        // Actualizar la imagen existente
        $existingImage->update([
            'cloudinary_url' => $url,
            'cloudinary_public_id' => $path,
        ]);

        Log::info('Imagen actualizada.');
    } else {
        // Crear nueva imagen si no hay una existente
        $post->image()->create([
            'cloudinary_url' => $url,
            'cloudinary_public_id' => $path,
        ]);

        Log::info('Imagen creada.');
    }

    return response()->json(['message' => 'Imagen actualizada correctamente.'], 201);
}


}
