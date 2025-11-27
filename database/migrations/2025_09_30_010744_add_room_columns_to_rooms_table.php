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
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('room_number')->unique()->after('id');
            $table->string('name')->after('room_number');
            $table->string('type')->after('name');
            $table->integer('capacity')->after('type');
            $table->integer('occupied')->default(0)->after('capacity');
            $table->integer('floor')->after('occupied');
            $table->decimal('price', 8, 2)->after('floor');
            $table->enum('status', ['available', 'occupied', 'full', 'maintenance'])->default('available')->after('price');
            $table->text('description')->nullable()->after('status');
            $table->json('amenities')->nullable()->after('description');
            $table->timestamp('last_cleaned')->nullable()->after('amenities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn([
                'room_number',
                'name',
                'type',
                'capacity',
                'occupied',
                'floor',
                'price',
                'status',
                'description',
                'amenities',
                'last_cleaned'
            ]);
        });
    }
};
