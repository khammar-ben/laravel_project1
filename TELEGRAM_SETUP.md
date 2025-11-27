# Telegram Notifications Setup Guide

This guide will help you set up Telegram notifications for new booking requests in your hotel management system.

## Step 1: Create a Telegram Bot

1. Open Telegram and search for `@BotFather`
2. Start a conversation with BotFather
3. Send the command `/newbot`
4. Follow the prompts to:
   - Choose a name for your bot (e.g., "Happy Hostel Notifications")
   - Choose a username for your bot (e.g., "happy_hostel_bot")
5. BotFather will give you a **Bot Token** - save this!

## Step 2: Get Your Chat ID

1. Start a conversation with your new bot
2. Send any message to the bot (e.g., "Hello")
3. Open a new browser tab and go to: `https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates`
4. Look for your message in the response and find the `"chat":{"id":123456789}` - this is your **Chat ID**

## Step 3: Configure in Admin Panel

1. Go to your admin dashboard
2. Click on the "Notifications" tab
3. Enter your Bot Token and Chat ID
4. Enable notifications
5. Click "Save Settings"
6. Click "Send Test Message" to verify it works

## Step 4: Test Notifications

1. Create a test booking through your booking form
2. You should receive a notification in Telegram with:
   - Guest details
   - Room information
   - Check-in/out dates
   - Total amount
   - Booking status

## Environment Variables

Add these to your `.env` file:

```env
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_CHAT_ID=your_chat_id_here
TELEGRAM_NOTIFICATIONS_ENABLED=true
```

## Troubleshooting

- **Bot not responding**: Check if the bot token is correct
- **No notifications**: Verify the chat ID and that you've started a conversation with the bot
- **Test message fails**: Make sure both bot token and chat ID are properly set

## Security Notes

- Keep your bot token secure and never share it publicly
- The bot will only send messages to the configured chat ID
- You can disable notifications anytime from the admin panel
