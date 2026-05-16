<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'superadmin@umng.com'],
            [
                'name'       => 'Super Admin',
                'password'          => Hash::make('password'),
                'role'              => 'super_admin',
                'status'            => 'active',
                'company_id'        => null,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Super Admin created: superadmin@umng.com / password');
    }
}
