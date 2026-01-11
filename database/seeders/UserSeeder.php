<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'name' => 'Administrator',
            'password' => Hash::make('password'), // simple password for testing
            'role' => 'admin',
        ]);

        User::create([
            'username' => 'tentor',
            'name' => 'Tentor 1',
            'password' => Hash::make('password'),
            'role' => 'tentor',
        ]);

        User::create([
            'username' => 'rizky',
            'name' => 'Rizky Tentor',
            'password' => Hash::make('password'),
            'role' => 'tentor',
        ]);
    }
}
