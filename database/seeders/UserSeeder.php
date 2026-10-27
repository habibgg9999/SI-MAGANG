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
        // 1. Akun Koordinator Magang
        User::firstOrCreate(
            ['email' => 'koordinator@simagang.ac.id'],
            [
                'nim' => null,
                'name' => 'Dr. Ir. Hendra Wijaya, M.Kom.',
                'phone' => '081234567890',
                'prodi' => 'Teknik Informatika',
                'angkatan' => null,
                'role' => 'koordinator',
                'password' => Hash::make('password123'),
                'avatar' => null,
            ]
        );

        // Koordinator cadangan / staf
        User::firstOrCreate(
            ['email' => 'admin.magang@simagang.ac.id'],
            [
                'nim' => null,
                'name' => 'Staf Administrasi Magang',
                'phone' => '081234567899',
                'prodi' => 'Sistem Informasi',
                'angkatan' => null,
                'role' => 'koordinator',
                'password' => Hash::make('password123'),
                'avatar' => null,
            ]
        );

        // 2. Akun Mahasiswa Dummy
        $mahasiswaList = [
            [
                'nim' => '21081010001',
                'name' => 'Muhammad Habib Hanafi',
                'email' => 'habib@mahasiswa.ac.id',
                'phone' => '082198765432',
                'prodi' => 'Teknik Informatika',
                'angkatan' => '2021',
                'role' => 'mahasiswa',
                'password' => Hash::make('password123'),
                'avatar' => null,
            ],
            [
                'nim' => '21081010002',
                'name' => 'Ahmad Fauzi',
                'email' => 'fauzi@mahasiswa.ac.id',
                'phone' => '082198765433',
                'prodi' => 'Teknik Informatika',
                'angkatan' => '2021',
                'role' => 'mahasiswa',
                'password' => Hash::make('password123'),
                'avatar' => null,
            ],
            [
                'nim' => '21081010003',
                'name' => 'Siti Rahmawati',
                'email' => 'siti@mahasiswa.ac.id',
                'phone' => '082198765434',
                'prodi' => 'Sistem Informasi',
                'angkatan' => '2021',
                'role' => 'mahasiswa',
                'password' => Hash::make('password123'),
                'avatar' => null,
            ],
            [
                'nim' => '22081010004',
                'name' => 'Budi Pratama',
                'email' => 'budi@mahasiswa.ac.id',
                'phone' => '082198765435',
                'prodi' => 'Teknik Informatika',
                'angkatan' => '2022',
                'role' => 'mahasiswa',
                'password' => Hash::make('password123'),
                'avatar' => null,
            ],
            [
                'nim' => '22081010005',
                'name' => 'Putri Ayu Lestari',
                'email' => 'putri@mahasiswa.ac.id',
                'phone' => '082198765436',
                'prodi' => 'Sistem Informasi',
                'angkatan' => '2022',
                'role' => 'mahasiswa',
                'password' => Hash::make('password123'),
                'avatar' => null,
            ],
        ];

        foreach ($mahasiswaList as $mhs) {
            User::firstOrCreate(
                ['email' => $mhs['email']],
                $mhs
            );
        }
    }
}
