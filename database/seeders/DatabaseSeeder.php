<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(AdminUserSeeder::class);
        $this->call(PostSeeder::class);

        \App\Models\Poll::firstOrCreate(
            ['question' => 'What new food option do you want in the cafeteria next semester?'],
            ['options' => ['Sushi Bar', 'Taco Stand', 'Vegan Deli'], 'is_active' => true],
        );
    }
}
