<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomPlayerController;
use App\Http\Controllers\UserController;
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

Route::get('auth/request-key', [AuthController::class, 'sendEmail']);
Route::post('auth/login', [AuthController::class, 'login']);
Route::put('auth/recover-password', [AuthController::class, 'resetPassowrd']);
//change password
Route::put('auth/{userId}/change-password', [AuthController::class, 'changePassword']);

Route::resource('rooms', RoomController::class);
Route::resource('users', UserController::class);
Route::put('room-players/{roomId}/kick/{playerId}', [RoomPlayerController::class, 'kickPlayer']);
Route::put('room-players/{roomId}/leave/{playerId}', [RoomPlayerController::class, 'leftPlayer']);
Route::put('room-players/{roomId}/join-player/{playerId}', [RoomPlayerController::class, 'joinPlayer']);

// join by room code
Route::post('player/{playerId}/join-room', [RoomPlayerController::class, 'joinPlayerByRoomCode']);

Route::get('room-players/{playerId}/stats', [RoomPlayerController::class, 'stats']);
Route::get('room-players/{playerId}/history', [RoomPlayerController::class, 'history']);

Route::resource('locations', LocationController::class);
Route::resource('room-players', RoomPlayerController::class);
Route::resource('categories', CategoryController::class);
