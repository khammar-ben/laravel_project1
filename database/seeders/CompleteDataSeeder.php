<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Room;
use App\Models\Activity;
use App\Models\Guest;
use App\Models\Booking;
use App\Models\ActivityBooking;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CompleteDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        $this->command->info('Clearing existing data...');
        
        // Disable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        ActivityBooking::truncate();
        Booking::truncate();
        Guest::truncate();
        Activity::truncate();
        Room::truncate();
        User::where('email', '!=', 'test@abdo.com')->delete();
        
        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create admin users
        $this->command->info('Creating admin users...');
        $admin1 = Admin::create([
            'name' => 'Admin User 1',
            'email' => 'admin1@hostel.com',
            'password' => bcrypt('admin123')
        ]);

        $admin2 = Admin::create([
            'name' => 'Admin User 2', 
            'email' => 'admin2@hostel.com',
            'password' => bcrypt('admin123')
        ]);

        // Create rooms for both admins
        $this->command->info('Creating rooms...');
        $rooms = [
            // Admin 1 rooms
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
                'image_url' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&h=600&fit=crop',
                'amenities' => ['Free WiFi', 'AC', 'Individual Lockers', 'Reading Lights', 'Power Outlets'],
                'last_cleaned' => now()->subHours(2),
                'user_id' => $admin1->id,
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
                'image_url' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&h=600&fit=crop',
                'amenities' => ['Free WiFi', 'AC', 'Individual Lockers', 'Hair Dryer', 'Shared Bathroom'],
                'last_cleaned' => now()->subHours(1),
                'user_id' => $admin1->id,
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
                'image_url' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&h=600&fit=crop',
                'amenities' => ['Private Space', 'Shared Bathroom', 'Free WiFi', 'Quiet Area', 'Reading Desk'],
                'last_cleaned' => now()->subDays(1),
                'user_id' => $admin1->id,
            ],
            [
                'room_number' => 'R004',
                'name' => 'Private Double 302',
                'type' => 'Private Double',
                'capacity' => 2,
                'occupied' => 0,
                'floor' => 3,
                'price' => 65.00,
                'status' => 'available',
                'description' => 'Private double room with private bathroom and city view.',
                'image_url' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=800&h=600&fit=crop',
                'amenities' => ['Private Bathroom', 'Free WiFi', 'TV', 'City View', 'Mini Fridge'],
                'last_cleaned' => now()->subDays(2),
                'user_id' => $admin1->id,
            ],
            // Admin 2 rooms
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
                'image_url' => 'https://images.unsplash.com/photo-1522771739844-6a9ed6b74bbf?w=800&h=600&fit=crop',
                'amenities' => ['Free WiFi', 'AC', 'Individual Lockers', 'Large Windows', 'Common Area Access'],
                'last_cleaned' => now()->subHours(3),
                'user_id' => $admin2->id,
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
                'image_url' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=800&h=600&fit=crop',
                'amenities' => ['Free WiFi', 'Individual Lockers', 'Large Windows', 'Reading Lights'],
                'last_cleaned' => now()->subHours(4),
                'user_id' => $admin2->id,
            ],
            [
                'room_number' => 'R007',
                'name' => 'Family Suite 401',
                'type' => 'Family Suite',
                'capacity' => 4,
                'occupied' => 2,
                'floor' => 4,
                'price' => 85.00,
                'status' => 'available',
                'description' => 'Spacious family suite with private bathroom and kitchenette.',
                'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=600&fit=crop',
                'amenities' => ['Private Bathroom', 'Kitchenette', 'Free WiFi', 'TV', 'Balcony', 'City View'],
                'last_cleaned' => now()->subHours(1),
                'user_id' => $admin2->id,
            ],
            [
                'room_number' => 'R008',
                'name' => 'Deluxe Private 501',
                'type' => 'Deluxe Private',
                'capacity' => 2,
                'occupied' => 1,
                'floor' => 5,
                'price' => 95.00,
                'status' => 'available',
                'description' => 'Luxury private room with premium amenities and panoramic views.',
                'image_url' => 'https://images.unsplash.com/photo-1595576508898-0ad5c879a061?w=800&h=600&fit=crop',
                'amenities' => ['Private Bathroom', 'Mini Bar', 'Free WiFi', 'Smart TV', 'Balcony', 'City View', 'Room Service'],
                'last_cleaned' => now()->subHours(2),
                'user_id' => $admin2->id,
            ],
            // Additional rooms for Admin 1
            [
                'room_number' => 'R009',
                'name' => '6-Bed Female Dorm E',
                'type' => 'Female Dormitory',
                'capacity' => 6,
                'occupied' => 4,
                'floor' => 2,
                'price' => 22.00,
                'status' => 'available',
                'description' => 'Modern female dormitory with en-suite bathroom and study area.',
                'image_url' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&h=600&fit=crop',
                'amenities' => ['Free WiFi', 'AC', 'En-suite Bathroom', 'Study Desk', 'Individual Lockers', 'Hair Dryer'],
                'last_cleaned' => now()->subHours(1),
                'user_id' => $admin1->id,
            ],
            [
                'room_number' => 'R010',
                'name' => 'Private Twin 203',
                'type' => 'Private Twin',
                'capacity' => 2,
                'occupied' => 0,
                'floor' => 2,
                'price' => 55.00,
                'status' => 'available',
                'description' => 'Private twin room with two single beds and shared bathroom.',
                'image_url' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&h=600&fit=crop',
                'amenities' => ['Two Single Beds', 'Shared Bathroom', 'Free WiFi', 'Quiet Area', 'Reading Desk', 'Wardrobe'],
                'last_cleaned' => now()->subDays(1),
                'user_id' => $admin1->id,
            ],
            [
                'room_number' => 'R011',
                'name' => '10-Bed Mixed Dorm F',
                'type' => 'Mixed Dormitory',
                'capacity' => 10,
                'occupied' => 7,
                'floor' => 3,
                'price' => 19.00,
                'status' => 'available',
                'description' => 'Large mixed dormitory with modern facilities and common area access.',
                'image_url' => 'https://images.unsplash.com/photo-1522771739844-6a9ed6b74bbf?w=800&h=600&fit=crop',
                'amenities' => ['Free WiFi', 'AC', 'Individual Lockers', 'Common Area', 'Reading Lights', 'Power Outlets'],
                'last_cleaned' => now()->subHours(2),
                'user_id' => $admin1->id,
            ],
            [
                'room_number' => 'R012',
                'name' => 'Executive Suite 401',
                'type' => 'Executive Suite',
                'capacity' => 3,
                'occupied' => 1,
                'floor' => 4,
                'price' => 75.00,
                'status' => 'available',
                'description' => 'Spacious executive suite with separate living area and premium amenities.',
                'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=600&fit=crop',
                'amenities' => ['Private Bathroom', 'Living Area', 'Free WiFi', 'TV', 'Mini Fridge', 'City View', 'Work Desk'],
                'last_cleaned' => now()->subHours(3),
                'user_id' => $admin1->id,
            ],
            // Additional rooms for Admin 2
            [
                'room_number' => 'R013',
                'name' => '4-Bed Female Dorm G',
                'type' => 'Female Dormitory',
                'capacity' => 4,
                'occupied' => 3,
                'floor' => 1,
                'price' => 24.00,
                'status' => 'available',
                'description' => 'Cozy female dormitory with modern amenities and shared facilities.',
                'image_url' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&h=600&fit=crop',
                'amenities' => ['Free WiFi', 'AC', 'Individual Lockers', 'Shared Bathroom', 'Reading Lights', 'Hair Dryer'],
                'last_cleaned' => now()->subHours(2),
                'user_id' => $admin2->id,
            ],
            [
                'room_number' => 'R014',
                'name' => 'Private Triple 303',
                'type' => 'Private Triple',
                'capacity' => 3,
                'occupied' => 2,
                'floor' => 3,
                'price' => 70.00,
                'status' => 'available',
                'description' => 'Private triple room with three single beds and private bathroom.',
                'image_url' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=800&h=600&fit=crop',
                'amenities' => ['Three Single Beds', 'Private Bathroom', 'Free WiFi', 'TV', 'Wardrobe', 'City View'],
                'last_cleaned' => now()->subDays(1),
                'user_id' => $admin2->id,
            ],
            [
                'room_number' => 'R015',
                'name' => '14-Bed Mixed Dorm H',
                'type' => 'Mixed Dormitory',
                'capacity' => 14,
                'occupied' => 11,
                'floor' => 4,
                'price' => 17.00,
                'status' => 'available',
                'description' => 'Economy mixed dormitory with basic amenities and large windows.',
                'image_url' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=800&h=600&fit=crop',
                'amenities' => ['Free WiFi', 'Individual Lockers', 'Large Windows', 'Reading Lights', 'Common Area Access'],
                'last_cleaned' => now()->subHours(4),
                'user_id' => $admin2->id,
            ],
            [
                'room_number' => 'R016',
                'name' => 'Penthouse Suite 601',
                'type' => 'Penthouse Suite',
                'capacity' => 4,
                'occupied' => 0,
                'floor' => 6,
                'price' => 120.00,
                'status' => 'available',
                'description' => 'Luxury penthouse suite with panoramic city views and premium amenities.',
                'image_url' => 'https://images.unsplash.com/photo-1595576508898-0ad5c879a061?w=800&h=600&fit=crop',
                'amenities' => ['Private Bathroom', 'Kitchenette', 'Free WiFi', 'Smart TV', 'Balcony', 'Panoramic View', 'Room Service', 'Mini Bar'],
                'last_cleaned' => now()->subHours(1),
                'user_id' => $admin2->id,
            ],
            [
                'room_number' => 'R017',
                'name' => '8-Bed Female Dorm I',
                'type' => 'Female Dormitory',
                'capacity' => 8,
                'occupied' => 6,
                'floor' => 2,
                'price' => 21.00,
                'status' => 'available',
                'description' => 'Modern female dormitory with en-suite facilities and study area.',
                'image_url' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&h=600&fit=crop',
                'amenities' => ['Free WiFi', 'AC', 'En-suite Bathroom', 'Study Area', 'Individual Lockers', 'Hair Dryer', 'Makeup Mirror'],
                'last_cleaned' => now()->subHours(2),
                'user_id' => $admin1->id,
            ],
            [
                'room_number' => 'R018',
                'name' => 'Private Quad 404',
                'type' => 'Private Quad',
                'capacity' => 4,
                'occupied' => 3,
                'floor' => 4,
                'price' => 80.00,
                'status' => 'available',
                'description' => 'Private quad room with four single beds and private bathroom.',
                'image_url' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=800&h=600&fit=crop',
                'amenities' => ['Four Single Beds', 'Private Bathroom', 'Free WiFi', 'TV', 'Wardrobe', 'City View', 'Work Desk'],
                'last_cleaned' => now()->subDays(1),
                'user_id' => $admin1->id,
            ],
            [
                'room_number' => 'R019',
                'name' => '16-Bed Mixed Dorm J',
                'type' => 'Mixed Dormitory',
                'capacity' => 16,
                'occupied' => 12,
                'floor' => 5,
                'price' => 16.00,
                'status' => 'available',
                'description' => 'Budget-friendly large dormitory with basic amenities and common area.',
                'image_url' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=800&h=600&fit=crop',
                'amenities' => ['Free WiFi', 'Individual Lockers', 'Common Area', 'Reading Lights', 'Large Windows'],
                'last_cleaned' => now()->subHours(5),
                'user_id' => $admin2->id,
            ],
            [
                'room_number' => 'R020',
                'name' => 'Accessible Suite 102',
                'type' => 'Accessible Suite',
                'capacity' => 2,
                'occupied' => 1,
                'floor' => 1,
                'price' => 60.00,
                'status' => 'available',
                'description' => 'Wheelchair accessible suite with adapted facilities and private bathroom.',
                'image_url' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&h=600&fit=crop',
                'amenities' => ['Wheelchair Accessible', 'Private Bathroom', 'Free WiFi', 'Adapted Facilities', 'Emergency Call Button', 'Wide Doorways'],
                'last_cleaned' => now()->subHours(1),
                'user_id' => $admin1->id,
            ],
        ];

        foreach ($rooms as $roomData) {
            Room::create($roomData);
        }

        // Create activities for both admins
        $this->command->info('Creating activities...');
        $activities = [
            // Admin 1 activities
            [
                'name' => 'City Walking Tour',
                'description' => 'Explore the historic city center with our knowledgeable local guide. Discover hidden gems, learn about local history, and experience the authentic culture of our beautiful city.',
                'short_description' => 'Guided walking tour through the historic city center',
                'price' => 25.00,
                'duration_minutes' => 120,
                'max_participants' => 15,
                'min_participants' => 3,
                'difficulty_level' => 'easy',
                'location' => 'City Center',
                'meeting_point' => 'Hostel Reception',
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'start_time' => '10:00',
                'end_time' => '12:00',
                'image_url' => 'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=800',
                'is_active' => true,
                'advance_booking_hours' => 24,
                'what_to_bring' => 'Comfortable walking shoes, water bottle, camera',
                'rating' => 4.8,
                'total_reviews' => 127,
                'user_id' => $admin1->id,
            ],
            [
                'name' => 'Mountain Hiking Adventure',
                'description' => 'Challenge yourself with a guided hike through scenic mountain trails. Enjoy breathtaking views, fresh mountain air, and the satisfaction of reaching the summit.',
                'short_description' => 'Guided mountain hike with stunning views',
                'price' => 45.00,
                'duration_minutes' => 300,
                'max_participants' => 8,
                'min_participants' => 2,
                'difficulty_level' => 'hard',
                'location' => 'Mountain Trail',
                'meeting_point' => 'Hostel Reception (Transport Provided)',
                'available_days' => ['saturday', 'sunday'],
                'start_time' => '08:00',
                'end_time' => '13:00',
                'image_url' => 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=800',
                'is_active' => true,
                'advance_booking_hours' => 48,
                'what_to_bring' => 'Hiking boots, backpack, water (2L), snacks, weather-appropriate clothing',
                'rating' => 4.9,
                'total_reviews' => 89,
                'user_id' => $admin1->id,
            ],
            [
                'name' => 'Cooking Class Experience',
                'description' => 'Learn to cook traditional local dishes with our expert chef. Hands-on cooking experience followed by enjoying the delicious meal you prepared.',
                'short_description' => 'Learn traditional cooking with local chef',
                'price' => 35.00,
                'duration_minutes' => 180,
                'max_participants' => 12,
                'min_participants' => 4,
                'difficulty_level' => 'easy',
                'location' => 'Hostel Kitchen',
                'meeting_point' => 'Hostel Kitchen',
                'available_days' => ['tuesday', 'thursday', 'saturday'],
                'start_time' => '18:00',
                'end_time' => '21:00',
                'image_url' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800',
                'is_active' => true,
                'advance_booking_hours' => 24,
                'what_to_bring' => 'Apron (provided), appetite for learning',
                'rating' => 4.7,
                'total_reviews' => 156,
                'user_id' => $admin1->id,
            ],
            // Admin 2 activities
            [
                'name' => 'Bike City Tour',
                'description' => 'Explore the city on two wheels! Cycle through parks, along rivers, and discover the best spots that are perfect for biking. Suitable for all fitness levels.',
                'short_description' => 'Guided bicycle tour around the city',
                'price' => 30.00,
                'duration_minutes' => 150,
                'max_participants' => 10,
                'min_participants' => 3,
                'difficulty_level' => 'moderate',
                'location' => 'City Parks & Riverside',
                'meeting_point' => 'Hostel Courtyard',
                'available_days' => ['monday', 'wednesday', 'friday', 'sunday'],
                'start_time' => '14:00',
                'end_time' => '16:30',
                'image_url' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800',
                'is_active' => true,
                'advance_booking_hours' => 12,
                'what_to_bring' => 'Comfortable clothes, helmet (provided), water bottle',
                'rating' => 4.6,
                'total_reviews' => 203,
                'user_id' => $admin2->id,
            ],
            [
                'name' => 'Photography Workshop',
                'description' => 'Improve your photography skills while exploring photogenic locations around the city. Learn composition, lighting, and editing techniques from a professional photographer.',
                'short_description' => 'Learn photography while exploring the city',
                'price' => 40.00,
                'duration_minutes' => 240,
                'max_participants' => 6,
                'min_participants' => 2,
                'difficulty_level' => 'moderate',
                'location' => 'Various City Locations',
                'meeting_point' => 'Hostel Lobby',
                'available_days' => ['saturday', 'sunday'],
                'start_time' => '09:00',
                'end_time' => '13:00',
                'image_url' => 'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?w=800',
                'is_active' => true,
                'advance_booking_hours' => 24,
                'what_to_bring' => 'Camera (DSLR or smartphone), comfortable walking shoes',
                'rating' => 4.9,
                'total_reviews' => 74,
                'user_id' => $admin2->id,
            ],
            [
                'name' => 'Pub Crawl Night',
                'description' => 'Experience the local nightlife with fellow travelers! Visit the best pubs and bars in the city, enjoy drink specials, and make new friends.',
                'short_description' => 'Guided tour of local pubs and bars',
                'price' => 20.00,
                'duration_minutes' => 240,
                'max_participants' => 20,
                'min_participants' => 5,
                'difficulty_level' => 'easy',
                'location' => 'City Center Pubs',
                'meeting_point' => 'Hostel Reception',
                'available_days' => ['friday', 'saturday'],
                'start_time' => '20:00',
                'end_time' => '00:00',
                'image_url' => 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?w=800',
                'is_active' => true,
                'advance_booking_hours' => 6,
                'what_to_bring' => 'Valid ID, comfortable shoes, good mood',
                'rating' => 4.5,
                'total_reviews' => 312,
                'user_id' => $admin2->id,
            ],
        ];

        foreach ($activities as $activityData) {
            Activity::create($activityData);
        }

        // Create guests
        $this->command->info('Creating guests...');
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
            [
                'first_name' => 'Alessandro',
                'last_name' => 'Rossi',
                'email' => 'alessandro.rossi@email.com',
                'phone' => '+39 06 1234 5678',
                'nationality' => 'Italian',
                'date_of_birth' => '1987-09-30',
                'id_type' => 'passport',
                'id_number' => 'IT987654321',
                'address' => 'Via Roma 123, Rome, Italy',
                'emergency_contact_name' => 'Giulia Rossi',
                'emergency_contact_phone' => '+39 06 1234 5679',
            ],
            [
                'first_name' => 'Chen',
                'last_name' => 'Wei',
                'email' => 'chen.wei@email.com',
                'phone' => '+86 10 1234 5678',
                'nationality' => 'Chinese',
                'date_of_birth' => '1993-04-12',
                'id_type' => 'passport',
                'id_number' => 'CN123456789',
                'address' => '123 Wangfujing Street, Beijing, China',
                'emergency_contact_name' => 'Li Wei',
                'emergency_contact_phone' => '+86 10 1234 5679',
            ],
        ];

        foreach ($guests as $guestData) {
            Guest::create($guestData);
        }

        // Get all created data
        $allRooms = Room::all();
        $allGuests = Guest::all();
        $allActivities = Activity::all();

        // Create room bookings with realistic data
        $this->command->info('Creating room bookings...');
        $bookingStatuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'];
        $bookingCount = 0;

        foreach ($allGuests as $guest) {
            // Each guest can have 1-3 bookings
            $numBookings = rand(1, 3);
            
            for ($i = 0; $i < $numBookings; $i++) {
                $room = $allRooms->random();
                $checkIn = Carbon::now()->addDays(rand(-30, 30));
                $checkOut = $checkIn->copy()->addDays(rand(1, 14));
                $status = $bookingStatuses[array_rand($bookingStatuses)];
                
                // Adjust status based on dates
                if ($checkIn->isPast() && $checkOut->isFuture()) {
                    $status = 'checked_in';
                } elseif ($checkOut->isPast()) {
                    $status = 'checked_out';
                } elseif ($checkIn->isFuture()) {
                    $status = rand(0, 1) ? 'confirmed' : 'pending';
                }

                $nights = $checkIn->diffInDays($checkOut);
                $totalAmount = $room->price * $nights;

                Booking::create([
                    'booking_reference' => 'BK-' . strtoupper(Str::random(8)),
                    'guest_id' => $guest->id,
                    'room_id' => $room->id,
                    'check_in_date' => $checkIn,
                    'check_out_date' => $checkOut,
                    'number_of_guests' => rand(1, min(4, $room->capacity)),
                    'total_amount' => $totalAmount,
                    'status' => $status,
                    'special_requests' => rand(0, 1) ? 'Late check-in requested' : null,
                    'confirmed_at' => $status === 'confirmed' || $status === 'checked_in' || $status === 'checked_out' ? now() : null,
                    'checked_in_at' => $status === 'checked_in' || $status === 'checked_out' ? now() : null,
                    'checked_out_at' => $status === 'checked_out' ? now() : null,
                ]);
                
                $bookingCount++;
            }
        }

        // Create activity bookings
        $this->command->info('Creating activity bookings...');
        $activityBookingStatuses = ['pending', 'confirmed', 'cancelled'];
        $activityBookingCount = 0;

        foreach ($allGuests as $guest) {
            // Each guest can have 0-2 activity bookings
            $numActivityBookings = rand(0, 2);
            
            for ($i = 0; $i < $numActivityBookings; $i++) {
                $activity = $allActivities->random();
                $bookingDate = Carbon::now()->addDays(rand(-10, 30));
                $status = $activityBookingStatuses[array_rand($activityBookingStatuses)];
                
                // Adjust status based on dates
                if ($bookingDate->isPast()) {
                    $status = 'confirmed';
                } elseif ($bookingDate->isFuture()) {
                    $status = rand(0, 1) ? 'confirmed' : 'pending';
                }

                $participants = rand($activity->min_participants, min($activity->max_participants, 4));
                $totalAmount = $activity->price * $participants;

                ActivityBooking::create([
                    'activity_id' => $activity->id,
                    'guest_id' => $guest->id,
                    'booking_reference' => 'ACT-' . strtoupper(Str::random(8)),
                    'booking_date' => $bookingDate,
                    'booking_time' => $activity->start_time,
                    'participants' => $participants,
                    'per_person_price' => $activity->price,
                    'total_amount' => $totalAmount,
                    'status' => $status,
                    'special_requests' => rand(0, 1) ? 'First time visitor' : null,
                    'confirmed_at' => $status === 'confirmed' ? now() : null,
                ]);
                
                $activityBookingCount++;
            }
        }

        // Update room occupancy based on actual bookings
        $this->command->info('Updating room occupancy...');
        foreach ($allRooms as $room) {
            $room->syncOccupancy();
        }

        $this->command->info("Database seeding completed successfully!");
        $this->command->info("- Created 2 admin users");
        $this->command->info("- Created {$allRooms->count()} rooms");
        $this->command->info("- Created {$allActivities->count()} activities");
        $this->command->info("- Created {$allGuests->count()} guests");
        $this->command->info("- Created {$bookingCount} room bookings");
        $this->command->info("- Created {$activityBookingCount} activity bookings");
        $this->command->info("");
        $this->command->info("Admin login credentials:");
        $this->command->info("Admin 1: admin1@hostel.com / admin123");
        $this->command->info("Admin 2: admin2@hostel.com / admin123");
    }
}