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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('offer_code')->unique();
            $table->string('name');
            $table->text('description');
            $table->enum('type', ['group-discount', 'solo-discount', 'length-discount', 'student-discount', 'early-booking', 'seasonal', 'loyalty']);
            $table->string('discount_type'); // percentage, fixed_amount, free_night
            $table->decimal('discount_value', 10, 2);
            $table->integer('min_guests')->default(1);
            $table->integer('min_nights')->nullable();
            $table->integer('max_uses')->nullable();
            $table->integer('used_count')->default(0);
            $table->date('valid_from');
            $table->date('valid_to');
            $table->enum('status', ['active', 'scheduled', 'expired', 'paused', 'cancelled'])->default('active');
            $table->boolean('is_public')->default(true);
            $table->json('conditions')->nullable(); // Additional conditions as JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
