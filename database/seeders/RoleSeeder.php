<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the default application roles.
     *
     * Idempotent — safe to run multiple times.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'description' => 'Full system access. Manages users, vendors, and platform settings.'],
            ['name' => 'vendor', 'description' => 'Sells products on the platform. Manages own inventory and orders.'],
            ['name' => 'customer', 'description' => 'Default role. Browses and purchases products.'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }
}
