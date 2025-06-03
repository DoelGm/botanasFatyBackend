<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustumerController;
use App\Http\Controllers\packagesController;
use App\Http\Controllers\ProductController;

use App\Http\Controllers\ImgurController;//ImgurController

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
Route::get('/products/{id}', [ProductController::class, 'show'] );
Route::post('/products', [ProductController::class, 'store'] );
Route::put('/products/{id}', [ProductController::class, 'update'] );
Route::delete('/products/{id}', [ProductController::class, 'destroy'] );
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::get('/categories', [CategoryController::class, 'index']);


Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);
Route::post('/posts', [PostController::class, 'store']);
Route::put('/posts/{id}', [PostController::class, 'update']);
Route::delete('/posts/{id}', [PostController::class, 'destroy']);

Route::get('/upload', [ImgurController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
   
    Route::post('/upload', [ImgurController::class, 'upload']);

});// ImgurController




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