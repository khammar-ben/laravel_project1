# Room Availability Management System

## Overview

The Room Availability Management System provides comprehensive tools for managing room availability, occupancy, bookings, and reservations. This system includes both backend services and frontend components for complete room management.

## Features

### 🏨 **Room Availability Management**
- Real-time availability checking with date conflicts
- Room occupancy tracking and synchronization
- Automatic status updates based on bookings
- Room maintenance and cleaning status tracking

### 📊 **Analytics & Reporting**
- Occupancy summary and statistics
- Room utilization reports
- Upcoming check-ins and check-outs
- Maintenance and cleaning alerts

### 🔧 **Administrative Tools**
- Bulk room status updates
- Maintenance mode management
- Cleaning schedule tracking
- Room availability calendar

## Backend Components

### 1. RoomAvailabilityService (`app/Services/RoomAvailabilityService.php`)

Central service for all room availability operations:

```php
// Check if a room is available for specific dates
$isAvailable = $availabilityService->isRoomAvailable($roomId, $checkInDate, $checkOutDate, $numberOfGuests);

// Get all available rooms for specific criteria
$rooms = $availabilityService->getAvailableRooms($checkInDate, $checkOutDate, $numberOfGuests, $roomType);

// Get room availability calendar
$calendar = $availabilityService->getRoomAvailabilityCalendar($roomId, $months);

// Get occupancy summary
$summary = $availabilityService->getRoomOccupancySummary();

// Update room status
$availabilityService->updateRoomStatus($roomId);
```

### 2. RoomAvailabilityController (`app/Http/Controllers/RoomAvailabilityController.php`)

API endpoints for room availability management:

#### Public Endpoints (No Authentication Required)
- `GET /api/room-availability/available` - Get available rooms
- `POST /api/room-availability/check` - Check specific room availability
- `GET /api/room-availability/calendar/{room_id}` - Get room availability calendar

#### Admin Endpoints (Authentication Required)
- `GET /api/room-availability/occupancy-summary` - Get occupancy summary
- `POST /api/room-availability/update-status` - Update specific room status
- `POST /api/room-availability/update-all-statuses` - Update all room statuses
- `GET /api/room-availability/needing-attention` - Get rooms needing maintenance/cleaning
- `GET /api/room-availability/upcoming-transitions` - Get upcoming check-ins/check-outs
- `GET /api/room-availability/utilization-report` - Get room utilization report

### 3. Enhanced Room Model (`app/Models/Room.php`)

Additional methods for room management:

```php
// Get detailed availability status
$status = $room->getAvailabilityStatus();

// Get active bookings
$bookings = $room->getActiveBookings();

// Get upcoming bookings
$upcoming = $room->getUpcomingBookings(7);

// Get maintenance status
$maintenance = $room->getMaintenanceStatus();

// Mark room as cleaned
$room->markAsCleaned();

// Set maintenance mode
$room->setMaintenanceMode('Plumbing repair');

// Remove from maintenance mode
$room->removeMaintenanceMode();
```

### 4. Console Command (`app/Console/Commands/UpdateRoomAvailability.php`)

Command-line tool for room availability management:

```bash
# Update all room statuses
php artisan rooms:update-availability

# Update specific room
php artisan rooms:update-availability --room-id=1

# Check maintenance needs
php artisan rooms:update-availability --maintenance

# Force update
php artisan rooms:update-availability --force
```

## Frontend Components

### AdminRoomAvailability Component (`frontend/src/pages/admin/AdminRoomAvailability.tsx`)

Comprehensive admin dashboard for room availability management:

#### Features:
- **Room Status Overview**: View all rooms with their current status, occupancy, and availability
- **Occupancy Summary**: Real-time statistics including total rooms, capacity, occupancy rate
- **Upcoming Transitions**: Track upcoming check-ins and check-outs
- **Maintenance Management**: Monitor rooms needing cleaning or maintenance
- **Filtering & Search**: Filter rooms by type, status, and other criteria
- **Bulk Operations**: Update multiple room statuses at once

#### Tabs:
1. **Room Status**: Overview of all rooms with filtering options
2. **Upcoming Transitions**: Check-ins and check-outs for the next 7 days
3. **Maintenance**: Rooms needing attention for cleaning or maintenance

## API Usage Examples

### Get Available Rooms
```javascript
const response = await fetch('/api/room-availability/available', {
  method: 'GET',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    check_in_date: '2024-01-15',
    check_out_date: '2024-01-20',
    number_of_guests: 2,
    room_type: 'Private Double'
  })
});
```

### Check Specific Room Availability
```javascript
const response = await fetch('/api/room-availability/check', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    room_id: 1,
    check_in_date: '2024-01-15',
    check_out_date: '2024-01-20',
    number_of_guests: 2
  })
});
```

### Get Occupancy Summary (Admin)
```javascript
const response = await fetch('/api/room-availability/occupancy-summary', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
  }
});
```

## Room Status Types

- **available**: Room is available for booking
- **occupied**: Room has guests but has available spaces
- **full**: Room is at maximum capacity
- **maintenance**: Room is under maintenance and not available

## Room Types Supported

- Mixed Dormitory
- Female Dormitory
- Private Single
- Private Double
- Private Triple
- Private Quad
- Executive Suite
- Penthouse Suite
- Accessible Suite

## Maintenance Features

### Cleaning Management
- Track last cleaning date
- Identify rooms needing cleaning (7+ days)
- Mark rooms as cleaned
- Cleaning schedule alerts

### Maintenance Mode
- Set rooms to maintenance mode
- Add maintenance reasons
- Remove from maintenance mode
- Maintenance status tracking

## Integration with Booking System

The room availability system integrates seamlessly with the existing booking system:

1. **Automatic Updates**: Room occupancy is automatically updated when bookings are created, confirmed, or cancelled
2. **Conflict Prevention**: Date conflicts are checked before allowing new bookings
3. **Status Synchronization**: Room status is automatically updated based on current occupancy
4. **Real-time Availability**: Availability is checked in real-time for accurate booking information

## Best Practices

1. **Regular Updates**: Run the update command regularly to keep room statuses current
2. **Maintenance Scheduling**: Use the maintenance features to schedule room cleaning and repairs
3. **Monitor Occupancy**: Keep track of occupancy rates to optimize room allocation
4. **Check Transitions**: Regularly review upcoming check-ins and check-outs for planning
5. **Clean Regularly**: Ensure rooms are cleaned at least every 7 days

## Troubleshooting

### Common Issues:

1. **Room Status Not Updating**: Run `php artisan rooms:update-availability` to sync statuses
2. **Availability Not Accurate**: Check for date conflicts in existing bookings
3. **Maintenance Mode**: Ensure rooms are removed from maintenance mode when ready
4. **Cleaning Alerts**: Check rooms that haven't been cleaned in 7+ days

### Commands for Maintenance:

```bash
# Sync all room statuses
php artisan rooms:update-availability

# Check rooms needing attention
php artisan rooms:update-availability --maintenance

# Update specific room
php artisan rooms:update-availability --room-id=1
```

This comprehensive room availability management system provides all the tools needed to efficiently manage room bookings, reservations, occupancy, and maintenance in your hostel booking system.

