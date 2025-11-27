<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $botToken;
    protected $chatId;
    protected $enabled;

    public function __construct($admin = null)
    {
        // If admin is provided, use admin-specific settings
        if ($admin && $admin->telegram_bot_token) {
            $this->botToken = $admin->telegram_bot_token;
            $this->chatId = $admin->telegram_chat_id;
            $this->enabled = $admin->telegram_enabled ?? false;
        } else {
            // Fallback to global config
            $this->botToken = config('services.telegram.bot_token');
            $this->chatId = config('services.telegram.chat_id');
            $this->enabled = config('services.telegram.enabled');
        }
    }

    /**
     * Send a message to Telegram
     */
    public function sendMessage(string $message, array $options = [])
    {
        if (!$this->enabled || !$this->botToken || !$this->chatId) {
            Log::warning('Telegram notifications disabled or missing configuration');
            return false;
        }

        try {
            $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
            
            $data = array_merge([
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ], $options);

            $response = Http::timeout(10)->post($url, $data);

            if ($response->successful()) {
                Log::info('Telegram message sent successfully');
                return true;
            } else {
                Log::error('Failed to send Telegram message', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Telegram service error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a booking notification
     */
    public function sendBookingNotification($booking)
    {
        $message = $this->formatBookingMessage($booking);
        return $this->sendMessage($message);
    }

    /**
     * Format booking message for Telegram
     */
    protected function formatBookingMessage($booking)
    {
        $statusEmoji = $this->getStatusEmoji($booking->status);
        $roomInfo = $booking->room ? "{$booking->room->room_number} ({$booking->room->room_type})" : 'N/A';
        
        $message = "🔔 <b>New Booking Request!</b>\n\n";
        $message .= "📋 <b>Booking ID:</b> {$booking->booking_reference}\n";
        $message .= "👤 <b>Guest:</b> {$booking->guest->first_name} {$booking->guest->last_name}\n";
        $message .= "📧 <b>Email:</b> {$booking->guest->email}\n";
        $message .= "📱 <b>Phone:</b> {$booking->guest->phone_number}\n";
        $message .= "🏨 <b>Room:</b> {$roomInfo}\n";
        $message .= "📅 <b>Check-in:</b> " . \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') . "\n";
        $message .= "📅 <b>Check-out:</b> " . \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') . "\n";
        $message .= "👥 <b>Guests:</b> {$booking->number_of_guests}\n";
        $message .= "💰 <b>Total:</b> $" . number_format($booking->total_amount, 2) . "\n";
        $message .= "{$statusEmoji} <b>Status:</b> " . ucfirst($booking->status) . "\n\n";
        $message .= "⏰ <b>Time:</b> " . \Carbon\Carbon::now()->format('M d, Y H:i:s');

        return $message;
    }

    /**
     * Get emoji for booking status
     */
    protected function getStatusEmoji($status)
    {
        return match($status) {
            'pending' => '⏳',
            'confirmed' => '✅',
            'checked_in' => '🏨',
            'checked_out' => '🚪',
            'cancelled' => '❌',
            default => '📋'
        };
    }

    /**
     * Send a test message
     */
    public function sendTestMessage()
    {
        $message = "🧪 <b>Test Message</b>\n\n";
        $message .= "✅ Telegram notifications are working correctly!\n";
        $message .= "⏰ " . \Carbon\Carbon::now()->format('M d, Y H:i:s');
        
        return $this->sendMessage($message);
    }

    /**
     * Check if Telegram is properly configured
     */
    public function isConfigured()
    {
        return $this->enabled && $this->botToken && $this->chatId;
    }
}
