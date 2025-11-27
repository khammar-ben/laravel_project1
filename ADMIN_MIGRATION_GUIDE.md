# Admin Table Migration Guide

## Changes Made

The admin authentication system has been migrated from the `users` table to a dedicated `admins` table.

## What Changed

### 1. Database
- ✅ Created `admins` table migration
- ✅ New `admins` table with all necessary fields

### 2. Models
- ✅ Created `Admin` model extending `Authenticatable`
- ✅ Updated `Room`, `Activity` models to use `Admin` relationship
- ✅ `Offer` model already has `user_id` field (works with Admin)

### 3. Authentication
- ✅ Added `admin` guard in `config/auth.php`
- ✅ Added `admins` provider in `config/auth.php`
- ✅ Updated `LoginRequest` to use `admin` guard
- ✅ Updated `AuthenticatedSessionController` to use `admin` guard
- ✅ Updated `ApiAuthController` to use `Admin` model

### 4. Seeders
- ✅ Updated `AdminUserSeeder` to use `Admin` model
- ✅ Updated `Admin1HostelSeeder` to use `Admin` model
- ✅ Updated `CompleteDataSeeder` to use `Admin` model

## Migration Steps

1. **Run the migration:**
   ```bash
   cd backend
   php artisan migrate
   ```

2. **Seed admin users:**
   ```bash
   php artisan db:seed --class=AdminUserSeeder
   ```

3. **Test admin login:**
   - Email: `admin@happyhostel.com`
   - Password: `admin123456`

## Important Notes

- The `users` table still exists and is used for visitors (not admins)
- Admin authentication now uses the `admins` table
- All admin-related models (Room, Activity, Offer) now reference `Admin` instead of `User`
- The `user_id` field in rooms/activities/offers now references `admins.id`

## API Changes

- Admin login endpoints remain the same
- Authentication now uses the `admin` guard
- Tokens are created for `Admin` model instances

## Testing

After migration, test:
1. Admin login via API
2. Admin authentication in protected routes
3. Room/Activity/Offer creation (should use admin's ID)
4. Admin profile updates




