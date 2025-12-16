<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Artisan;

Route::controller(MainController::class)->group(function () {
    Route::get('/', 'storeFront')->name('storeFront');
});

// Routes cho sản phẩm
Route::controller(ProductController::class)->group(function () {
    Route::get('/san-pham', 'categories')->name('products.categories');
    Route::get('/san-pham/danh-muc/{slug}', 'category')->name('products.category');
    Route::get('/san-pham/{slug}', 'show')->name('products.show');
});

// Routes cho bài viết
Route::controller(PostController::class)->group(function () {
    Route::get('/bai-viet/chuyen-muc/{slug}', 'category')->name('posts.category');
    Route::get('/bai-viet/chuyen-muc', 'categories')->name('posts.categories');
    Route::get('/bai-viet/{slug}', 'show')->name('posts.show');
    Route::get('/bai-viet', 'index')->name('posts.index');
});



// SEO routes
Route::controller(SitemapController::class)->group(function () {
    Route::get('/sitemap.xml', 'index')->name('sitemap');
    Route::get('/robots.txt', 'robots')->name('robots');
});

Route::get('/run-storage-link', function () {
    try {
        Artisan::call('storage:link');
        return response()->json(['message' => 'Storage linked successfully!'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
