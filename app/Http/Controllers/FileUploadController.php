<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    /**
     * Upload an image file
     */
    public function uploadImage(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            ]);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                
                // Generate unique filename
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                
                // Store in public/images directory
                $path = $file->storeAs('public/images', $filename);
                
                // Get public URL
                $imageUrl = Storage::url('images/' . $filename);
                
                return response()->json([
                    'success' => true,
                    'image_url' => $imageUrl,
                    'message' => 'Image uploaded successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No image file provided'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }
}