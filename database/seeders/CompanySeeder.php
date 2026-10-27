<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $koordinator = User::where('role', 'koordinator')->first();
        $mahasiswa = User::where('role', 'mahasiswa')->first();

        $companies = [
            // 1. Pemerintah
            [
                'name' => 'Dinas Komunikasi, Informatika dan Statistik Provinsi',
                'address' => 'Jl. Pahlawan No. 45, Kompleks Perkantoran Pemerintah',
                'type' => 'pemerintah',
                'field' => 'Teknologi Informasi & Tata Kelola SPBE',
                'city' => 'Semarang',
                'quota' => 6,
                'contact_person' => 'Bambang Triatmojo, S.Kom.',
                'contact_phone' => '081223344551',
                'is_active' => true,
                'is_approved' => true,
                'requested_by' => null,
            ],
            [
                'name' => 'Badan Perencanaan Pembangunan Daerah (Bappeda)',
                'address' => 'Jl. Pemuda No. 12',
                'type' => 'pemerintah',
                'field' => 'Analisis Data & Sistem Informasi Perencanaan',
                'city' => 'Jakarta',
                'quota' => 4,
                'contact_person' => 'Sri Wahyuni, M.Si.',
                'contact_phone' => '081223344552',
                'is_active' => true,
                'is_approved' => true,
                'requested_by' => null,
            ],

            // 2. BUMN
            [
                'name' => 'PT Telkom Indonesia (Persero) Tbk - Digital Service Division',
                'address' => 'Telkom Landmark Tower, Lt. 28, Jl. Jend. Gatot Subroto Kav. 52',
                'type' => 'BUMN',
                'field' => 'Telekomunikasi, Cloud & Software Engineering',
                'city' => 'Jakarta Selatan',
                'quota' => 10,
                'contact_person' => 'Raditya Pratama (HR Talent Acquisition)',
                'contact_phone' => '081233445566',
                'is_active' => true,
                'is_approved' => true,
                'requested_by' => null,
            ],
            [
                'name' => 'PT Bank Rakyat Indonesia (Persero) Tbk - Divisi IT & Digital Banking',
                'address' => 'Gedung BRI 2, Jl. Jend. Sudirman Kav. 44-46',
                'type' => 'BUMN',
                'field' => 'Fintech, Cybersecurity & Data Analytics',
                'city' => 'Jakarta Pusat',
                'quota' => 8,
                'contact_person' => 'Dewi Anggraini',
                'contact_phone' => '081234556677',
                'is_active' => true,
                'is_approved' => true,
                'requested_by' => null,
            ],

            // 3. Swasta
            [
                'name' => 'PT GoTo Gojek Tokopedia Tbk',
                'address' => 'Pasaraya Blok M Gedung B Lt. 6-7, Jl. Iskandarsyah II No. 2',
                'type' => 'swasta',
                'field' => 'E-Commerce, Logistics & Fullstack Engineering',
                'city' => 'Jakarta Selatan',
                'quota' => 12,
                'contact_person' => 'Kevin Sanjaya (University Relations)',
                'contact_phone' => '081399887766',
                'is_active' => true,
                'is_approved' => true,
                'requested_by' => null,
            ],
            [
                'name' => 'PT Gamatechno Indonesia',
                'address' => 'Jl. Kaliurang KM 5, Gang Pandega Padma No. 7',
                'type' => 'swasta',
                'field' => 'Smart City Solutions & Web Development',
                'city' => 'Yogyakarta',
                'quota' => 5,
                'contact_person' => 'Aditya Nugraha',
                'contact_phone' => '081322114455',
                'is_active' => true,
                'is_approved' => true,
                'requested_by' => null,
            ],

            // 4. NGO
            [
                'name' => 'Yayasan Rumah Coding & Edukasi Terbuka',
                'address' => 'Jl. Gejayan No. 88, Caturtunggal',
                'type' => 'NGO',
                'field' => 'Edukasi Teknologi & Platform Open Source',
                'city' => 'Sleman',
                'quota' => 4,
                'contact_person' => 'Faisal Akbar (Ketua Divisi Program)',
                'contact_phone' => '081566778899',
                'is_active' => true,
                'is_approved' => true,
                'requested_by' => $mahasiswa ? $mahasiswa->id : null,
            ],
            [
                'name' => 'Yayasan Konservasi Alam Nusantara (YKAN)',
                'address' => 'Graha Iskandarsyah Lt. 3, Jl. Iskandarsyah Raya No. 66 C',
                'type' => 'NGO',
                'field' => 'GIS, Spatial Database & Lingkungan Hidup',
                'city' => 'Jakarta Selatan',
                'quota' => 3,
                'contact_person' => 'Maya Kartika',
                'contact_phone' => '081577889900',
                'is_active' => true,
                'is_approved' => true,
                'requested_by' => null,
            ],
        ];

        foreach ($companies as $comp) {
            Company::firstOrCreate(
                ['name' => $comp['name']],
                $comp
            );
        }
    }
}
