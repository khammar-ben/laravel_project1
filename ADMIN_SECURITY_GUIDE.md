# Admin Security & Data Isolation Guide

## Overview

All admin endpoints are now secured to ensure each admin can only access and manage their own data. This includes rooms, bookings, activities, offers, and Telegram settings.

## Security Features Implemented

### 1. **Data Isolation by Admin ID**

All controllers filter data by the authenticated admin's ID:

- **Rooms**: Only shows rooms where `user_id = admin.id`
- **Bookings**: Only shows bookings for rooms owned by the admin
- **Activities**: Only shows activities where `user_id = admin.id`
- **Offers**: Only shows offers where `user_id = admin.id`
- **Dashboard**: Only shows statistics for admin's own data

### 2. **Authorization Checks**

All CRUD operations verify ownership:

- **View**: Admin can only view their own resources
- **Update**: Admin can only update their own resources
- **Delete**: Admin can only delete their own resources
- **Create**: New resources are automatically assigned to the admin

### 3. **Telegram Configuration (Per Admin)**

Each admin has their own Telegram settings stored in the `admins` table:

- `telegram_bot_token` - Admin's bot token
- `telegram_chat_id` - Admin's chat ID
- `telegram_enabled` - Enable/disable notifications

**Benefits:**
- Each admin receives notifications only for their own bookings
- Admins can configure their own Telegram bot
- Settings are isolated per admin

## API Endpoints Security

### Rooms (`/api/rooms`)
- ✅ `GET /api/rooms` - Only returns admin's rooms
- ✅ `POST /api/rooms` - Automatically assigns to admin
- ✅ `GET /api/rooms/{id}` - Only if room belongs to admin
- ✅ `PUT /api/rooms/{id}` - Only if room belongs to admin
- ✅ `DELETE /api/rooms/{id}` - Only if room belongs to admin + checks for active bookings

### Bookings (`/api/bookings`)
- ✅ `GET /api/bookings` - Only bookings for admin's rooms
- ✅ `GET /api/bookings/{id}` - Only if booking belongs to admin's room
- ✅ `PUT /api/bookings/{id}` - Only if booking belongs to admin's room
- ✅ `DELETE /api/bookings/{id}` - Only if booking belongs to admin's room

### Activities (`/api/activities`)
- ✅ `GET /api/activities` - Only admin's activities
- ✅ `POST /api/activities` - Automatically assigns to admin
- ✅ `GET /api/activities/{id}` - Only if activity belongs to admin
- ✅ `PUT /api/activities/{id}` - Only if activity belongs to admin
- ✅ `DELETE /api/activities/{id}` - Only if activity belongs to admin + checks for bookings

### Offers (`/api/offers`)
- ✅ `GET /api/offers` - Only admin's offers
- ✅ `POST /api/offers` - Automatically assigns to admin
- ✅ `GET /api/offers/{id}` - Only if offer belongs to admin
- ✅ `PUT /api/offers/{id}` - Only if offer belongs to admin
- ✅ `DELETE /api/offers/{id}` - Only if offer belongs to admin

### Dashboard (`/api/dashboard/stats`)
- ✅ Only shows statistics for admin's own:
  - Rooms
  - Bookings
  - Activities
  - Revenue
  - Occupancy rates

### Telegram (`/api/telegram/*`)
- ✅ `GET /api/telegram/status` - Shows admin's Telegram settings (masked)
- ✅ `POST /api/telegram/settings` - Updates admin's Telegram settings
- ✅ `POST /api/telegram/test` - Sends test message using admin's settings

## Telegram Configuration

### Setup Steps

1. **Create a Telegram Bot:**
   - Contact @BotFather on Telegram
   - Send `/newbot` command
   - Follow instructions to create bot
   - Save the **Bot Token**

2. **Get Chat ID:**
   - Start a conversation with your bot
   - Send any message
   - Visit: `https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates`
   - Find your `chat.id` in the response

3. **Configure in Admin Panel:**
   - Go to Telegram Settings
   - Enter Bot Token
   - Enter Chat ID
   - Enable notifications
   - Click "Send Test Message" to verify

### Database Structure

The `admins` table now includes:
```sql
telegram_bot_token VARCHAR(255) NULL
telegram_chat_id VARCHAR(255) NULL
telegram_enabled BOOLEAN DEFAULT FALSE
```

### How It Works

1. **Booking Created:**
   - System finds the room owner (admin)
   - Checks if admin has Telegram enabled
   - Sends notification using admin's bot token and chat ID

2. **Settings Update:**
   - Each admin updates their own settings
   - Settings are stored in `admins` table
   - No global configuration needed

## Security Best Practices

### ✅ Implemented

1. **Authentication Required:**
   - All admin endpoints require `auth:sanctum` middleware
   - Token-based authentication

2. **Data Filtering:**
   - All queries filter by `user_id = admin.id`
   - No fallback to "all data"

3. **Ownership Verification:**
   - Update/Delete operations verify ownership
   - Returns 404 if resource doesn't belong to admin

4. **Sensitive Data Protection:**
   - Telegram tokens are masked in API responses
   - Only last 4 characters shown: `***1234`

### 🔒 Additional Recommendations

1. **Rate Limiting:**
   - Consider adding rate limiting to prevent abuse

2. **Audit Logging:**
   - Log all admin actions for security auditing

3. **Role-Based Access:**
   - Consider adding roles (super_admin, admin, staff)

4. **API Keys:**
   - Rotate tokens periodically
   - Use environment variables for sensitive data

## Testing Security

### Test Cases

1. **Admin A cannot see Admin B's rooms:**
   ```bash
   # Login as Admin A
   # GET /api/rooms
   # Should only return Admin A's rooms
   ```

2. **Admin A cannot update Admin B's room:**
   ```bash
   # Login as Admin A
   # PUT /api/rooms/{admin_b_room_id}
   # Should return 404
   ```

3. **Telegram notifications are admin-specific:**
   ```bash
   # Admin A creates booking for their room
   # Only Admin A receives Telegram notification
   # Admin B does not receive notification
   ```

## Migration

Run the migration to add Telegram settings:

```bash
cd backend
php artisan migrate
```

This will add the Telegram columns to the `admins` table.

## Notes

- The `users` table is still used for visitors (not admins)
- All admin operations use the `admins` table
- Each admin is completely isolated from other admins' data
- Telegram notifications are sent only to the room owner (admin)




