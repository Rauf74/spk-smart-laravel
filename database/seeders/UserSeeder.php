<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

/**
 * Seeder untuk data user (Guru BK dan Siswa).
 * Data sesuai dengan spk_smart_pg_neon.sql
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'id_user' => 1,
                'nama_user' => 'Guru BK',
                'jenis_kelamin' => 'Laki-laki',
                'username' => 'gurubk1',
                'password' => bcrypt('123456'),
                'role' => 'Guru BK',
                'nis' => null,
                'is_logged_in' => false,
            ],
            [
                'id_user' => 2,
                'nama_user' => 'Yusup Faisal Ikhsan',
                'jenis_kelamin' => 'Laki-laki',
                'username' => 'siswa1',
                'password' => bcrypt('siswa123'),
                'role' => 'Siswa',
                'nis' => '12345',
                'is_logged_in' => false,
            ],
            [
                'id_user' => 3,
                'nama_user' => 'Siswa 2',
                'jenis_kelamin' => 'Laki-laki',
                'username' => 'siswa2',
                'password' => bcrypt('siswa123'),
                'role' => 'Siswa',
                'nis' => '12346',
                'is_logged_in' => false,
            ],
            [
                'id_user' => 4,
                'nama_user' => 'Siswa 3',
                'jenis_kelamin' => 'Perempuan',
                'username' => 'siswa3',
                'password' => bcrypt('siswa123'),
                'role' => 'Siswa',
                'nis' => '12347',
                'is_logged_in' => false,
            ],
            [
                'id_user' => 5,
                'nama_user' => 'Danny Bungai',
                'jenis_kelamin' => 'Laki-laki',
                'username' => 'siswa4',
                'password' => bcrypt('siswa123'),
                'role' => 'Siswa',
                'nis' => '12348',
                'is_logged_in' => false,
            ],
            [
                'id_user' => 6,
                'nama_user' => 'Dafit Muttaqin',
                'jenis_kelamin' => 'Laki-laki',
                'username' => 'siswa5',
                'password' => bcrypt('siswa123'),
                'role' => 'Siswa',
                'nis' => '12349',
                'is_logged_in' => false,
            ],
            [
                'id_user' => 7,
                'nama_user' => 'Danny Bungai',
                'jenis_kelamin' => 'Laki-laki',
                'username' => 'siswa240',
                'password' => bcrypt('1234567'),
                'role' => 'Guru BK',
                'nis' => '12349',
                'is_logged_in' => false,
            ],
            [
                'id_user' => 8,
                'nama_user' => 'chihuahuaw',
                'jenis_kelamin' => 'Laki-laki',
                'username' => 'wawa',
                'password' => bcrypt('1234567'),
                'role' => 'Siswa',
                'nis' => '12222',
                'is_logged_in' => false,
            ],
            [
                'id_user' => 10,
                'nama_user' => 'wwwwwwwww',
                'jenis_kelamin' => null,
                'username' => 'wwwwww',
                'password' => bcrypt('123456'),
                'role' => 'Siswa',
                'nis' => null,
                'is_logged_in' => false,
            ],
            [
                'id_user' => 11,
                'nama_user' => 'Rauf',
                'jenis_kelamin' => 'Laki-laki',
                'username' => 'eunzoo',
                'password' => bcrypt('123456'),
                'role' => 'Guru BK',
                'nis' => null,
                'is_logged_in' => false,
            ],
            [
                'id_user' => 12,
                'nama_user' => 'Rauf',
                'jenis_kelamin' => 'Laki-laki',
                'username' => 'knight123',
                'password' => bcrypt('12345678'),
                'role' => 'Siswa',
                'nis' => '211011401693',
                'is_logged_in' => false,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
