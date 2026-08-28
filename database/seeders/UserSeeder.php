<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@kotomalintang.id'],
            [
                'name' => 'Administrator Nagari',
                'username' => 'admin',
                'nik' => '1306010101010001',
                'phone' => '081234567890',
                'address' => 'Kantor Wali Nagari Koto Malintang',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        // Wali Nagari
        User::firstOrCreate(
            ['email' => 'wali@kotomalintang.id'],
            [
                'name' => 'Wali Nagari Koto Malintang',
                'username' => 'walinagari',
                'nik' => '1306010101010002',
                'phone' => '081234567891',
                'address' => 'Nagari Koto Malintang, Kec. Tanjung Raya',
                'role' => 'wali_nagari',
                'password' => Hash::make('password'),
            ]
        );

        // Contoh Warga
        User::firstOrCreate(
            ['email' => 'warga@kotomalintang.id'],
            [
                'name' => 'Budi Santoso',
                'username' => 'warga',
                'nik' => '1306012345670001',
                'phone' => '081234567892',
                'address' => 'Jorong Muko-Muko, Nagari Koto Malintang',
                'role' => 'warga',
                'password' => Hash::make('password'),
            ]
        );
    }
}
