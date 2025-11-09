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
        // Create some test users with known passwords for testing
        User::create([
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com',
            'password' => bcrypt('password'),
        ]);

        User::create([
            'name' => 'Bob Smith',
            'email' => 'bob@example.com',
            'password' => bcrypt('password'),
        ]);

        User::create([
            'name' => 'Charlie Brown',
            'email' => 'charlie@example.com',
            'password' => bcrypt('password'),
        ]);

        User::create([
            'name' => 'Diana Prince',
            'email' => 'diana@example.com',
            'password' => bcrypt('password'),
        ]);

        User::create([
            'name' => 'Eve Wilson',
            'email' => 'eve@example.com',
            'password' => bcrypt('password'),
        ]);
    }
}
