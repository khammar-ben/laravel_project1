<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OfferController extends Controller
{
    /**
     * Get all public offers
     */
    public function index(): JsonResponse
    {
        $offers = Offer::public()
            ->active()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $offers,
            'message' => 'Offers retrieved successfully'
        ]);
    }

    /**
     * Get a specific offer by ID
     */
    public function show($id): JsonResponse
    {
        $offer = Offer::public()->active()->find($id);

        if (!$offer) {
            return response()->json([
                'success' => false,
                'message' => 'Offer not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $offer,
            'message' => 'Offer retrieved successfully'
        ]);
    }

    /**
     * Get offers by type
     */
    public function byType($type): JsonResponse
    {
        $offers = Offer::public()
            ->active()
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $offers,
            'message' => 'Offers retrieved successfully'
        ]);
    }

    /**
     * Validate offer code
     */
    public function validateCode(Request $request): JsonResponse
    {
        $request->validate([
            'offer_code' => 'required|string',
            'guests' => 'required|integer|min:1',
            'nights' => 'required|integer|min:1'
        ]);

        $offer = Offer::public()
            ->active()
            ->where('offer_code', $request->offer_code)
            ->first();

        if (!$offer) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid offer code'
            ], 400);
        }

        // Check if offer meets requirements
        if ($request->guests < $offer->min_guests) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum guests requirement not met'
            ], 400);
        }

        if ($offer->min_nights && $request->nights < $offer->min_nights) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum nights requirement not met'
            ], 400);
        }

        // Check usage limits
        if ($offer->max_uses && $offer->used_count >= $offer->max_uses) {
            return response()->json([
                'success' => false,
                'message' => 'Offer usage limit reached'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $offer,
            'message' => 'Offer is valid'
        ]);
    }

    /**
     * Get available offers for booking
     */
    public function available(Request $request): JsonResponse
    {
        $request->validate([
            'guests' => 'required|integer|min:1',
            'nights' => 'required|integer|min:1'
        ]);

        $offers = Offer::public()
            ->active()
            ->where('min_guests', '<=', $request->guests)
            ->where(function($query) use ($request) {
                $query->whereNull('min_nights')
                      ->orWhere('min_nights', '<=', $request->nights);
            })
            ->where(function($query) {
                $query->whereNull('max_uses')
                      ->orWhereRaw('used_count < max_uses');
            })
            ->orderBy('discount_value', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $offers,
            'message' => 'Available offers retrieved successfully'
        ]);
    }
}
