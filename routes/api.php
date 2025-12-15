<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebDesignController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// WebDesign API Routes
Route::prefix('webdesign')->group(function () {
    Route::get('/', [WebDesignController::class, 'index']);
    Route::get('/visible', [WebDesignController::class, 'visible']);
    Route::get('/export', [WebDesignController::class, 'export']);
    Route::get('/{sectionKey}', [WebDesignController::class, 'show']);

    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/clear-cache', [WebDesignController::class, 'clearCache']);
        Route::post('/reset', [WebDesignController::class, 'reset']);
    });
});
