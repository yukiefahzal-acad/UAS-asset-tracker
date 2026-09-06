<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Cleaned up: Remains only Super Admin account (sadmin / yukisadmin).
     */
    public function run(): void
    {
        // Super Admin Account (Auto Approved)
        User::create([
            'name' => 'sadmin',
            'email' => 'sadmin@assettrack.com',
            'password' => Hash::make('yukisadmin'),
            'role' => 'superadmin',
            'status' => 'approved',
            'approved_by' => 'SYSTEM',
            'approved_at' => now(),
        ]);
    }
}
