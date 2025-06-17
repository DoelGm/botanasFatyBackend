<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\packagesController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TicketController;

use App\Http\Controllers\ImageController; //image

use App\Http\Controllers\PostController; //post

use App\Http\Controllers\UserController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();

});

Route::get('/products', [ProductController::class, 'index'] );
// Route::get('/products/{id}', [ProductController::class, 'show'] );
Route::get('/products/category/{id}', [ProductController::class, 'showByCategory'] );
Route::post('/products', [ProductController::class, 'store'] );
Route::put('/products/{id}', [ProductController::class, 'update'] );
Route::delete('/products/{id}', [ProductController::class, 'destroy'] );
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/tickets', [TicketController::class, 'store']);

// Subir imágenes a un producto
Route::post('/products/{id}/images', [ImageController::class, 'uploadImages']);

// Mostrar producto con sus imágenes
Route::get('/products/images', [ImageController::class, 'showAll']);

Route::get('/products/{id}/images', [ImageController::class, 'show']);


// Actualizar (reemplazar) todas las imágenes de un producto
Route::post('/products/{product}/updateImages', [ImageController::class, 'updateImages']);

// Eliminar una sola imagen por su ID
Route::delete('/products/images/{id}', [ImageController::class, 'deleteImage']);

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);
Route::post('/posts', [PostController::class, 'store']);
Route::put('/posts/{id}', [PostController::class, 'update']);
Route::delete('/posts/{id}', [PostController::class, 'destroy']);

Route::get('/posts-with-images', [PostController::class, 'showAll']);
Route::get('/posts/{postId}/with-images', [PostController::class, 'show']);
Route::post('/posts/{postId}/images', [PostController::class, 'uploadImages']);
Route::post('/posts/{postId}/updateImages', [PostController::class, 'updateImages']);
Route::delete('/images/{imageId}', [PostController::class, 'deleteImage']);



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/{id}', [CustomerController::class, 'show']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::put('/customers/{id}', [CustomerController::class, 'update']);
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/packages', [packagesController::class, 'index']);
    Route::get('/packages/{id}', [packagesController::class, 'show']);
    Route::post('/packages', [packagesController::class, 'store']);
    Route::put('/packages/{id}', [packagesController::class, 'update']);
    Route::delete('/packages/{id}', [packagesController::class, 'destroy']);
});
