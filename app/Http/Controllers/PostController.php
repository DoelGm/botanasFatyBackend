<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // GET /api/posts
    public function index()
    {
        $posts = Post::all();

        // Agregar URL de imagen a cada post
        foreach ($posts as $post) {
            $post->image_url = url('/api/image/post/' . $post->id);
        }

        return response()->json($posts);
    }

    // POST /api/posts
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg',
        ]);

        // Crear el post
        $post = Post::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category_id' => $validated['category_id'] ?? null,
        ]);

        // Procesar y guardar imagen
        $this->saveImage($request->file('image'), 'post', $post->id);

        return response()->json([
            'message' => 'Post creado con éxito',
            'post' => $post
        ], 201);
    }

    // GET /api/posts/{id}
    public function show($id)
    {
        $post = Post::findOrFail($id);
        $post->image_url = url('/api/image/post/' . $post->id);

        return response()->json($post);
    }

    // PUT /api/posts/{id}
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        $post = Post::findOrFail($id);
        $post->update($validated);

        // Si se sube una nueva imagen, reemplazarla
        if ($request->hasFile('image')) {
            $this->deleteImage('post', $id);
            $this->saveImage($request->file('image'), 'post', $id);
        }

        return response()->json([
            'message' => 'Post actualizado correctamente',
            'post' => $post
        ]);
    }

    // DELETE /api/posts/{id}
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        // Eliminar imagen si existe
        $this->deleteImage('post', $id);

        $post->delete();

        return response()->json(['message' => 'Post e imagen eliminados correctamente']);
    }

    // Función para guardar imagen .webp
    protected function saveImage($image, $type, $id)
    {
        $img = Image::make($image)->encode('webp', 90);
        $filename = $type . '_' . $id . '.webp';
        $path = storage_path('app/images/' . $filename);
        $img->save($path);
    }

    // Función para eliminar imagen .webp
    protected function deleteImage($type, $id)
    {
        $filename = $type . '_' . $id . '.webp';
        $path = storage_path('app/images/' . $filename);

        if (file_exists($path)) {
            unlink($path);
        }
    }
}
