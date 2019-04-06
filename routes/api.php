<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::apiResource('artists', 'ArtistController');
// Route::apiResource('songs', 'SongController');
// Route::apiResource('albums', 'AlbumController');

// AuthController inside namespace Auth
// all api route has prefix 'auth': /api/auth/login
Route::middleware('api')->namespace('Auth')->prefix('auth')->group(function() { 
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me'); 
});

Route::middleware('jwt.auth')->group(function() {
    Route::apiResource('albums', 'AlbumController');
    Route::apiResource('songs', 'SongController');
    Route::apiResource('artists', 'ArtistController');
});

Route::middleware(['jwt.auth', 'can:manage-users'])->group(function() {
    // Routes for managing users (not developed in the practical exercise)
});

Route::middleware(['jwt.auth', 'can:manage-artists'])->group(function() {

    Route::apiResource('artists', 'ArtistController')->only([
        'store',
        'update',
    ]);
});

Route::middleware(['jwt.auth', 'can:manage-albums'])->group(function() {

    Route::apiResource('albums', 'AlbumController')->only([
        'store',
        'update',
    ]);
});

Route::middleware(['jwt.auth', 'can:manage-songs'])->group(function() {

    Route::apiResource('songs', 'SongController')->only([
        'store',
        'update',
    ]);
});

Route::middleware(['jwt.auth', 'can:view-all'])->group(function() {

    Route::apiResource('artists', 'ArtistController')->only([
        'index',
        'show',
    ]);
    Route::apiResource('albums', 'AlbumController')->only([
        'index',
        'show',
    ]);
    Route::apiResource('songs', 'SongController')->only([
        'index',
        'show',
    ]);
});
