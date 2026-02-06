<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Create the default Super Admin account and assign the admin role.
     *
     * Idempotent — safe to run multiple times.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@natakahii.com'],
            [
                'name' => 'Super Admin',
                'password' => 'Natakahii@2026#', // Auto-hashed by the User model's "hashed" cast
                'status' => 'active',
            ]
        );

        $adminRole = Role::where('name', 'admin')->first();

        if ($adminRole) {
            $admin->roles()->syncWithoutDetaching([$adminRole->id]);
        }
    }
}
