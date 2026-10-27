<?php

namespace Database\Seeders;

use App\Models\ApplicationStatusLog;
use App\Models\Company;
use App\Models\CompanyRequest;
use App\Models\ForumPost;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\InternshipApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MagangInteractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $koordinator = User::where('role', 'koordinator')->first();
        $mhs1 = User::where('email', 'habib@mahasiswa.ac.id')->first();
        $mhs2 = User::where('email', 'fauzi@mahasiswa.ac.id')->first();
        $mhs3 = User::where('email', 'siti@mahasiswa.ac.id')->first();
        $mhs4 = User::where('email', 'budi@mahasiswa.ac.id')->first();
        $mhs5 = User::where('email', 'putri@mahasiswa.ac.id')->first();

        // Additional dummy students for group variation (Group 3: DataGov)
        $extraStudents = [
            ['nim' => '21081010006', 'name' => 'Reza Fahlevi', 'email' => 'reza@mahasiswa.ac.id'],
            ['nim' => '21081010007', 'name' => 'Dinda Puspita', 'email' => 'dinda@mahasiswa.ac.id'],
            ['nim' => '21081010008', 'name' => 'Fajar Pratama', 'email' => 'fajar@mahasiswa.ac.id'],
            ['nim' => '21081010009', 'name' => 'Anisa Triani', 'email' => 'anisa@mahasiswa.ac.id'],
        ];

        $extraUserModels = [];
        foreach ($extraStudents as $item) {
            $extraUserModels[] = User::firstOrCreate(
                ['email' => $item['email']],
                [
                    'nim' => $item['nim'],
                    'name' => $item['name'],
                    'phone' => '082198765439',
                    'prodi' => 'Teknik Informatika',
                    'angkatan' => '2021',
                    'role' => 'mahasiswa',
                    'password' => Hash::make('password123'),
                ]
            );
        }

        // Dedicated students for Group 4 (FinTech) to prevent split-brain active membership
        $fintechStudents = [
            ['nim' => '21081010010', 'name' => 'Dimas Pratama', 'email' => 'dimas@mahasiswa.ac.id'],
            ['nim' => '21081010011', 'name' => 'Ratna Sari', 'email' => 'ratna@mahasiswa.ac.id'],
            ['nim' => '21081010012', 'name' => 'Gilang Ramadhan', 'email' => 'gilang@mahasiswa.ac.id'],
        ];

        $fintechUserModels = [];
        foreach ($fintechStudents as $item) {
            $fintechUserModels[] = User::firstOrCreate(
                ['email' => $item['email']],
                [
                    'nim' => $item['nim'],
                    'name' => $item['name'],
                    'phone' => '082198765410',
                    'prodi' => 'Sistem Informasi',
                    'angkatan' => '2021',
                    'role' => 'mahasiswa',
                    'password' => Hash::make('password123'),
                ]
            );
        }

        $telkom = Company::where('name', 'LIKE', '%Telkom%')->first();
        $diskominfo = Company::where('name', 'LIKE', '%Dinas Komunikasi%')->first();
        $bappeda = Company::where('name', 'LIKE', '%Bappeda%')->first();
        $bri = Company::where('name', 'LIKE', '%Bank Rakyat Indonesia%')->first();
        $goto = Company::where('name', 'LIKE', '%GoTo%')->first();

        if (!$mhs1 || !$mhs2 || !$telkom) {
            return;
        }

        // 1. Buat Kelompok Magang
        // Group 1: DevForce Alpha (Leader: Habib)
        $group1 = Group::firstOrCreate(
            ['name' => 'DevForce Alpha'],
            [
                'leader_id' => $mhs1->id,
                'max_members' => 4,
                'is_open' => true,
                'description' => 'Kelompok fokus pada pengembangan Web Application dan Cloud di PT Telkom atau Diskominfo.',
                'is_active' => true,
            ]
        );

        // Group 2: CyberShield Squad (Leader: Budi)
        $group2 = Group::firstOrCreate(
            ['name' => 'CyberShield Squad'],
            [
                'leader_id' => $mhs4->id,
                'max_members' => 3,
                'is_open' => true,
                'description' => 'Fokus pendaftaran magang divisi Information Security dan Digital Banking.',
                'is_active' => true,
            ]
        );

        // Group 3: DataGov Team (Leader: Reza) -> Target: Bappeda (Penuh 100% -> Merah)
        $group3 = Group::firstOrCreate(
            ['name' => 'DataGov Analytics'],
            [
                'leader_id' => $extraUserModels[0]->id,
                'max_members' => 4,
                'is_open' => false,
                'description' => 'Fokus visualisasi dashboard perencanaan wilayah SPBE Bappeda.',
                'is_active' => true,
            ]
        );

        // Group 4: FinTech Squad (Leader: Dimas) -> Target: BRI (Kuning 50-89%)
        $group4 = Group::firstOrCreate(
            ['name' => 'FinTech Innovators'],
            [
                'leader_id' => $fintechUserModels[0]->id,
                'max_members' => 5,
                'is_open' => false,
                'description' => 'Eksplorasi open API & digital banking microservices.',
                'is_active' => true,
            ]
        );

        // Group 5: IoT Systems (Leader: Aditya) -> Target: Gamatechno (Pengajuan Menggantung > 14 hari)
        $mhsHanging = User::firstOrCreate(
            ['email' => 'aditya@mahasiswa.ac.id'],
            [
                'nim' => '22081010099',
                'name' => 'Aditya Wicaksono',
                'phone' => '082198765499',
                'prodi' => 'Teknik Informatika',
                'angkatan' => '2022',
                'role' => 'mahasiswa',
                'password' => Hash::make('password123'),
                'avatar' => null,
            ]
        );

        $group5 = Group::firstOrCreate(
            ['name' => 'IoT Smart City Hub'],
            [
                'leader_id' => $mhsHanging->id,
                'max_members' => 3,
                'is_open' => true,
                'description' => 'Pengembangan smart city sensor and IoT dashboard integration.',
                'is_active' => true,
            ]
        );

        GroupMember::firstOrCreate(
            ['group_id' => $group5->id, 'user_id' => $mhsHanging->id],
            ['status' => 'aktif', 'joined_at' => Carbon::now()->subDays(20)]
        );

        // 2. Anggota Kelompok (group_members)
        // Group 1 members: Habib (Leader), Fauzi, Siti (Total: 3)
        GroupMember::firstOrCreate(
            ['group_id' => $group1->id, 'user_id' => $mhs1->id],
            ['status' => 'aktif', 'joined_at' => Carbon::now()->subDays(10)]
        );
        GroupMember::firstOrCreate(
            ['group_id' => $group1->id, 'user_id' => $mhs2->id],
            ['status' => 'aktif', 'joined_at' => Carbon::now()->subDays(9)]
        );
        GroupMember::firstOrCreate(
            ['group_id' => $group1->id, 'user_id' => $mhs3->id],
            ['status' => 'aktif', 'joined_at' => Carbon::now()->subDays(7)]
        );

        // Group 2 member: Budi (Leader) (Total: 1)
        GroupMember::firstOrCreate(
            ['group_id' => $group2->id, 'user_id' => $mhs4->id],
            ['status' => 'aktif', 'joined_at' => Carbon::now()->subDays(5)]
        );

        // Group 3 members: Reza, Dinda, Fajar, Anisa (Total: 4)
        foreach ($extraUserModels as $m) {
            GroupMember::firstOrCreate(
                ['group_id' => $group3->id, 'user_id' => $m->id],
                ['status' => 'aktif', 'joined_at' => Carbon::now()->subDays(12)]
            );
        }

        // Group 4 members: Dimas (Leader), Ratna, Gilang, Putri (Total: 4)
        foreach ($fintechUserModels as $fm) {
            GroupMember::firstOrCreate(
                ['group_id' => $group4->id, 'user_id' => $fm->id],
                ['status' => 'aktif', 'joined_at' => Carbon::now()->subDays(8)]
            );
        }
        if ($mhs5) {
            GroupMember::firstOrCreate(
                ['group_id' => $group4->id, 'user_id' => $mhs5->id],
                ['status' => 'aktif', 'joined_at' => Carbon::now()->subDays(6)]
            );
        }

        // 3. Pendaftaran Magang (internship_applications)

        // Case A: PT Telkom (Quota: 10) -> Status: menunggu (3 anggota) => Terpakai: 0% 🟢 Hijau
        $appTelkom = InternshipApplication::firstOrCreate(
            ['group_id' => $group1->id, 'company_id' => $telkom->id],
            [
                'status' => 'menunggu',
                'applied_at' => Carbon::now()->subDays(3),
                'responded_at' => null,
                'notes' => 'Pengajuan posisi Backend Developer & DevOps untuk 3 orang.',
                'deadline_reminder' => Carbon::now()->addDays(11),
            ]
        );
        ApplicationStatusLog::firstOrCreate(
            ['application_id' => $appTelkom->id, 'status_after' => 'menunggu'],
            [
                'status_before' => null,
                'changed_by' => $mhs1->id,
                'reason' => 'Pengajuan proposal pendaftaran magang kelompok ke PT Telkom.',
                'changed_at' => Carbon::now()->subDays(3),
            ]
        );

        // Case B: Diskominfo (Quota: 6) -> Status: diterima (3 anggota) => 3/6 = 50% 🟡 Kuning
        if ($diskominfo) {
            $appDiskominfo = InternshipApplication::firstOrCreate(
                ['group_id' => $group1->id, 'company_id' => $diskominfo->id],
                [
                    'status' => 'diterima',
                    'applied_at' => Carbon::now()->subDays(14),
                    'responded_at' => Carbon::now()->subDays(2),
                    'notes' => 'Disetujui untuk penempatan Divisi Aplikasi SPBE.',
                    'deadline_reminder' => null,
                ]
            );
            ApplicationStatusLog::firstOrCreate(
                ['application_id' => $appDiskominfo->id, 'status_after' => 'diterima'],
                [
                    'status_before' => 'menunggu',
                    'changed_by' => $koordinator ? $koordinator->id : $mhs1->id,
                    'reason' => 'Surat balasan resmi diterima dan disetujui koordinator.',
                    'changed_at' => Carbon::now()->subDays(2),
                ]
            );
        }

        // Case C: Bappeda (Quota: 4) -> Group 3 diterima (4 anggota) => 4/4 = 100% 🔴 Merah
        if ($bappeda) {
            $appBappeda = InternshipApplication::firstOrCreate(
                ['group_id' => $group3->id, 'company_id' => $bappeda->id],
                [
                    'status' => 'diterima',
                    'applied_at' => Carbon::now()->subDays(15),
                    'responded_at' => Carbon::now()->subDays(5),
                    'notes' => 'Diterima penempatan lengkap untuk 4 mahasiswa.',
                    'deadline_reminder' => null,
                ]
            );
            ApplicationStatusLog::firstOrCreate(
                ['application_id' => $appBappeda->id, 'status_after' => 'diterima'],
                [
                    'status_before' => 'menunggu',
                    'changed_by' => $koordinator ? $koordinator->id : $extraUserModels[0]->id,
                    'reason' => 'Konfirmasi kuota penuh di Bappeda oleh Koordinator.',
                    'changed_at' => Carbon::now()->subDays(5),
                ]
            );
        }

        // Case D: BRI (Quota: 8) -> Group 4 diterima (4 anggota) => 4/8 = 50% 🟡 Kuning
        if ($bri) {
            $appBri = InternshipApplication::firstOrCreate(
                ['group_id' => $group4->id, 'company_id' => $bri->id],
                [
                    'status' => 'diterima',
                    'applied_at' => Carbon::now()->subDays(10),
                    'responded_at' => Carbon::now()->subDays(3),
                    'notes' => 'Diterima di Divisi IT & Digital Banking.',
                    'deadline_reminder' => null,
                ]
            );
            ApplicationStatusLog::firstOrCreate(
                ['application_id' => $appBri->id, 'status_after' => 'diterima'],
                [
                    'status_before' => 'menunggu',
                    'changed_by' => $koordinator ? $koordinator->id : $extraUserModels[1]->id,
                    'reason' => 'Pengumuman seleksi berkas BRI gelombang 1.',
                    'changed_at' => Carbon::now()->subDays(3),
                ]
            );
        }

        // Case E: Gamatechno (Quota: 5) -> Group 5 (IoT Smart City Hub) Status: MENUNGGU > 14 hari (19 hari lalu)
        $gamatechno = Company::where('name', 'LIKE', '%Gamatechno%')->first();
        if ($gamatechno) {
            $appGamatechno = InternshipApplication::firstOrCreate(
                ['group_id' => $group5->id, 'company_id' => $gamatechno->id],
                [
                    'status' => 'menunggu',
                    'applied_at' => Carbon::now()->subDays(19),
                    'responded_at' => null,
                    'notes' => 'Pengajuan posisi Network Security dan Web System.',
                    'deadline_reminder' => Carbon::now()->subDays(5),
                ]
            );
            ApplicationStatusLog::firstOrCreate(
                ['application_id' => $appGamatechno->id, 'status_after' => 'menunggu'],
                [
                    'status_before' => null,
                    'changed_by' => $mhsHanging->id,
                    'reason' => 'Pengajuan proposal awal kelompok ke PT Gamatechno Indonesia.',
                    'changed_at' => Carbon::now()->subDays(19),
                ]
            );
        }

        // 4. Forum Posts
        ForumPost::firstOrCreate(
            ['title' => 'Dicari 1 Anggota untuk Posisi Frontend Engineer'],
            [
                'user_id' => $mhs1->id,
                'group_id' => $group1->id,
                'type' => 'cari_anggota',
                'body' => 'Kelompok DevForce Alpha masih membutuhkan 1 anggota dengan minat Frontend (Vue / React / Tailwind) untuk kolaborasi proyek magang.',
                'target_company' => 'PT Telkom Indonesia / Diskominfo',
                'field' => 'Frontend Developer',
                'slots_needed' => 1,
                'is_fulfilled' => false,
            ]
        );

        if ($mhs5) {
            ForumPost::firstOrCreate(
                ['title' => 'Mencari Kelompok Magang Fokus UI/UX & Sistem Analis'],
                [
                    'user_id' => $mhs5->id,
                    'group_id' => null,
                    'type' => 'cari_kelompok',
                    'body' => 'Halo teman-teman, saya Putri angkatan 2022 mencari kelompok magang yang membutuhkan anggota di bidang UI/UX Design atau System Analyst.',
                    'target_company' => 'BUMN / Startup Swasta',
                    'field' => 'UI/UX & Business Analyst',
                    'slots_needed' => 1,
                    'is_fulfilled' => false,
                ]
            );
        }

        // 5. Usulan Instansi Baru (company_requests)
        CompanyRequest::firstOrCreate(
            ['name' => 'PT Teknologi Masa Depan Bangsa'],
            [
                'requested_by' => $mhs2->id,
                'address' => 'Gedung Cyber 2 Lt. 10, Jl. HR Rasuna Said, Jakarta Selatan',
                'type' => 'swasta',
                'field' => 'Artificial Intelligence & Software House',
                'city' => 'Jakarta Selatan',
                'quota_estimate' => 3,
                'contact_person' => 'Rian Hidayat (Head of Talent)',
                'status' => 'pending',
                'reject_reason' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]
        );

        CompanyRequest::firstOrCreate(
            ['name' => 'Balai Standardisasi Riset Daerah'],
            [
                'requested_by' => $mhs3->id,
                'address' => 'Jl. Bhayangkara No. 55',
                'type' => 'pemerintah',
                'field' => 'Data Science & Pengujian Mutu TI',
                'city' => 'Yogyakarta',
                'quota_estimate' => 2,
                'contact_person' => 'Dra. Endang Purwanti',
                'status' => 'approved',
                'reject_reason' => null,
                'reviewed_by' => $koordinator ? $koordinator->id : null,
                'reviewed_at' => Carbon::now()->subDays(1),
            ]
        );
    }
}
