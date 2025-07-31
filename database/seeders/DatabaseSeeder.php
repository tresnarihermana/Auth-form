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
        $this->call([
            RolePermissionSeeder::class,
        ]);
        // User::factory(10)->create();
        $Sadmin = User::factory()->create([
            'name' => 'SuperAdmin',
            'username' => 'Sadmin',
            'email' => 'sadmin@gmail.com',
            'email_verified_at' => now(),
        ]);
        $Sadmin->assignRole('Super Admin');

        $admin = User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');
        
        $operator = User::factory()->create([
            'name' => 'Operator',
            'username' => 'operator',
            'email' => 'operator@gmail.com',
            'email_verified_at' => now(),
        ]);
        $operator->assignRole('operator');
        
        $user = User::factory()->create([
            'name' => 'First User',
            'username' => 'user',
            'email' => 'user@gmail.com',
            'email_verified_at' => now(),
        ]);
        $user->assignRole('user');


     User::factory(100)->create()->each(function ($user) {
            $user->assignRole('user');
        });
    }
}
