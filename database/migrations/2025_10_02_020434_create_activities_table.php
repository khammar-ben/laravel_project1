<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('duration_minutes'); // Duration in minutes
            $table->integer('max_participants');
            $table->integer('min_participants')->default(1);
            $table->enum('difficulty_level', ['easy', 'moderate', 'hard'])->default('easy');
            $table->json('included_items')->nullable(); // What's included (equipment, guide, etc.)
            $table->json('requirements')->nullable(); // Age limits, fitness level, etc.
            $table->string('location')->nullable();
            $table->string('meeting_point')->nullable();
            $table->json('available_days')->nullable(); // Days of week when available
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('image_url')->nullable();
            $table->json('gallery_images')->nullable(); // Additional images
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_booking')->default(true);
            $table->integer('advance_booking_hours')->default(24); // How many hours in advance to book
            $table->text('cancellation_policy')->nullable();
            $table->text('what_to_bring')->nullable();
            $table->decimal('rating', 3, 2)->default(0.00); // Average rating
            $table->integer('total_reviews')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};