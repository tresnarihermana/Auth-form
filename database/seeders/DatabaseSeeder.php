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
            'name' => 'Ducky Gosh',
            'username' => 'Ducky_Gosh',
            'email' => 'ducky.gosh@gmail.com',
        ]);
        User::factory()->create([
            'name' => 'User Pertama',
            'username' => 'User_pertama',
            'email' => 'User@gmail.com',
        ]);
    }
}
