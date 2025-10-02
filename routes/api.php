<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SongController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\Auth\RegisteredUserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('artists', ArtistController::class);
Route::get('/artists/search/name', [ArtistController::class, 'searchByName']);
Route::get('/artists/search/genre', [ArtistController::class, 'searchByGenre']);



Route::apiResource('albums', AlbumController::class);
Route::apiResource('songs', SongController::class);

Route::get('songs/search', [SongController::class, 'search']);

require __DIR__.'/auth.php';
