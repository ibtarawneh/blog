<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

use App\Http\Controllers\users\C_admin; 
    Route::post('/login', [C_admin::class, 'login']);
    Route::post('/register', [C_admin::class, 'register']);

    Route::middleware('auth:api')->prefix('Credential')->group(function () {
        Route::get('/', [C_admin::class, 'Credential']); 
    });




    use App\Http\Controllers\blog\C_blog;
    Route::get('/view/{id}', [C_blog::class, 'view']);
    Route::middleware('auth:api')->prefix('blogs')->group(function () {
        Route::get('/top/{userId}', [C_blog::class, 'topBlogsByUserId']);
        Route::get('/', [C_blog::class, 'index']); // Get all blogs
        Route::post('/', [C_blog::class, 'store']); // Create a new blog
        Route::get('/{id}', [C_blog::class, 'show']); // Get a single blog
        Route::get('/user/{id}', [C_blog::class, 'blog_user']); // Get a single blog
        Route::put('/', [C_blog::class, 'update']); // Update a blog
        Route::delete('/{id}', [C_blog::class, 'destroy']); // Delete a blog
    }); 
    
    use App\Http\Controllers\blog\C_comment;
    Route::middleware('auth:api')->prefix('comment')->group(function () {

        Route::apiResource('blogs', C_comment::class);

        // Additional routes for comments
        Route::post('{blog}', [C_comment::class, 'storeComment']);
        Route::get('{blog}', [C_comment::class, 'getComments']);
        Route::delete('{blog}/{comment}', [C_comment::class, 'destroyComment']);

    });


    
    Route::put('/blogs/ns', [C_blog::class, 'update']); // Update a blog