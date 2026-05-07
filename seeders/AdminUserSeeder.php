<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@shop.com'],
            [
                'name'     => 'Administrateur',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        // Compte client de test
        User::firstOrCreate(
            ['email' => 'client@shop.com'],
            [
                'name'     => 'Client Test',
                'password' => Hash::make('password'),
                'role'     => 'user',
            ]
        );
    }
}
