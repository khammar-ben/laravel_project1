<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Room;
use Illuminate\Support\Facades\Hash;

class Admin1HostelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin1@hostel.com admin already exists
        $existingAdmin = Admin::where('email', 'admin1@hostel.com')->first();
        
        if ($existingAdmin) {
            $this->command->info('Admin1 user already exists!');
            $this->command->info('Email: admin1@hostel.com');
            $this->command->info('Password: admin123456');
        } else {
            // Create admin1@hostel.com admin
            $admin = Admin::create([
                'name' => 'Admin1 Hostel',
                'email' => 'admin1@hostel.com',
                'password' => Hash::make('admin123456'),
                'email_verified_at' => now(),
            ]);

            $this->command->info('✅ Admin1 user created successfully!');
            $this->command->info('📧 Email: admin1@hostel.com');
            $this->command->info('🔑 Password: admin123456');
        }

        // Get the admin (either existing or newly created)
        $adminUser = Admin::where('email', 'admin1@hostel.com')->first();

        // Check if rooms already exist for this user
        $existingRooms = Room::where('user_id', $adminUser->id)->count();
        
        if ($existingRooms > 0) {
            $this->command->info("Admin1 already has {$existingRooms} rooms assigned.");
            return;
        }

        // Create rooms for admin1@hostel.com
        $rooms = [
            [
                'room_number' => 'H001',
                'name' => 'Deluxe Single Room',
                'type' => 'Private Single',
                'capacity' => 1,
                'occupied' => 0,
                'floor' => 1,
                'price' => 50.00,
                'status' => 'available',
                'description' => 'Comfortable single room with modern amenities and city view.',
                'amenities' => ['Free WiFi', 'AC', 'Private Bathroom', 'City View', 'Work Desk', 'Mini Fridge'],
                'last_cleaned' => now()->subHours(2),
                'user_id' => $adminUser->id,
            ],
            [
                'room_number' => 'H002',
                'name' => 'Standard Double Room',
                'type' => 'Private Double',
                'capacity' => 2,
                'occupied' => 1,
                'floor' => 1,
                'price' => 75.00,
                'status' => 'occupied',
                'description' => 'Spacious double room perfect for couples or friends.',
                'amenities' => ['Free WiFi', 'AC', 'Private Bathroom', 'TV', 'Wardrobe', 'Balcony'],
                'last_cleaned' => now()->subDays(1),
                'user_id' => $adminUser->id,
            ],
            [
                'room_number' => 'H003',
                'name' => 'Family Suite',
                'type' => 'Family Suite',
                'capacity' => 4,
                'occupied' => 2,
                'floor' => 2,
                'price' => 120.00,
                'status' => 'available',
                'description' => 'Large family suite with separate living area and kitchenette.',
                'amenities' => ['Free WiFi', 'AC', 'Private Bathroom', 'Kitchenette', 'Living Area', 'TV', 'Balcony'],
                'last_cleaned' => now()->subHours(3),
                'user_id' => $adminUser->id,
            ],
            [
                'room_number' => 'H004',
                'name' => 'Budget Twin Room',
                'type' => 'Private Twin',
                'capacity' => 2,
                'occupied' => 0,
                'floor' => 2,
                'price' => 60.00,
                'status' => 'available',
                'description' => 'Affordable twin room with shared bathroom facilities.',
                'amenities' => ['Free WiFi', 'AC', 'Shared Bathroom', 'Reading Desk', 'Wardrobe'],
                'last_cleaned' => now()->subHours(1),
                'user_id' => $adminUser->id,
            ],
            [
                'room_number' => 'H005',
                'name' => 'Executive Room',
                'type' => 'Executive Room',
                'capacity' => 2,
                'occupied' => 1,
                'floor' => 3,
                'price' => 100.00,
                'status' => 'occupied',
                'description' => 'Premium executive room with business amenities and city view.',
                'amenities' => ['Free WiFi', 'AC', 'Private Bathroom', 'Work Desk', 'City View', 'Mini Bar', 'Room Service'],
                'last_cleaned' => now()->subDays(1),
                'user_id' => $adminUser->id,
            ],
            [
                'room_number' => 'H006',
                'name' => 'Garden View Room',
                'type' => 'Private Single',
                'capacity' => 1,
                'occupied' => 0,
                'floor' => 1,
                'price' => 45.00,
                'status' => 'available',
                'description' => 'Peaceful room overlooking the garden with natural light.',
                'amenities' => ['Free WiFi', 'AC', 'Private Bathroom', 'Garden View', 'Reading Chair', 'Natural Light'],
                'last_cleaned' => now()->subHours(4),
                'user_id' => $adminUser->id,
            ],
            [
                'room_number' => 'H007',
                'name' => 'Deluxe Family Room',
                'type' => 'Family Room',
                'capacity' => 5,
                'occupied' => 3,
                'floor' => 3,
                'price' => 150.00,
                'status' => 'available',
                'description' => 'Spacious family room with multiple beds and private facilities.',
                'amenities' => ['Free WiFi', 'AC', 'Private Bathroom', 'Multiple Beds', 'TV', 'Kitchenette', 'Balcony'],
                'last_cleaned' => now()->subHours(2),
                'user_id' => $adminUser->id,
            ],
            [
                'room_number' => 'H008',
                'name' => 'Standard Single Room',
                'type' => 'Private Single',
                'capacity' => 1,
                'occupied' => 0,
                'floor' => 2,
                'price' => 40.00,
                'status' => 'maintenance',
                'description' => 'Basic single room with essential amenities.',
                'amenities' => ['Free WiFi', 'AC', 'Shared Bathroom', 'Reading Desk'],
                'last_cleaned' => now()->subDays(2),
                'user_id' => $adminUser->id,
            ],
        ];

        foreach ($rooms as $roomData) {
            Room::create($roomData);
        }

        $this->command->info('✅ Successfully created ' . count($rooms) . ' rooms for admin1@hostel.com');
        $this->command->info('🏨 Rooms assigned to: admin1@hostel.com');
    }
}
