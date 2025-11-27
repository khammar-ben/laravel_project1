<?php

use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

// Public routes for room booking
Route::get('/api/rooms/public', [RoomController::class, 'publicIndex']);
Route::get('/api/available-rooms', [BookingController::class, 'getAvailableRooms']);
Route::post('/api/bookings', [BookingController::class, 'store']);

require __DIR__.'/auth.php';
