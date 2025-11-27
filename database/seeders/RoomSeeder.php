<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin users to assign rooms to
        $adminUsers = User::whereIn('email', ['admin@happyhostel.com'])->get();
        
        if ($adminUsers->isEmpty()) {
            $this->command->error('Admin users not found. Please run User seeder first.');
            return;
        }

        $rooms = [
            [
                'room_number' => 'R001',
                'name' => '4-Bed Mixed Dorm A',
                'type' => 'Mixed Dormitory',
                'capacity' => 4,
                'occupied' => 3,
                'floor' => 1,
                'price' => 25.00,
                'status' => 'available',
                'description' => 'Comfortable mixed dormitory with individual lockers and reading lights.',
                'amenities' => ['Free WiFi', 'AC', 'Individual Lockers', 'Reading Lights', 'Power Outlets'],
                'last_cleaned' => now()->subHours(2),
            ],
            [
                'room_number' => 'R002',
                'name' => '6-Bed Female Dorm B',
                'type' => 'Female Dormitory',
                'capacity' => 6,
                'occupied' => 6,
                'floor' => 1,
                'price' => 23.00,
                'status' => 'full',
                'description' => 'Female-only dormitory with shared bathroom facilities.',
                'amenities' => ['Free WiFi', 'AC', 'Individual Lockers', 'Hair Dryer', 'Shared Bathroom'],
                'last_cleaned' => now()->subHours(1),
            ],
            [
                'room_number' => 'R003',
                'name' => 'Private Single 201',
                'type' => 'Private Single',
                'capacity' => 1,
                'occupied' => 1,
                'floor' => 2,
                'price' => 45.00,
                'status' => 'occupied',
                'description' => 'Private single room with shared bathroom facilities.',
                'amenities' => ['Private Space', 'Shared Bathroom', 'Free WiFi', 'Quiet Area', 'Reading Desk'],
                'last_cleaned' => now()->subDays(1),
            ],
            [
                'room_number' => 'R004',
                'name' => 'Private Double 302',
                'type' => 'Private Double',
                'capacity' => 2,
                'occupied' => 0,
                'floor' => 3,
                'price' => 65.00,
                'status' => 'maintenance',
                'description' => 'Private double room with private bathroom and city view.',
                'amenities' => ['Private Bathroom', 'Free WiFi', 'TV', 'City View', 'Mini Fridge'],
                'last_cleaned' => now()->subDays(2),
            ],
            [
                'room_number' => 'R005',
                'name' => '8-Bed Mixed Dorm C',
                'type' => 'Mixed Dormitory',
                'capacity' => 8,
                'occupied' => 5,
                'floor' => 2,
                'price' => 20.00,
                'status' => 'available',
                'description' => 'Budget-friendly mixed dormitory with large windows.',
                'amenities' => ['Free WiFi', 'AC', 'Individual Lockers', 'Large Windows', 'Common Area Access'],
                'last_cleaned' => now()->subHours(3),
            ],
            [
                'room_number' => 'R006',
                'name' => '12-Bed Mixed Dorm D',
                'type' => 'Mixed Dormitory',
                'capacity' => 12,
                'occupied' => 8,
                'floor' => 3,
                'price' => 18.00,
                'status' => 'available',
                'description' => 'Economy option with basic amenities and large windows.',
                'amenities' => ['Free WiFi', 'Individual Lockers', 'Large Windows', 'Reading Lights'],
                'last_cleaned' => now()->subHours(4),
            ],
            [
                'room_number' => 'R007',
                'name' => '6-Bed Female Dorm E',
                'type' => 'Female Dormitory',
                'capacity' => 6,
                'occupied' => 4,
                'floor' => 2,
                'price' => 22.00,
                'status' => 'available',
                'description' => 'Modern female dormitory with en-suite bathroom and study area.',
                'amenities' => ['Free WiFi', 'AC', 'En-suite Bathroom', 'Study Desk', 'Individual Lockers', 'Hair Dryer'],
                'last_cleaned' => now()->subHours(1),
            ],
            [
                'room_number' => 'R008',
                'name' => 'Private Twin 203',
                'type' => 'Private Twin',
                'capacity' => 2,
                'occupied' => 0,
                'floor' => 2,
                'price' => 55.00,
                'status' => 'available',
                'description' => 'Private twin room with two single beds and shared bathroom.',
                'amenities' => ['Two Single Beds', 'Shared Bathroom', 'Free WiFi', 'Quiet Area', 'Reading Desk', 'Wardrobe'],
                'last_cleaned' => now()->subDays(1),
            ],
            [
                'room_number' => 'R009',
                'name' => '10-Bed Mixed Dorm F',
                'type' => 'Mixed Dormitory',
                'capacity' => 10,
                'occupied' => 7,
                'floor' => 3,
                'price' => 19.00,
                'status' => 'available',
                'description' => 'Large mixed dormitory with modern facilities and common area access.',
                'amenities' => ['Free WiFi', 'AC', 'Individual Lockers', 'Common Area', 'Reading Lights', 'Power Outlets'],
                'last_cleaned' => now()->subHours(2),
            ],
            [
                'room_number' => 'R010',
                'name' => 'Executive Suite 401',
                'type' => 'Executive Suite',
                'capacity' => 3,
                'occupied' => 1,
                'floor' => 4,
                'price' => 75.00,
                'status' => 'available',
                'description' => 'Spacious executive suite with separate living area and premium amenities.',
                'amenities' => ['Private Bathroom', 'Living Area', 'Free WiFi', 'TV', 'Mini Fridge', 'City View', 'Work Desk'],
                'last_cleaned' => now()->subHours(3),
            ],
            [
                'room_number' => 'R011',
                'name' => '4-Bed Female Dorm G',
                'type' => 'Female Dormitory',
                'capacity' => 4,
                'occupied' => 3,
                'floor' => 1,
                'price' => 24.00,
                'status' => 'available',
                'description' => 'Cozy female dormitory with modern amenities and shared facilities.',
                'amenities' => ['Free WiFi', 'AC', 'Individual Lockers', 'Shared Bathroom', 'Reading Lights', 'Hair Dryer'],
                'last_cleaned' => now()->subHours(2),
            ],
            [
                'room_number' => 'R012',
                'name' => 'Private Triple 303',
                'type' => 'Private Triple',
                'capacity' => 3,
                'occupied' => 2,
                'floor' => 3,
                'price' => 70.00,
                'status' => 'available',
                'description' => 'Private triple room with three single beds and private bathroom.',
                'amenities' => ['Three Single Beds', 'Private Bathroom', 'Free WiFi', 'TV', 'Wardrobe', 'City View'],
                'last_cleaned' => now()->subDays(1),
            ],
            [
                'room_number' => 'R013',
                'name' => '14-Bed Mixed Dorm H',
                'type' => 'Mixed Dormitory',
                'capacity' => 14,
                'occupied' => 11,
                'floor' => 4,
                'price' => 17.00,
                'status' => 'available',
                'description' => 'Economy mixed dormitory with basic amenities and large windows.',
                'amenities' => ['Free WiFi', 'Individual Lockers', 'Large Windows', 'Reading Lights', 'Common Area Access'],
                'last_cleaned' => now()->subHours(4),
            ],
            [
                'room_number' => 'R014',
                'name' => 'Penthouse Suite 601',
                'type' => 'Penthouse Suite',
                'capacity' => 4,
                'occupied' => 0,
                'floor' => 6,
                'price' => 120.00,
                'status' => 'available',
                'description' => 'Luxury penthouse suite with panoramic city views and premium amenities.',
                'amenities' => ['Private Bathroom', 'Kitchenette', 'Free WiFi', 'Smart TV', 'Balcony', 'Panoramic View', 'Room Service', 'Mini Bar'],
                'last_cleaned' => now()->subHours(1),
            ],
            [
                'room_number' => 'R015',
                'name' => 'Accessible Suite 102',
                'type' => 'Accessible Suite',
                'capacity' => 2,
                'occupied' => 1,
                'floor' => 1,
                'price' => 60.00,
                'status' => 'available',
                'description' => 'Wheelchair accessible suite with adapted facilities and private bathroom.',
                'amenities' => ['Wheelchair Accessible', 'Private Bathroom', 'Free WiFi', 'Adapted Facilities', 'Emergency Call Button', 'Wide Doorways'],
                'last_cleaned' => now()->subHours(1),
            ],
        ];

        foreach ($rooms as $index => $roomData) {
            // Assign rooms to different admins
            $roomData['user_id'] = $adminUsers[$index % $adminUsers->count()]->id;
            Room::create($roomData);
        }
    }
}
