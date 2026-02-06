<?php

use Database\Seeders\RoleSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Seed roles and the default super admin user.
     * Both seeders are idempotent — safe to run multiple times.
     */
    public function up(): void
    {
        Artisan::call('db:seed', [
            '--class' => RoleSeeder::class,
            '--force' => true,
        ]);

        Artisan::call('db:seed', [
            '--class' => SuperAdminSeeder::class,
            '--force' => true,
        ]);
    }

    /**
     * No rollback needed — user and role data should persist.
     */
    public function down(): void
    {
        //
    }
};
