<?php

use App\Http\Controllers\FeedController;
use App\Http\Controllers\PostController;
use App\Http\Middleware\DetectBlogByHostMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', FeedController::class)
    ->middleware(DetectBlogByHostMiddleware::class)
    ->name('feed');
Route::get('/posts', FeedController::class)
    ->middleware(DetectBlogByHostMiddleware::class)
    ->name('posts');
Route::get('/shorts', FeedController::class)
    ->middleware(DetectBlogByHostMiddleware::class)
    ->name('shorts');
Route::get('/tags', FeedController::class)
    ->middleware(DetectBlogByHostMiddleware::class)
    ->name('tags');

Route::get('/post/{slug}', PostController::class)
    ->middleware(DetectBlogByHostMiddleware::class)
    ->name('post');
