<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PostController extends Controller
{

    public function index()
    {
        $posts = Post::with(['imagenes'])->get();

        return response()->json($posts);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ✅ Validamos datos del post y las imágenes
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'link.*' => 'image|max:2048',
            'category_id' => 'nullable|exists:categories,id', // Aseguramos que la categoría exista
        ]);

        $uploadedImages = [];

        // ✅ Verificamos si se subieron imágenes
        if ($request->hasFile('link')) {
            foreach ($request->file('link') as $file) {
                $imageData = base64_encode(file_get_contents($file->getRealPath()));

                // Subir a Imgur
                $response = Http::withHeaders([
                    'Authorization' => 'Client-ID f0d51ed40f1414e', // ⚠️ Usa tu propio Client-ID
                ])->asForm()->post('https://api.imgur.com/3/image', [
                    'image' => $imageData,
                    'type' => 'base64',
                ]);

                if ($response->successful()) {
                    $data = $response->json()['data'];

                    // Guardar en la tabla imagenes
                    $imgId = DB::table('imagenes')->insertGetId([
                        'link' => $data['link'],
                        'deletehash' => $data['deletehash'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Guardar el link en el array de imágenes
                    $uploadedImages[] = [
                        'img_id' => $imgId,
                        'link' => $data['link'],
                        'deletehash' => $data['deletehash'],
                    ];
                }
            }
        }

        // ✅ Crear el Post (relaciona con la primera imagen o con null si no hay imágenes)
        $post = new Post();
        $post->title = $validatedData['title'];
        $post->content = $validatedData['content'];
        $post->img_id = count($uploadedImages) > 0 ? $uploadedImages[0]['img_id'] : null;
        $post->category_id = $validatedData['category_id'] ?? null; // Aseguramos que category_id sea nullable
        $post->save();

        return response()->json([
            'message' => 'Post creado con éxito!',
            'post' => $post,
            'imagenes' => $uploadedImages,
        ], 201);
    }

    // Otras funciones las dejas igual
}
