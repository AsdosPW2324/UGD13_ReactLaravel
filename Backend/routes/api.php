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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/contents', [App\Http\Controllers\Api\ContentController::class, 'index']);
    Route::post('/contents', [App\Http\Controllers\Api\ContentController::class, 'store']);
    Route::get('/contents/{id}', [App\Http\Controllers\Api\ContentController::class, 'show']);
    Route::put('/contents/{id}', [App\Http\Controllers\Api\ContentController::class, 'update']);
    Route::delete('/contents/{id}', [App\Http\Controllers\Api\ContentController::class, 'destroy']);

    Route::get('/contents/user/{id}', [App\Http\Controllers\Api\ContentController::class, 'showContentbyUser']);

    Route::get('/favourites', [App\Http\Controllers\Api\FavouriteController::class, 'showUserFavouriteList']);
    Route::post('/favourites', [App\Http\Controllers\Api\FavouriteController::class, 'store']);
    Route::delete('/favourites/{id}', [App\Http\Controllers\Api\FavouriteController::class, 'deletefromFavourites']);

    Route::get('/watch_laters', [App\Http\Controllers\Api\WatchLaterController::class, 'showUserWatchLaterList']);
    Route::post('/watch_laters', [App\Http\Controllers\Api\WatchLaterController::class, 'store']);
    Route::delete('/watch_laters/{id}', [App\Http\Controllers\Api\WatchLaterController::class, 'deletefromWatchLater']);

    // Bonus
    Route::post("/contents/{id}/thumbnail", [App\Http\Controllers\Api\ContentController::class, "changeThumbnail"]);

    // Bonus Kelas A
    Route::get('/user', [App\Http\Controllers\Api\UserController::class, 'index']);
    Route::put('/user', [App\Http\Controllers\Api\UserController::class, 'update']);
    Route::post("/user/avatar", [App\Http\Controllers\Api\UserController::class, "updateFotoDanBio"]);

    Route::get('/reviews/{id}', [App\Http\Controllers\Api\ReviewController::class, 'showReviewofContent']);
    Route::post('/reviews', [App\Http\Controllers\Api\ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [App\Http\Controllers\Api\ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [App\Http\Controllers\Api\ReviewController::class, 'destroy']);

    Route::get('/comments/{id}', [App\Http\Controllers\Api\KomentarController::class, 'showCommentofContent']);
    Route::post('/comments', [App\Http\Controllers\Api\KomentarController::class, 'store']);
    Route::put('/comments/{id}', [App\Http\Controllers\Api\KomentarController::class, 'update']);
    Route::delete('/comments/{id}', [App\Http\Controllers\Api\KomentarController::class, 'destroy']);

});
