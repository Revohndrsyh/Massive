<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // factory-generated user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // manual user
        User::create([
            'user_id' => Str::upper(Str::random(10)),
            'name' => 'Manual User',
            'email' => 'manual@example.com',
            'password' => Hash::make('secret123'),
            'no_telp' => '081234567890',
            'nama_usaha' => 'Manual Business',
            'kategori_usaha' => 'Service',
        ]);
    }
}
