<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'username' => 'Ducky Gosh',
            'email' => 'ducky.gosh@gmail.com',
        ]);
        User::factory()->create([
            'username' => 'User',
            'email' => 'User@gmail.com',
        ]);
    }
}
