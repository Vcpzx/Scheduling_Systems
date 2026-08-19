<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Resource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin / Secretary User
        User::create([
            'user_id' => 'SEC-001',
            'name' => 'Registrar Office',
            'password' => Hash::make('admin123'),
            'role' => 'secretary',
            'status' => 'approved',
        ]);

        // Sample Resources
        Resource::create(['name' => 'Lab 101', 'type' => 'lab', 'location' => 'Building A', 'capacity' => 30]);
        Resource::create(['name' => 'Room 204', 'type' => 'room', 'location' => 'Building B', 'capacity' => 45]);
        Resource::create(['name' => 'Auditorium', 'type' => 'facility', 'location' => 'Main Hall', 'capacity' => 200]);
        Resource::create(['name' => 'Projector Set A', 'type' => 'equipment', 'location' => 'AV Room', 'capacity' => null]);
    }
}