<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\ReviewController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/user/register', [UserController::class, 'createUser']);
Route::post('/user/login', [UserController::class, 'loginUser']);


Route::middleware("auth:sanctum")->group(function () {
    Route::post('/reviews/books/{book_id}', [ReviewController::class, 'storeBookReview']);
});

Route::get('authors', [AuthorController::class, 'index']);
Route::get('authors/{author}', [AuthorController::class, 'show']);
Route::post('authors', [AuthorController::class, 'store']);
Route::put('authors/{author}', [AuthorController::class, 'update']);
Route::delete('authors/{author}', [AuthorController::class, 'destroy']);


Route::get('books', [BookController::class, 'index']);
Route::get('books/{book}', [BookController::class, 'show']);
Route::post('books', [BookController::class, 'store']);
Route::put('books/{book}', [BookController::class, 'update']);
Route::delete('books/{book}', [BookController::class, 'destroy']);


