<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Offer;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offers = [
            [
                'offer_code' => 'EARLY20',
                'name' => 'Early Bird Special',
                'description' => 'Book 30 days in advance and save 20% on your stay! Perfect for planning ahead.',
                'type' => 'early-booking',
                'discount_type' => 'percentage',
                'discount_value' => 20.00,
                'min_guests' => 1,
                'min_nights' => 2,
                'max_uses' => 100,
                'used_count' => 15,
                'valid_from' => '2024-01-01',
                'valid_to' => '2024-12-31',
                'status' => 'active',
                'is_public' => true,
                'conditions' => ['advance_booking' => true, 'min_days_ahead' => 30]
            ],
            [
                'offer_code' => 'GROUP15',
                'name' => 'Group Discount',
                'description' => 'Traveling with 4+ people? Get 15% off your entire booking when you book together.',
                'type' => 'group-discount',
                'discount_type' => 'percentage',
                'discount_value' => 15.00,
                'min_guests' => 4,
                'min_nights' => null,
                'max_uses' => 200,
                'used_count' => 42,
                'valid_from' => '2024-01-01',
                'valid_to' => '2024-12-31',
                'status' => 'active',
                'is_public' => true,
                'conditions' => ['group_booking' => true, 'min_guests' => 4]
            ],
            [
                'offer_code' => 'STUDENT10',
                'name' => 'Student Special',
                'description' => 'Valid student ID required. Get 10% off any room type with your student discount.',
                'type' => 'student-discount',
                'discount_type' => 'percentage',
                'discount_value' => 10.00,
                'min_guests' => 1,
                'min_nights' => null,
                'max_uses' => 150,
                'used_count' => 0,
                'valid_from' => '2024-01-01',
                'valid_to' => '2024-12-31',
                'status' => 'active',
                'is_public' => true,
                'conditions' => ['student_id_required' => true]
            ],
            [
                'offer_code' => 'LONGSTAY',
                'name' => 'Extended Stay',
                'description' => 'Stay 7+ nights and get 25% off your entire booking. Perfect for long-term travelers.',
                'type' => 'length-discount',
                'discount_type' => 'percentage',
                'discount_value' => 25.00,
                'min_guests' => 1,
                'min_nights' => 7,
                'max_uses' => 50,
                'used_count' => 8,
                'valid_from' => '2024-01-01',
                'valid_to' => '2024-12-31',
                'status' => 'active',
                'is_public' => true,
                'conditions' => ['extended_stay' => true, 'min_nights' => 7]
            ],
            [
                'offer_code' => 'WEEKEND',
                'name' => 'Weekend Getaway',
                'description' => 'Book Friday to Sunday and get $10 off per night. Perfect for weekend trips!',
                'type' => 'seasonal',
                'discount_type' => 'fixed_amount',
                'discount_value' => 10.00,
                'min_guests' => 1,
                'min_nights' => 2,
                'max_uses' => 100,
                'used_count' => 23,
                'valid_from' => '2024-01-01',
                'valid_to' => '2024-12-31',
                'status' => 'active',
                'is_public' => true,
                'conditions' => ['weekend_booking' => true, 'min_nights' => 2]
            ],
            [
                'offer_code' => 'FIRSTTIME',
                'name' => 'First Time Guest',
                'description' => 'New to our hostel? Get 15% off your first booking with us. Welcome bonus!',
                'type' => 'loyalty',
                'discount_type' => 'percentage',
                'discount_value' => 15.00,
                'min_guests' => 1,
                'min_nights' => null,
                'max_uses' => null,
                'used_count' => 67,
                'valid_from' => '2024-01-01',
                'valid_to' => '2024-12-31',
                'status' => 'active',
                'is_public' => true,
                'conditions' => ['first_time_guest' => true]
            ]
        ];

        foreach ($offers as $offer) {
            Offer::create($offer);
        }
    }
}
