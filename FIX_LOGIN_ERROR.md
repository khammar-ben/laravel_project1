# Fix Login/Registration Errors

## Issues Fixed

1. ✅ **Login Error**: `Method Illuminate\Auth\RequestGuard::attempt does not exist`
   - Fixed by changing admin guard from 'sanctum' to 'session' driver
   - Updated authentication to manually validate credentials for API

2. ✅ **Registration Error**: `Base table or view not found: users`
   - Fixed by updating `RegisteredUserController` to use `Admin` model instead of `User`
   - Updated validation to check `admins` table instead of `users`

## Required Steps

### 1. Run the Migration

The `admins` table migration is pending. Run:

```bash
cd backend
php artisan migrate
```

This will create the `admins` table.

### 2. Seed Admin Users (Optional)

Create test admin accounts:

```bash
php artisan db:seed --class=AdminUserSeeder
```

This creates:
- Email: `admin@happyhostel.com`
- Password: `admin123456`

### 3. Test Login

After migration, test the login:
- Endpoint: `POST http://localhost:8000/login`
- Body: `{ "email": "admin@happyhostel.com", "password": "admin123456" }`

## Changes Made

### Authentication Configuration
- Admin guard now uses 'session' driver (supports `attempt()` method)
- Added 'admin-api' guard with 'sanctum' driver for token-based auth

### Login Controller
- Simplified authentication to manually validate credentials
- Creates Sanctum token for API authentication
- No session required for API calls

### Registration Controller
- Updated to use `Admin` model
- Validation checks `admins` table
- Creates Sanctum token after registration

## Testing

1. **Login Test:**
   ```bash
   curl -X POST http://localhost:8000/login \
     -H "Content-Type: application/json" \
     -d '{"email":"admin@happyhostel.com","password":"admin123456"}'
   ```

2. **Registration Test:**
   ```bash
   curl -X POST http://localhost:8000/register \
     -H "Content-Type: application/json" \
     -d '{"name":"Test Admin","email":"test@admin.com","password":"password123","password_confirmation":"password123"}'
   ```

## Notes

- The `users` table still exists and is used for visitors (not admins)
- Admin authentication now uses the `admins` table
- All admin operations use the `Admin` model
- API authentication uses Sanctum tokens (no sessions needed)




