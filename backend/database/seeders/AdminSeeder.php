<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * The admin login for /admin. Override the defaults with ADMIN_EMAIL / ADMIN_PASSWORD in .env;
 * in production the default password is refused, so the live site never ships with it.
 */
class AdminSeeder extends Seeder
{
    private const DEFAULT_PASSWORD = 'password123';

    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'abdurdiary@gmail.com');
        $password = env('ADMIN_PASSWORD', self::DEFAULT_PASSWORD);

        if (app()->isProduction() && $password === self::DEFAULT_PASSWORD) {
            $this->command?->error('Set a strong ADMIN_PASSWORD in .env before seeding the admin in production.');

            return;
        }

        User::updateOrCreate(['email' => $email], ['name' => 'Abdur Rahman', 'password' => $password]);
    }
}
