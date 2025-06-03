<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ImgurController extends Controller
{
    public function index()
    {
        $imagenes = DB::table('imagenes')->get();

        return response()->json($imagenes);
    }
    // ✅ Subir imagen a Imgur
   public function upload(Request $request){
    $request->validate([
        'link' => 'required|array',  // ✅ Validar que es un array
        'link.*' => 'image|max:2048',  // ✅ Cada elemento es una imagen
    ]);

    $files = $request->file('link');  // ✅ Ya es un array de archivos
    $uploadedImages = [];  // ✅ Aquí guardaremos las imágenes subidas

    foreach ($files as $file) {  // ✅ Recorrer cada imagen
        $imageData = base64_encode(file_get_contents($file->getRealPath()));

        $response = Http::withHeaders([
            'Authorization' => 'Client-ID f0d51ed40f1414e',  // Reemplaza con tu Client-ID
        ])->asForm()->post('https://api.imgur.com/3/image', [
            'image' => $imageData,
            'type' => 'base64',
        ]);

        if ($response->successful()) {
            $data = $response->json()['data'];

            // Guardar en la base de datos
            DB::table('imagenes')->insert([
                'link' => $data['link'],
                'deletehash' => $data['deletehash'],
                'created_at' => now(),
            ]);

            $uploadedImages[] = [
                'link' => $data['link'],
                'deletehash' => $data['deletehash'],
            ];
        } else {
            // ✅ Si falla, regresar el error de esta imagen y detener todo
            return response()->json([
                'message' => 'Error al subir la imagen a Imgur',
                'error' => $response->json(),
            ], 500);
        }
    }

        // ✅ Devolver todas las imágenes subidas
        return response()->json([
            'message' => 'Imágenes subidas con éxito!',
            'imagenes' => $uploadedImages,
        ]);
        }
    // ✅ Mostrar una imagen específica
    public function show($id)
    {
        $imagen = DB::table('imagenes')->where('id', $id)->first();

        if ($imagen) {
            return response()->json($imagen);
        } else {
            return response()->json(['message' => 'Imagen no encontrada'], 404);
        }
    }

    // ✅ Actualizar imagen (subir una nueva y eliminar la anterior)
    public function update(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        $imagen = DB::table('imagenes')->where('id', $id)->first();

        if (!$imagen) {
            return response()->json(['message' => 'Imagen no encontrada'], 404);
        }

        $file = $request->file('link')[0];  

        $imageData = base64_encode(file_get_contents($image->getRealPath()));

        $response = Http::withHeaders([
            'Authorization' => 'Client-ID TU_CLIENT_ID_AQUI',
        ])->asForm()->post('https://api.imgur.com/3/image', [
            'image' => $imageData,
            'type' => 'base64',
        ]);

        if ($response->successful()) {
            $data = $response->json()['data'];

            // Eliminar la imagen anterior en Imgur
            Http::withHeaders([
                'Authorization' => 'Client-ID TU_CLIENT_ID_AQUI',
            ])->delete("https://api.imgur.com/3/image/{$imagen->deletehash}");

            // Actualizar la base de datos
            DB::table('imagenes')->where('id', $id)->update([
                'link' => $data['link'],
                'deletehash' => $data['deletehash'],
                'updated_at' => now(),
            ]);

            return response()->json([
                'message' => 'Imagen actualizada con éxito!',
                'link' => $data['link'],
                'deletehash' => $data['deletehash'],
            ]);
        } else {
            return response()->json([
                'message' => 'Error al actualizar la imagen en Imgur',
                'error' => $response->json(),
            ], 500);
        }
    }

    // ✅ Eliminar imagen
    public function delete($id)
    {
        $imagen = DB::table('imagenes')->where('id', $id)->first();

        if (!$imagen) {
            return response()->json(['message' => 'Imagen no encontrada'], 404);
        }

        // Eliminar en Imgur
        $response = Http::withHeaders([
            'Authorization' => 'Client-ID TU_CLIENT_ID_AQUI',
        ])->delete("https://api.imgur.com/3/image/{$imagen->deletehash}");

        if ($response->successful()) {
            // Eliminar en la base de datos
            DB::table('imagenes')->where('id', $id)->delete();

            return response()->json(['message' => 'Imagen eliminada con éxito!']);
        } else {
            return response()->json([
                'message' => 'Error al eliminar la imagen en Imgur',
                'error' => $response->json(),
            ], 500);
        }
    }
}
