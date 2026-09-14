<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            [
                'email' => 'admin@school.com',
            ],
            [
                'name' => 'School Admin',
                'password' => Hash::make('Admin@12345'),
            ]
        );

        $user->assignRole('Admin');
    }
}
