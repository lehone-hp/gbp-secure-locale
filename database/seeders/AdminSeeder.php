<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::admin()->count() > 0) {
            return;
        }

        User::create([
            'name'      => 'System Admin',
            'email'     => 'admin@gbp-securelocal.com',
            'password'  => Hash::make('password'),
            'role'      => User::ROLE_ADMIN
        ]);
    }
}
