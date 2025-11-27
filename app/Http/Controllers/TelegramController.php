<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TelegramController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Get Telegram configuration status for the authenticated admin
     */
    public function status(Request $request): JsonResponse
    {
        try {
            $admin = $request->user();
            
            $isConfigured = !empty($admin->telegram_bot_token) && !empty($admin->telegram_chat_id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'configured' => $isConfigured,
                    'enabled' => $admin->telegram_enabled ?? false,
                    'bot_token_set' => !empty($admin->telegram_bot_token),
                    'chat_id_set' => !empty($admin->telegram_chat_id),
                    // Don't expose actual tokens/IDs for security
                    'bot_token' => $admin->telegram_bot_token ? '***' . substr($admin->telegram_bot_token, -4) : null,
                    'chat_id' => $admin->telegram_chat_id ? '***' . substr($admin->telegram_chat_id, -4) : null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get Telegram status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send test message using admin's Telegram settings
     */
    public function sendTest(Request $request): JsonResponse
    {
        try {
            $admin = $request->user();
            
            if (!$admin->telegram_enabled) {
                return response()->json([
                    'success' => false,
                    'message' => 'Telegram notifications are disabled. Please enable them first.'
                ], 400);
            }
            
            if (!$admin->telegram_bot_token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bot token is not configured. Please add your bot token.'
                ], 400);
            }
            
            if (!$admin->telegram_chat_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat ID is not configured. Please add your chat ID.'
                ], 400);
            }
            
            // Create service instance with admin settings
            $telegramService = new \App\Services\TelegramService($admin);
            $result = $telegramService->sendTestMessage();
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test message sent successfully! Check your Telegram.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send test message. Please check the logs for details.'
                ], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Telegram test error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Telegram settings for the authenticated admin
     */
    public function updateSettings(Request $request): JsonResponse
    {
        try {
            $admin = $request->user();
            
            $validated = $request->validate([
                'bot_token' => 'nullable|string|max:255',
                'chat_id' => 'nullable|string|max:255',
                'enabled' => 'boolean',
            ]);

            // Update admin's Telegram settings
            $admin->telegram_bot_token = $validated['bot_token'] ?? $admin->telegram_bot_token;
            $admin->telegram_chat_id = $validated['chat_id'] ?? $admin->telegram_chat_id;
            $admin->telegram_enabled = $validated['enabled'] ?? $admin->telegram_enabled ?? false;
            $admin->save();

            return response()->json([
                'success' => true,
                'message' => 'Telegram settings updated successfully!',
                'data' => [
                    'enabled' => $admin->telegram_enabled,
                    'bot_token_set' => !empty($admin->telegram_bot_token),
                    'chat_id_set' => !empty($admin->telegram_chat_id),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings: ' . $e->getMessage()
            ], 500);
        }
    }
}
