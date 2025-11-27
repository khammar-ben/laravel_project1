# Telegram Configuration - Admin-Specific Setup

## Overview

Each admin now has their own Telegram bot configuration. When a booking is created for a room, only the room owner (admin) receives the Telegram notification using their own bot settings.

## Database Structure

The `admins` table includes:
- `telegram_bot_token` - Admin's Telegram bot token
- `telegram_chat_id` - Admin's chat ID  
- `telegram_enabled` - Enable/disable notifications (default: false)

## API Endpoints

### Get Telegram Status
```
GET /api/telegram/status
Authorization: Bearer {admin_token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "configured": true,
    "enabled": true,
    "bot_token_set": true,
    "chat_id_set": true,
    "bot_token": "***1234",  // Masked for security
    "chat_id": "***5678"     // Masked for security
  }
}
```

### Update Telegram Settings
```
POST /api/telegram/settings
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "bot_token": "123456789:ABCdefGHIjklMNOpqrsTUVwxyz",
  "chat_id": "123456789",
  "enabled": true
}
```

**Response:**
```json
{
  "success": true,
  "message": "Telegram settings updated successfully!",
  "data": {
    "enabled": true,
    "bot_token_set": true,
    "chat_id_set": true
  }
}
```

### Send Test Message
```
POST /api/telegram/test
Authorization: Bearer {admin_token}
```

**Response:**
```json
{
  "success": true,
  "message": "Test message sent successfully! Check your Telegram."
}
```

## How It Works

### Booking Notifications

1. **Booking Created:**
   - System finds the room owner (admin) via `room.user_id`
   - Checks if admin has `telegram_enabled = true`
   - Verifies `telegram_bot_token` and `telegram_chat_id` are set
   - Sends notification using admin's Telegram settings
   - Only the room owner receives the notification

2. **Notification Content:**
   - Booking reference
   - Guest details (name, email, phone)
   - Room information
   - Check-in/out dates
   - Number of guests
   - Total amount
   - Booking status

### Security Features

1. **Token Masking:**
   - API responses show only last 4 characters: `***1234`
   - Full tokens never exposed in responses

2. **Admin Isolation:**
   - Each admin's settings are completely separate
   - Admin A cannot see or modify Admin B's settings
   - Notifications are sent only to the room owner

3. **Validation:**
   - Bot token and chat ID are validated before saving
   - Test message verifies configuration works

## Setup Instructions

### Step 1: Create Telegram Bot

1. Open Telegram and search for `@BotFather`
2. Start conversation and send `/newbot`
3. Follow prompts to create bot
4. **Save the Bot Token** (format: `123456789:ABCdefGHIjklMNOpqrsTUVwxyz`)

### Step 2: Get Chat ID

1. Start a conversation with your new bot
2. Send any message (e.g., "Hello")
3. Open browser and visit:
   ```
   https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates
   ```
4. Find `"chat":{"id":123456789}` in the response
5. **Save the Chat ID**

### Step 3: Configure in Admin Panel

1. Login as admin
2. Navigate to Telegram Settings
3. Enter:
   - Bot Token
   - Chat ID
4. Enable notifications (toggle switch)
5. Click "Save Settings"
6. Click "Send Test Message" to verify

### Step 4: Test

1. Create a test booking for one of your rooms
2. Check Telegram for notification
3. Verify all details are correct

## Troubleshooting

### Bot Token Not Working
- Verify token is correct (no extra spaces)
- Check bot is active in BotFather
- Ensure bot hasn't been deleted

### Chat ID Not Working
- Make sure you've sent at least one message to the bot
- Verify chat ID is correct (should be numeric)
- Check you're using the right chat (personal vs group)

### No Notifications Received
- Check `telegram_enabled` is `true`
- Verify bot token and chat ID are set
- Check room belongs to you (`user_id` matches your admin ID)
- Review server logs for errors

### Test Message Fails
- Verify bot token format is correct
- Check chat ID is correct
- Ensure bot is not blocked
- Check internet connectivity

## Code Examples

### Using TelegramService with Admin

```php
// Get admin
$admin = Auth::user(); // or Admin::find($id)

// Create service with admin settings
$telegramService = new TelegramService($admin);

// Send message
$telegramService->sendMessage("Hello from admin!");

// Send booking notification
$telegramService->sendBookingNotification($booking);

// Send test message
$telegramService->sendTestMessage();
```

### Checking Configuration

```php
$admin = Auth::user();

if ($admin->telegram_enabled && 
    $admin->telegram_bot_token && 
    $admin->telegram_chat_id) {
    // Telegram is configured and enabled
    $telegramService = new TelegramService($admin);
    $telegramService->sendMessage("Notification");
}
```

## Security Notes

1. **Token Protection:**
   - Bot tokens are stored in database (encrypted at rest if using encryption)
   - Never expose full tokens in API responses
   - Only last 4 characters shown for verification

2. **Access Control:**
   - Only authenticated admins can update their own settings
   - Settings are isolated per admin
   - No admin can access another admin's settings

3. **Best Practices:**
   - Rotate bot tokens periodically
   - Use strong, unique tokens
   - Monitor for unauthorized access
   - Keep Telegram app updated

## Migration

The migration has been run. Telegram columns are now in the `admins` table:

```sql
ALTER TABLE admins 
ADD telegram_bot_token VARCHAR(255) NULL,
ADD telegram_chat_id VARCHAR(255) NULL,
ADD telegram_enabled BOOLEAN DEFAULT FALSE;
```

## Summary

✅ Each admin has their own Telegram configuration
✅ Notifications sent only to room owner
✅ Settings are isolated and secure
✅ Easy to configure via API
✅ Test functionality included
✅ Token masking for security




