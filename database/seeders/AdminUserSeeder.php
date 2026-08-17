<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Create a default admin account (skipped if the email already exists).
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@mading.test'],
            [
                'name' => 'Admin Mading',
                'password' => 'password', // hashed automatically by the model cast
                'is_admin' => true,
            ]
        );
    }
}
