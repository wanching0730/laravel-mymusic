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