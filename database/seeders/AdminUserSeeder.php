<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin user already exists
        $existingAdmin = Admin::where('email', 'admin@happyhostel.com')->first();
        
        if ($existingAdmin) {
            $this->command->info('Admin user already exists!');
            $this->command->info('Email: admin@happyhostel.com');
            $this->command->info('Password: admin123456');
            return;
        }

        // Create admin user
        $admin = Admin::create([
            'username' => 'admin',
            'email' => 'admin@happyhostel.com',
            'full_name' => 'Admin User',
            'password' => Hash::make('admin123456'),
            'role' => 'admin',
        ]);

        $this->command->info('✅ Admin user created successfully!');
        $this->command->info('📧 Email: admin@happyhostel.com');
        $this->command->info('🔑 Password: admin123456');
        $this->command->info('');
        $this->command->info('You can now login to the admin dashboard at: /login');
    }
}