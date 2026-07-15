<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

/**
 * Seeder data user dummy (Guru BK & Siswa) untuk keperluan demo/development.
 * Tidak memakai data asli. Password semua: "password".
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'id_user' => 1,
                'nama_user' => 'Guru BK Demo',
                'jenis_kelamin' => 'Laki-laki',
                'username' => 'gurubk',
                'password' => bcrypt('password'),
                'role' => 'Guru BK',
                'nis' => null,
                'is_logged_in' => false,
            ],
            [
                'id_user' => 2,
                'nama_user' => 'Siswa Demo 1',
                'jenis_kelamin' => 'Laki-laki',
                'username' => 'siswa01',
                'password' => bcrypt('password'),
                'role' => 'Siswa',
                'nis' => '10001',
                'is_logged_in' => false,
            ],
            [
                'id_user' => 3,
                'nama_user' => 'Siswa Demo 2',
                'jenis_kelamin' => 'Perempuan',
                'username' => 'siswa02',
                'password' => bcrypt('password'),
                'role' => 'Siswa',
                'nis' => '10002',
                'is_logged_in' => false,
            ],
            [
                'id_user' => 4,
                'nama_user' => 'Siswa Demo 3',
                'jenis_kelamin' => 'Laki-laki',
                'username' => 'siswa03',
                'password' => bcrypt('password'),
                'role' => 'Siswa',
                'nis' => '10003',
                'is_logged_in' => false,
            ],
            [
                'id_user' => 5,
                'nama_user' => 'Siswa Demo 4',
                'jenis_kelamin' => 'Perempuan',
                'username' => 'siswa04',
                'password' => bcrypt('password'),
                'role' => 'Siswa',
                'nis' => '10004',
                'is_logged_in' => false,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['username' => $user['username']], $user);
        }
    }
}
