<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command?->warn('Skipping admin seeder. Set ADMIN_EMAIL and ADMIN_PASSWORD to create/update admin user.');
        } else {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => env('ADMIN_NAME', 'Administrator'),
                    'password' => Hash::make($password),
                    'role' => 'admin',
                    'email_verified_at' => now(),
                ]
            );

            $this->command?->info("Admin user ready: {$email}");
        }

        $this->call(MockSurveyDataSeeder::class);
    }
}
