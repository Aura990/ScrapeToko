<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'role' => 'admin',
                'password' => 'admin123',
            ],
            [
                'name' => 'Karyawan Satu',
                'email' => 'karyawan@gmail.com',
                'role' => 'karyawan',
                'password' => 'karyawan',
            ],
            [
                'name' => 'Karyawan Dua',
                'email' => 'karyawan2@gmail.com',
                'role' => 'karyawan',
                'password' => 'karyawan',
            ],
            [
                'name' => 'Karyawan Tiga',
                'email' => 'karyawan3@gmail.com',
                'role' => 'karyawan',
                'password' => 'karyawan',
            ],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'password' => Hash::make($user['password']),
            ]);
        }
    }
}
