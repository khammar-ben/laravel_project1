<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Room;
use App\Models\Guest;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class Admin789Seeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating admin789@gmail.com with rooms and bookings...');

        $admin = Admin::firstOrCreate(
            ['email' => 'admin789@gmail.com'],
            [
                'username' => 'admin789',
                'full_name' => 'Admin 789',
                'password' => Hash::make('admin123456'),
                'role' => 'admin',
            ]
        );

        $this->command->info("Admin created/found: {$admin->email} (ID: {$admin->id})");

        $rooms = [
            [
                'room_number' => 'A789-001',
                'name' => 'Deluxe Single Room',
                'type' => 'Private Single',
                'capacity' => 1,
                'occupied' => 0,
                'floor' => 1,
                'price' => 50.00,
                'status' => 'available',
                'description' => 'Comfortable single room with private bathroom and city view.',
                'image_url' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&h=600&fit=crop',
                'amenities' => ['Free WiFi', 'Private Bathroom', 'AC', 'TV', 'City View', 'Work Desk'],
                'last_cleaned' => now()->subHours(2),
                'user_id' => $admin->id,
            ],
            [
                'room_number' => 'A789-002',
                'name' => 'Standard Double Room',
                'type' => 'Private Double',
                'capacity' => 2,
                'occupied' => 1,
                'floor' => 1,
                'price' => 70.00,
                'status' => 'available',
                'description' => 'Spacious double room with queen bed and private facilities.',
                'image_url' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=800&h=600&fit=crop',
                'amenities' => ['Free WiFi', 'Private Bathroom', 'AC', 'TV', 'Mini Fridge', 'City View'],
                'last_cleaned' => now()->subHours(1),
                'user_id' => $admin->id,
            ],
            [
                'room_number' => 'A789-003',
                'name' => '4-Bed Mixed Dormitory',
                'type' => 'Mixed Dormitory',
                'capacity' => 4,
                'occupied' => 2,
                'floor' => 2,
                'price' => 25.00,
                'status' => 'available',
                'description' => 'Modern mixed dormitory with individual lockers and reading lights.',
                'image_url' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&h=600&fit=crop',
                'amenities' => ['Free WiFi', 'AC', 'Individual Lockers', 'Reading Lights', 'Power Outlets', 'Shared Bathroom'],
                'last_cleaned' => now()->subHours(3),
                'user_id' => $admin->id,
            ],
            [
                'room_number' => 'A789-004',
                'name' => '6-Bed Female Dormitory',
                'type' => 'Female Dormitory',
                'capacity' => 6,
                'occupied' => 4,
                'floor' => 2,
                'price' => 23.00,
                'status' => 'available',
                'description' => 'Female-only dormitory with en-suite bathroom and study area.',
                'image_url' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&h=600&fit=crop',
                'amenities' => ['Free WiFi', 'AC', 'En-suite Bathroom', 'Individual Lockers', 'Hair Dryer', 'Study Desk'],
                'last_cleaned' => now()->subHours(1),
                'user_id' => $admin->id,
            ],
            [
                'room_number' => 'A789-005',
                'name' => 'Family Suite',
                'type' => 'Family Suite',
                'capacity' => 4,
                'occupied' => 0,
                'floor' => 3,
                'price' => 90.00,
                'status' => 'available',
                'description' => 'Spacious family suite with separate living area and kitchenette.',
                'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=600&fit=crop',
                'amenities' => ['Free WiFi', 'Private Bathroom', 'Kitchenette', 'TV', 'Balcony', 'City View', 'Living Area'],
                'last_cleaned' => now()->subDays(1),
                'user_id' => $admin->id,
            ],
            [
                'room_number' => 'A789-006',
                'name' => '8-Bed Mixed Dormitory',
                'type' => 'Mixed Dormitory',
                'capacity' => 8,
                'occupied' => 5,
                'floor' => 3,
                'price' => 20.00,
                'status' => 'available',
                'description' => 'Budget-friendly mixed dormitory with large windows and common area.',
                'image_url' => 'https://images.unsplash.com/photo-1522771739844-6a9ed6b74bbf?w=800&h=600&fit=crop',
                'amenities' => ['Free WiFi', 'AC', 'Individual Lockers', 'Large Windows', 'Common Area', 'Reading Lights'],
                'last_cleaned' => now()->subHours(4),
                'user_id' => $admin->id,
            ],
        ];

        $createdRooms = [];
        foreach ($rooms as $roomData) {
            $room = Room::firstOrCreate(
                ['room_number' => $roomData['room_number']],
                $roomData
            );
            $createdRooms[] = $room;
            $this->command->info("Room created: {$room->room_number} - {$room->name}");
        }

        $this->command->info('Creating bookings...');

        $guests = [
            [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'john.smith@example.com',
                'phone' => '+1234567890',
                'nationality' => 'American',
                'date_of_birth' => Carbon::parse('1990-05-15'),
                'id_type' => 'Passport',
                'id_number' => 'P123456789',
                'address' => '123 Main St, New York, USA',
                'emergency_contact_name' => 'Jane Smith',
                'emergency_contact_phone' => '+1234567891',
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Garcia',
                'email' => 'maria.garcia@example.com',
                'phone' => '+1987654321',
                'nationality' => 'Spanish',
                'date_of_birth' => Carbon::parse('1992-08-22'),
                'id_type' => 'Passport',
                'id_number' => 'ES987654321',
                'address' => '456 Calle Mayor, Madrid, Spain',
                'emergency_contact_name' => 'Carlos Garcia',
                'emergency_contact_phone' => '+1987654322',
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Chen',
                'email' => 'david.chen@example.com',
                'phone' => '+1122334455',
                'nationality' => 'Chinese',
                'date_of_birth' => Carbon::parse('1988-12-10'),
                'id_type' => 'Passport',
                'id_number' => 'CN112233445',
                'address' => '789 Beijing Road, Shanghai, China',
                'emergency_contact_name' => 'Li Chen',
                'emergency_contact_phone' => '+1122334456',
            ],
            [
                'first_name' => 'Emma',
                'last_name' => 'Johnson',
                'email' => 'emma.johnson@example.com',
                'phone' => '+1555666777',
                'nationality' => 'British',
                'date_of_birth' => Carbon::parse('1995-03-20'),
                'id_type' => 'Passport',
                'id_number' => 'GB555666777',
                'address' => '321 Oxford Street, London, UK',
                'emergency_contact_name' => 'James Johnson',
                'emergency_contact_phone' => '+1555666778',
            ],
            [
                'first_name' => 'Ahmed',
                'last_name' => 'Ali',
                'email' => 'ahmed.ali@example.com',
                'phone' => '+201234567890',
                'nationality' => 'Egyptian',
                'date_of_birth' => Carbon::parse('1991-07-05'),
                'id_type' => 'Passport',
                'id_number' => 'EG201234567',
                'address' => '654 Tahrir Square, Cairo, Egypt',
                'emergency_contact_name' => 'Fatima Ali',
                'emergency_contact_phone' => '+201234567891',
            ],
        ];

        $bookings = [
            [
                'room' => $createdRooms[0],
                'guest' => $guests[0],
                'check_in_date' => Carbon::now()->subDays(2),
                'check_out_date' => Carbon::now()->addDays(3),
                'number_of_guests' => 1,
                'status' => 'checked_in',
                'special_requests' => 'Late check-in requested',
            ],
            [
                'room' => $createdRooms[1],
                'guest' => $guests[1],
                'check_in_date' => Carbon::now()->subDays(1),
                'check_out_date' => Carbon::now()->addDays(4),
                'number_of_guests' => 2,
                'status' => 'checked_in',
                'special_requests' => 'Need extra towels',
            ],
            [
                'room' => $createdRooms[2],
                'guest' => $guests[2],
                'check_in_date' => Carbon::now()->addDays(2),
                'check_out_date' => Carbon::now()->addDays(5),
                'number_of_guests' => 1,
                'status' => 'confirmed',
                'special_requests' => 'Early check-in if possible',
            ],
            [
                'room' => $createdRooms[2],
                'guest' => $guests[3],
                'check_in_date' => Carbon::now()->addDays(3),
                'check_out_date' => Carbon::now()->addDays(7),
                'number_of_guests' => 1,
                'status' => 'confirmed',
                'special_requests' => null,
            ],
            [
                'room' => $createdRooms[3],
                'guest' => $guests[4],
                'check_in_date' => Carbon::now()->addDays(5),
                'check_out_date' => Carbon::now()->addDays(10),
                'number_of_guests' => 1,
                'status' => 'pending',
                'special_requests' => 'Window seat preferred',
            ],
            [
                'room' => $createdRooms[4],
                'guest' => $guests[0],
                'check_in_date' => Carbon::now()->addDays(10),
                'check_out_date' => Carbon::now()->addDays(15),
                'number_of_guests' => 4,
                'status' => 'pending',
                'special_requests' => 'Family with two children',
            ],
            [
                'room' => $createdRooms[5],
                'guest' => $guests[1],
                'check_in_date' => Carbon::now()->subDays(3),
                'check_out_date' => Carbon::now()->addDays(2),
                'number_of_guests' => 2,
                'status' => 'checked_in',
                'special_requests' => null,
            ],
            [
                'room' => $createdRooms[5],
                'guest' => $guests[2],
                'check_in_date' => Carbon::now()->subDays(3),
                'check_out_date' => Carbon::now()->addDays(1),
                'number_of_guests' => 1,
                'status' => 'checked_in',
                'special_requests' => null,
            ],
        ];

        foreach ($bookings as $bookingData) {
            $guest = Guest::firstOrCreate(
                ['email' => $bookingData['guest']['email']],
                $bookingData['guest']
            );

            $checkIn = $bookingData['check_in_date'];
            $checkOut = $bookingData['check_out_date'];
            $numberOfNights = $checkIn->diffInDays($checkOut);
            $room = $bookingData['room'];
            $originalAmount = $numberOfNights * $room->price;
            $totalAmount = $originalAmount;

            $booking = Booking::create([
                'booking_reference' => 'BK' . strtoupper(Str::random(8)),
                'guest_id' => $guest->id,
                'room_id' => $room->id,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'number_of_guests' => $bookingData['number_of_guests'],
                'total_amount' => $totalAmount,
                'original_amount' => $originalAmount,
                'discount_amount' => 0,
                'status' => $bookingData['status'],
                'special_requests' => $bookingData['special_requests'],
                'confirmed_at' => in_array($bookingData['status'], ['confirmed', 'checked_in']) ? now() : null,
                'checked_in_at' => $bookingData['status'] === 'checked_in' ? now() : null,
            ]);

            if (in_array($bookingData['status'], ['confirmed', 'checked_in'])) {
                $room->increment('occupied', $bookingData['number_of_guests']);
                $room->fresh()->updateStatusBasedOnOccupancy();
            }

            $this->command->info("Booking created: {$booking->booking_reference} - {$guest->first_name} {$guest->last_name} - {$room->room_number}");
        }

        $this->command->info('✅ Successfully created admin789@gmail.com with rooms and bookings!');
    }
}




