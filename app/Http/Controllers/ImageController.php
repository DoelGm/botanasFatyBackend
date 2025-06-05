<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Intervention\Image\Facades\Image;

class ImageController extends Controller
{
    public function getImage($type, $id)
    {
        $filename = $type . '_' . $id . '.webp';
        $path = storage_path('app/images/' . $filename);

        if (!file_exists($path)) {
            return response()->json(['message' => 'Imagen no encontrada.'], 404);
        }

        return response()->file($path);
    }
}
