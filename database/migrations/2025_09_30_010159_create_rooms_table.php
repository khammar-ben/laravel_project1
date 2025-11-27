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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->string('name');
            $table->string('type'); // Mixed Dormitory, Female Dormitory, Private Single, Private Double, etc.
            $table->integer('capacity');
            $table->integer('occupied')->default(0);
            $table->integer('floor');
            $table->decimal('price', 8, 2);
            $table->enum('status', ['available', 'occupied', 'full', 'maintenance'])->default('available');
            $table->text('description')->nullable();
            $table->json('amenities')->nullable(); // Store amenities as JSON array
            $table->timestamp('last_cleaned')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
