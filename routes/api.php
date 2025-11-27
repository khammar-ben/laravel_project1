<?php

use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityBookingController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\RoomAvailabilityController;
use App\Http\Controllers\Api\VisitorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public booking routes (no auth required)
Route::middleware('api-public')->group(function () {
    Route::get('/test', function () {
        return response()->json(['message' => 'API is working']);
    });
    
    Route::options('/test', function () {
        return response()->json(['message' => 'CORS preflight']);
    });

    Route::options('/bookings', function () {
        return response()->json(['message' => 'CORS preflight']);
    });
    Route::get('/bookings/public', [BookingController::class, 'publicIndex']);
    Route::get('/available-rooms', [BookingController::class, 'getAvailableRooms']);
    Route::get('/rooms/public', [RoomController::class, 'publicIndex']);
    // Public room availability routes
    Route::get('/room-availability/available', [RoomAvailabilityController::class, 'getAvailableRooms']);
    Route::post('/room-availability/check', [RoomAvailabilityController::class, 'checkRoomAvailability']);
    Route::options('/room-availability/check', function () {
        return response()->json(['message' => 'CORS preflight']);
    });
    Route::get('/room-availability/calendar/{room_id}', [RoomAvailabilityController::class, 'getRoomAvailabilityCalendar']);
    Route::post('/room-availability/search', [RoomAvailabilityController::class, 'searchRooms']);
    Route::options('/room-availability/search', function () {
        return response()->json(['message' => 'CORS preflight']);
    });

    // Public activity routes (no auth required)
    Route::get('/activities/public', [ActivityController::class, 'publicIndex']);
    Route::get('/activities/{activity}/availability', [ActivityController::class, 'availability']);

    // Public offer routes (no auth required)
    Route::get('/offers', [App\Http\Controllers\Api\OfferController::class, 'index']);
    Route::get('/offers/{id}', [App\Http\Controllers\Api\OfferController::class, 'show']);
    Route::get('/offers/type/{type}', [App\Http\Controllers\Api\OfferController::class, 'byType']);
    Route::post('/offers/validate', [App\Http\Controllers\Api\OfferController::class, 'validateCode']);
    Route::get('/offers/available', [App\Http\Controllers\Api\OfferController::class, 'available']);
    Route::get('/offers/public', [OfferController::class, 'publicIndex']);

    // Public file upload (temporary for testing)
    Route::post('/upload-image', [FileUploadController::class, 'uploadImage']);

    // Public dashboard route for testing (temporary)
    Route::get('/dashboard/stats/public', [DashboardController::class, 'getStatsPublic']);
});

// Visitor authentication routes (no auth required)
Route::post('/visitor/register', [VisitorController::class, 'register']);
Route::post('/visitor/login', [VisitorController::class, 'login']);
Route::post('/visitor/forgot-password', [VisitorController::class, 'forgotPassword']);
Route::post('/visitor/reset-password', [VisitorController::class, 'resetPassword']);

// Visitor protected routes (auth required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/visitor/logout', [VisitorController::class, 'logout']);
    Route::get('/visitor/profile', [VisitorController::class, 'profile']);
    Route::get('/visitor/bookings', [VisitorController::class, 'bookings']);
    Route::put('/visitor/profile', [VisitorController::class, 'updateProfile']);
});

// Public booking routes with CSRF protection
Route::middleware('api-protected')->group(function () {
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::post('/activity-bookings', [ActivityBookingController::class, 'store']);
});

// Room management routes (protected)
Route::middleware(['api-protected', 'auth:sanctum'])->group(function () {
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('bookings', BookingController::class);
    Route::get('/bookings/my', [BookingController::class, 'myBookings']);
    
    // Activity management routes (Admin only)
    Route::apiResource('activities', ActivityController::class);
    Route::apiResource('activity-bookings', ActivityBookingController::class);
    
    // Offer management routes (Admin only)
    Route::apiResource('offers', OfferController::class);
    Route::get('/offers/statistics', [OfferController::class, 'statistics']);
    
    // Dashboard routes (Admin only)
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
    
    // Room availability management routes (Admin only)
    Route::prefix('room-availability')->group(function () {
        Route::get('/occupancy-summary', [RoomAvailabilityController::class, 'getOccupancySummary']);
        Route::post('/update-status', [RoomAvailabilityController::class, 'updateRoomStatus']);
        Route::post('/update-all-statuses', [RoomAvailabilityController::class, 'updateAllRoomStatuses']);
        Route::get('/needing-attention', [RoomAvailabilityController::class, 'getRoomsNeedingAttention']);
        Route::get('/upcoming-transitions', [RoomAvailabilityController::class, 'getUpcomingTransitions']);
        Route::get('/utilization-report', [RoomAvailabilityController::class, 'getRoomUtilizationReport']);
    });
    
    // Telegram notification routes
    Route::prefix('telegram')->group(function () {
        Route::get('/status', [TelegramController::class, 'status']);
        Route::post('/test', [TelegramController::class, 'sendTest']);
        Route::post('/settings', [TelegramController::class, 'updateSettings']);
    });
    
    // User management routes
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::post('/update-password', [UserController::class, 'updatePassword']);
    });
    
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    });
    
    // Alias for /me endpoint
    Route::get('/me', function (Request $request) {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    });
    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    });
});
