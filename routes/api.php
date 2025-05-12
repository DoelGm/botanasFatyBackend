<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
// Route::apiResource('/categories', CategoryController::class);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/products', [ProductController::class, 'index'] );
Route::get('/products/{id}', [ProductController::class, 'show'] );
Route::post('/products', [ProductController::class, 'store'] );
Route::put('/products/{id}', [ProductController::class, 'update'] );
Route::delete('/products/{id}', [ProductController::class, 'destroy'] );
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});