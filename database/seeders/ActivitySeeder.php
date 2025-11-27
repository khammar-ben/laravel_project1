<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\User;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin users to assign activities to
        $adminUsers = User::whereIn('email', ['admin@happyhostel.com'])->get();
        
        if ($adminUsers->isEmpty()) {
            $this->command->error('Admin users not found. Please run User seeder first.');
            return;
        }

        $activities = [
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
            ],
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
            ],
        ];

        foreach ($activities as $index => $activity) {
            // Assign activities to different admins
            $activity['user_id'] = $adminUsers[$index % $adminUsers->count()]->id;
            Activity::create($activity);
        }
    }
}