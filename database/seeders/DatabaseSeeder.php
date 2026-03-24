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

        $admin = User::create([
            'name' => 'Admin Postelio',
            'email' => 'admin@postelio.com',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            'is_admin' => true,
            'is_premium' => true,
            'email_verified_at' => now(),
        ]);

        $admin->company()->create([
            'name' => 'Postelio HQ',
            'city' => 'Paris',
        ]);
    }
}
