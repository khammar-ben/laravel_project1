<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guest;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Activity;
use App\Models\ActivityBooking;
use Carbon\Carbon;

class GuestBookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample guests
        $guests = [
            [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'john.smith@email.com',
                'phone' => '+44 7700 900123',
                'nationality' => 'British',
                'date_of_birth' => '1990-05-15',
                'id_type' => 'passport',
                'id_number' => 'GB123456789',
                'address' => '123 Main Street, London, UK',
                'emergency_contact_name' => 'Jane Smith',
                'emergency_contact_phone' => '+44 7700 900124',
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Garcia',
                'email' => 'maria.garcia@email.com',
                'phone' => '+34 600 123 456',
                'nationality' => 'Spanish',
                'date_of_birth' => '1988-08-22',
                'id_type' => 'passport',
                'id_number' => 'ES987654321',
                'address' => 'Calle Mayor 45, Madrid, Spain',
                'emergency_contact_name' => 'Carlos Garcia',
                'emergency_contact_phone' => '+34 600 123 457',
            ],
            [
                'first_name' => 'Hans',
                'last_name' => 'Mueller',
                'email' => 'hans.mueller@email.com',
                'phone' => '+49 30 12345678',
                'nationality' => 'German',
                'date_of_birth' => '1985-12-03',
                'id_type' => 'passport',
                'id_number' => 'DE456789123',
                'address' => 'Hauptstraße 10, Berlin, Germany',
                'emergency_contact_name' => 'Anna Mueller',
                'emergency_contact_phone' => '+49 30 12345679',
            ],
            [
                'first_name' => 'Sophie',
                'last_name' => 'Dubois',
                'email' => 'sophie.dubois@email.com',
                'phone' => '+33 1 42 34 56 78',
                'nationality' => 'French',
                'date_of_birth' => '1992-03-18',
                'id_type' => 'passport',
                'id_number' => 'FR789123456',
                'address' => '15 Rue de la Paix, Paris, France',
                'emergency_contact_name' => 'Pierre Dubois',
                'emergency_contact_phone' => '+33 1 42 34 56 79',
            ],
            [
                'first_name' => 'Yuki',
                'last_name' => 'Tanaka',
                'email' => 'yuki.tanaka@email.com',
                'phone' => '+81 3 1234 5678',
                'nationality' => 'Japanese',
                'date_of_birth' => '1991-07-25',
                'id_type' => 'passport',
                'id_number' => 'JP321654987',
                'address' => '1-2-3 Shibuya, Tokyo, Japan',
                'emergency_contact_name' => 'Hiroshi Tanaka',
                'emergency_contact_phone' => '+81 3 1234 5679',
            ],
            [
                'first_name' => 'Emma',
                'last_name' => 'Johnson',
                'email' => 'emma.johnson@email.com',
                'phone' => '+1 555 123 4567',
                'nationality' => 'American',
                'date_of_birth' => '1989-11-12',
                'id_type' => 'passport',
                'id_number' => 'US654321789',
                'address' => '456 Broadway, New York, USA',
                'emergency_contact_name' => 'Michael Johnson',
                'emergency_contact_phone' => '+1 555 123 4568',
            ],
        ];

        foreach ($guests as $guestData) {
            Guest::create($guestData);
        }

        // Get all rooms and guests
        $rooms = Room::all();
        $guestsList = Guest::all();
        $activities = Activity::all();

        if ($rooms->isEmpty() || $guestsList->isEmpty()) {
            $this->command->info('No rooms or guests found. Please seed rooms and create guests first.');
            return;
        }

        // Create sample room bookings
        $bookings = [
            [
                'booking_reference' => 'BK-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                'guest_id' => $guestsList[0]->id,
                'room_id' => $rooms[0]->id,
                'check_in_date' => Carbon::now()->subDays(2),
                'check_out_date' => Carbon::now()->addDays(3),
                'number_of_guests' => 1,
                'total_amount' => $rooms[0]->price * 5,
                'status' => 'confirmed',
                'special_requests' => 'Late check-in requested',
            ],
            [
                'booking_reference' => 'BK-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                'guest_id' => $guestsList[1]->id,
                'room_id' => $rooms[1]->id,
                'check_in_date' => Carbon::now()->addDays(1),
                'check_out_date' => Carbon::now()->addDays(7),
                'number_of_guests' => 2,
                'total_amount' => $rooms[1]->price * 6,
                'status' => 'pending',
                'special_requests' => 'Ground floor room preferred',
            ],
            [
                'booking_reference' => 'BK-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                'guest_id' => $guestsList[2]->id,
                'room_id' => $rooms[2]->id,
                'check_in_date' => Carbon::now()->addDays(5),
                'check_out_date' => Carbon::now()->addDays(10),
                'number_of_guests' => 1,
                'total_amount' => $rooms[2]->price * 5,
                'status' => 'confirmed',
                'special_requests' => null,
            ],
            [
                'booking_reference' => 'BK-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                'guest_id' => $guestsList[3]->id,
                'room_id' => $rooms->count() > 3 ? $rooms[3]->id : $rooms[0]->id,
                'check_in_date' => Carbon::now()->subDays(1),
                'check_out_date' => Carbon::now()->addDays(2),
                'number_of_guests' => 1,
                'total_amount' => ($rooms->count() > 3 ? $rooms[3]->price : $rooms[0]->price) * 3,
                'status' => 'checked_in',
                'special_requests' => 'Extra towels needed',
            ],
            [
                'booking_reference' => 'BK-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                'guest_id' => $guestsList[4]->id,
                'room_id' => $rooms->count() > 4 ? $rooms[4]->id : $rooms[1]->id,
                'check_in_date' => Carbon::now()->addDays(3),
                'check_out_date' => Carbon::now()->addDays(8),
                'number_of_guests' => 2,
                'total_amount' => ($rooms->count() > 4 ? $rooms[4]->price : $rooms[1]->price) * 5,
                'status' => 'pending',
                'special_requests' => 'Vegetarian breakfast option',
            ],
        ];

        foreach ($bookings as $bookingData) {
            Booking::create($bookingData);
        }

        // Create sample activity bookings if activities exist
        if ($activities->isNotEmpty()) {
            $activityBookings = [
                [
                    'activity_id' => $activities[0]->id,
                    'guest_id' => $guestsList[0]->id,
                    'booking_date' => Carbon::now()->addDays(1),
                    'booking_time' => '10:00',
                    'participants' => 1,
                    'per_person_price' => $activities[0]->price,
                    'total_amount' => $activities[0]->price,
                    'status' => 'confirmed',
                    'special_requests' => 'First time visitor',
                ],
                [
                    'activity_id' => $activities->count() > 1 ? $activities[1]->id : $activities[0]->id,
                    'guest_id' => $guestsList[1]->id,
                    'booking_date' => Carbon::now()->addDays(2),
                    'booking_time' => '14:00',
                    'participants' => 2,
                    'per_person_price' => $activities->count() > 1 ? $activities[1]->price : $activities[0]->price,
                    'total_amount' => ($activities->count() > 1 ? $activities[1]->price : $activities[0]->price) * 2,
                    'status' => 'pending',
                    'special_requests' => 'Group booking',
                ],
                [
                    'activity_id' => $activities->count() > 2 ? $activities[2]->id : $activities[0]->id,
                    'guest_id' => $guestsList[2]->id,
                    'booking_date' => Carbon::now()->addDays(4),
                    'booking_time' => '17:00',
                    'participants' => 1,
                    'per_person_price' => $activities->count() > 2 ? $activities[2]->price : $activities[0]->price,
                    'total_amount' => $activities->count() > 2 ? $activities[2]->price : $activities[0]->price,
                    'status' => 'confirmed',
                    'special_requests' => null,
                ],
            ];

            foreach ($activityBookings as $activityBookingData) {
                ActivityBooking::create($activityBookingData);
            }
        }

        $this->command->info('Sample guests, room bookings, and activity bookings created successfully!');
    }
}