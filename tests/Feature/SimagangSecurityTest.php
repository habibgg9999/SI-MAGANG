<?php

namespace Tests\Feature;

use App\Helpers\WhatsappHelper;
use App\Models\Company;
use App\Models\CompanyRequest;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\InternshipApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SimagangSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $coordinator;
    protected User $leaderA;
    protected User $memberA;
    protected User $leaderB;
    protected User $soloStudent;
    protected Group $groupA;
    protected Group $groupB;
    protected Company $companyActive;
    protected Company $companyUnapproved;
    protected Company $companyInactive;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Koordinator
        $this->coordinator = User::factory()->create([
            'role' => 'koordinator',
            'name' => 'Prof. Koordinator Magang',
            'email' => 'koordinator.audit@univ.ac.id',
        ]);

        // 2. Mahasiswa & Kelompok A
        $this->leaderA = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Leader Alpha',
            'email' => 'leader.a@mhs.ac.id',
            'phone' => '081234567801',
        ]);

        $this->memberA = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Member Alpha One',
            'email' => 'member.a@mhs.ac.id',
            'phone' => '081234567802',
        ]);

        $this->groupA = Group::create([
            'name' => 'Squad Alpha',
            'leader_id' => $this->leaderA->id,
            'max_members' => 4,
            'is_open' => true,
            'is_active' => true,
        ]);

        GroupMember::create([
            'group_id' => $this->groupA->id,
            'user_id' => $this->leaderA->id,
            'status' => 'aktif',
            'joined_at' => now(),
        ]);

        GroupMember::create([
            'group_id' => $this->groupA->id,
            'user_id' => $this->memberA->id,
            'status' => 'aktif',
            'joined_at' => now(),
        ]);

        // 3. Mahasiswa & Kelompok B
        $this->leaderB = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Leader Beta',
            'email' => 'leader.b@mhs.ac.id',
            'phone' => '081234567803',
        ]);

        $this->groupB = Group::create([
            'name' => 'Squad Beta',
            'leader_id' => $this->leaderB->id,
            'max_members' => 3,
            'is_open' => true,
            'is_active' => true,
        ]);

        GroupMember::create([
            'group_id' => $this->groupB->id,
            'user_id' => $this->leaderB->id,
            'status' => 'aktif',
            'joined_at' => now(),
        ]);

        // 4. Mahasiswa Solo
        $this->soloStudent = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Solo Hunter',
            'email' => 'solo@mhs.ac.id',
            'phone' => '081234567899',
        ]);

        // 5. Instansi
        $this->companyActive = Company::create([
            'name' => 'PT Astra International Tbk',
            'type' => 'swasta',
            'quota' => 3, // Kuota pas untuk 3 mhs
            'city' => 'Jakarta Utara',
            'field' => 'Automotive & IT',
            'is_active' => true,
            'is_approved' => true,
        ]);

        $this->companyUnapproved = Company::create([
            'name' => 'Instansi Ilegal Draft',
            'type' => 'swasta',
            'quota' => 10,
            'city' => 'Yogyakarta',
            'field' => 'Dark Web Research',
            'is_active' => true,
            'is_approved' => false, // BELUM APPROVED
        ]);

        $this->companyInactive = Company::create([
            'name' => 'PT Mitra Nonaktif',
            'type' => 'swasta',
            'quota' => 5,
            'city' => 'Bandung',
            'field' => 'General',
            'is_active' => false, // NON-AKTIF
            'is_approved' => true,
        ]);
    }

    /**
     * Skenario 1 (Wajib): test_mahasiswa_cannot_join_more_than_one_active_group
     * Memastikan mahasiswa gagal bergabung ke kelompok kedua saat masih aktif di kelompok pertama.
     */
    public function test_mahasiswa_cannot_join_more_than_one_active_group(): void
    {
        // $this->memberA sudah aktif di $this->groupA
        $this->assertTrue($this->memberA->hasActiveGroup());

        // Coba bergabung mandiri ke groupB
        $response = $this->actingAs($this->memberA)
            ->post(route('groups.join', $this->groupB));

        $response->assertRedirect(route('groups.show', $this->groupB));
        $response->assertSessionHas('error');

        // Pastikan tidak ada record aktif baru untuk memberA di groupB
        $this->assertDatabaseMissing('group_members', [
            'group_id' => $this->groupB->id,
            'user_id' => $this->memberA->id,
            'status' => 'aktif',
        ]);

        // Coba ketua groupB menambahkan memberA secara paksa via addMember
        $response2 = $this->actingAs($this->leaderB)
            ->post(route('groups.members.add', $this->groupB), [
                'user_id' => $this->memberA->id,
            ]);

        $response2->assertRedirect(route('groups.show', $this->groupB));
        $response2->assertSessionHas('error');
    }

    /**
     * Skenario 2 (Wajib): test_group_cannot_apply_while_having_pending_application
     * Memastikan kelompok yang punya status 'menunggu' ditolak backend saat mencoba mendaftar ke instansi lain.
     */
    public function test_group_cannot_apply_while_having_pending_application(): void
    {
        // Buat pengajuan berstatus 'menunggu' di companyActive
        InternshipApplication::create([
            'group_id' => $this->groupA->id,
            'company_id' => $this->companyActive->id,
            'status' => 'menunggu',
            'applied_at' => now()->subDays(2),
        ]);

        $companySecond = Company::create([
            'name' => 'PT Bank Central Asia Tbk',
            'type' => 'swasta',
            'quota' => 10,
            'is_active' => true,
            'is_approved' => true,
        ]);

        // Coba daftar lagi ke instansi kedua via StoreApplication
        $response = $this->actingAs($this->leaderA)
            ->post(route('applications.store'), [
                'group_id' => $this->groupA->id,
                'company_id' => $companySecond->id,
                'notes' => 'Mencoba pendaftaran ganda secara paralel.',
            ]);

        // Harus ditolak dengan Validation Error (HTTP 302 with session errors atau HTTP 422)
        $response->assertSessionHasErrors('group_id');

        // Pastikan tidak ada aplikasi kedua di database
        $this->assertDatabaseMissing('internship_applications', [
            'group_id' => $this->groupA->id,
            'company_id' => $companySecond->id,
        ]);
    }

    /**
     * Skenario 3 (Wajib): test_non_leader_cannot_submit_or_update_application
     * Memastikan anggota biasa mendapatkan error 403 (Forbidden) jika mencoba mengajukan magang atau mengubah status.
     */
    public function test_non_leader_cannot_submit_or_update_application(): void
    {
        // 1. Anggota biasa mencoba submit pendaftaran magang
        $responseSubmit = $this->actingAs($this->memberA)
            ->post(route('applications.store'), [
                'group_id' => $this->groupA->id,
                'company_id' => $this->companyActive->id,
                'notes' => 'Anggota biasa mencoba mendaftar.',
            ]);

        // Policy / Request Authorization melempar 403 Forbidden
        $responseSubmit->assertStatus(403);

        // 2. Buat aplikasi sah oleh ketua
        $app = InternshipApplication::create([
            'group_id' => $this->groupA->id,
            'company_id' => $this->companyActive->id,
            'status' => 'menunggu',
            'applied_at' => now(),
        ]);

        // Anggota biasa mencoba mengubah status pengajuan menjadi 'diterima'
        $responseUpdate = $this->actingAs($this->memberA)
            ->patch(route('applications.update-status', $app), [
                'status' => 'diterima',
                'reason' => 'Spoofing status oleh anggota non-ketua.',
            ]);

        // Harus diblokir dengan 403 Forbidden
        $responseUpdate->assertStatus(403);

        // Pastikan status di database tetap 'menunggu'
        $app->refresh();
        $this->assertEquals('menunggu', $app->status);
    }

    /**
     * Skenario 4 (Wajib): test_non_admin_cannot_access_admin_dashboard_or_approve_companies
     * Memastikan role 'mahasiswa' diblokir middleware saat mengakses route /admin/*.
     */
    public function test_non_admin_cannot_access_admin_dashboard_or_approve_companies(): void
    {
        // 1. Akses dashboard admin
        $responseDashboard = $this->actingAs($this->leaderA)->get('/admin');
        $responseDashboard->assertStatus(403);

        // 2. Akses halaman kelola instansi
        $responseCompanies = $this->actingAs($this->leaderA)->get('/admin/companies');
        $responseCompanies->assertStatus(403);

        // 3. Akses review usulan
        $responseRequests = $this->actingAs($this->leaderA)->get('/admin/requests');
        $responseRequests->assertStatus(403);

        // 4. Akses ekspor CSV
        $responseCsv = $this->actingAs($this->leaderA)->get('/admin/export-csv');
        $responseCsv->assertStatus(403);

        // 5. Coba approve usulan via POST
        $companyReq = CompanyRequest::create([
            'name' => 'PT Usulan Gelap',
            'type' => 'swasta',
            'status' => 'pending',
            'requested_by' => $this->soloStudent->id,
        ]);

        $responseApprove = $this->actingAs($this->leaderA)
            ->post(route('admin.requests.approve', $companyReq));
        $responseApprove->assertStatus(403);
    }

    /**
     * Skenario 5 (Wajib): test_quota_calculation_does_not_overflow_under_concurrent_accepts
     * Simulasi proteksi konkurensi (Pessimistic Lock) saat status kelompok diubah menjadi 'diterima'.
     */
    public function test_quota_calculation_does_not_overflow_under_concurrent_accepts(): void
    {
        // companyActive memiliki kuota 3 slot.
        // Group A memiliki 2 anggota (leaderA + memberA)
        // Group B memiliki 1 anggota (leaderB)

        $appA = InternshipApplication::create([
            'group_id' => $this->groupA->id,
            'company_id' => $this->companyActive->id,
            'status' => 'menunggu',
            'applied_at' => now()->subDays(3),
        ]);

        $appB = InternshipApplication::create([
            'group_id' => $this->groupB->id,
            'company_id' => $this->companyActive->id,
            'status' => 'menunggu',
            'applied_at' => now()->subDays(2),
        ]);

        // 1. Group A diterima (memakai 2 dari 3 slot kuota) -> SUKSES
        $responseA = $this->actingAs($this->leaderA)
            ->patch(route('applications.update-status', $appA), [
                'status' => 'diterima',
                'reason' => 'Surat penerimaan resmi dari PT Astra telah terbit.',
            ]);

        $responseA->assertRedirect();
        $responseA->assertSessionHas('success');

        $appA->refresh();
        $this->assertEquals('diterima', $appA->status);

        // Sekarang sisa kuota tinggal: 3 - 2 = 1 slot.
        // Tambahkan 1 anggota lagi ke Group B sehingga total anggota Group B menjadi 2 orang
        GroupMember::create([
            'group_id' => $this->groupB->id,
            'user_id' => $this->soloStudent->id,
            'status' => 'aktif',
            'joined_at' => now(),
        ]);
        $this->assertEquals(2, $this->groupB->activeMembers()->count());

        // 2. Sekarang coba terima Group B (butuh 2 slot, padahal tersisa hanya 1 slot kuota)
        // Logika database transaction & lockForUpdate harus memvalidasi dan MENOLAK transaksi!
        $responseB = $this->actingAs($this->leaderB)
            ->patch(route('applications.update-status', $appB), [
                'status' => 'diterima',
                'reason' => 'Mencoba melebihi sisa kuota resmi.',
            ]);

        $responseB->assertSessionHasErrors('status');

        // Pastikan Group B TIDAK berubah menjadi diterima (status tetap 'menunggu')
        $appB->refresh();
        $this->assertEquals('menunggu', $appB->status);
    }

    /**
     * Skenario 6 (Wajib): test_cannot_apply_to_unapproved_or_inactive_company
     * Memastikan instansi draft/non-aktif melempar error 404/422.
     */
    public function test_cannot_apply_to_unapproved_or_inactive_company(): void
    {
        // 1. Mencoba mendaftar ke instansi yang belum disetujui (is_approved = false)
        $responseUnapproved = $this->actingAs($this->leaderA)
            ->post(route('applications.store'), [
                'group_id' => $this->groupA->id,
                'company_id' => $this->companyUnapproved->id,
                'notes' => 'Mencoba mendaftar ke instansi draft/unapproved.',
            ]);

        // Ditolak dengan Validation Error (HTTP 422)
        $responseUnapproved->assertSessionHasErrors('company_id');

        // 2. Mencoba mendaftar ke instansi non-aktif (is_active = false)
        $responseInactive = $this->actingAs($this->leaderA)
            ->post(route('applications.store'), [
                'group_id' => $this->groupA->id,
                'company_id' => $this->companyInactive->id,
                'notes' => 'Mencoba mendaftar ke instansi non-aktif.',
            ]);

        $responseInactive->assertSessionHasErrors('company_id');

        // 3. Mengakses halaman detail instansi non-aktif via URL manual melempar 404 Not Found
        $responseShowInactive = $this->actingAs($this->leaderA)
            ->get(route('companies.show', $this->companyInactive));
        $responseShowInactive->assertStatus(404);

        // 4. Mengakses halaman detail instansi belum approved via URL manual melempar 404 Not Found
        $responseShowUnapproved = $this->actingAs($this->leaderA)
            ->get(route('companies.show', $this->companyUnapproved));
        $responseShowUnapproved->assertStatus(404);
    }

    /**
     * Skenario 7 (Celah 5 - Ghost Quota):
     * Anggota tidak dapat keluar atau dikeluarkan jika kelompok sudah berstatus 'diterima'.
     */
    public function test_ghost_quota_member_cannot_leave_or_be_removed_if_group_accepted(): void
    {
        // Tandai groupA sudah diterima
        InternshipApplication::create([
            'group_id' => $this->groupA->id,
            'company_id' => $this->companyActive->id,
            'status' => 'diterima',
            'applied_at' => now()->subDays(5),
            'responded_at' => now()->subDays(1),
        ]);

        $this->assertTrue($this->groupA->hasAcceptedApplication());

        // Anggota mencoba keluar mandiri
        $response = $this->actingAs($this->memberA)
            ->delete(route('groups.members.remove', ['group' => $this->groupA, 'user' => $this->memberA]));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Pastikan status member tetap 'aktif'
        $this->assertDatabaseHas('group_members', [
            'group_id' => $this->groupA->id,
            'user_id' => $this->memberA->id,
            'status' => 'aktif',
        ]);
    }

    /**
     * Skenario 8 (Celah 8 - State Machine):
     * Pengajuan berstatus 'ditolak' tidak dapat melompat langsung menjadi 'diterima'.
     */
    public function test_state_machine_cannot_jump_from_rejected_to_accepted(): void
    {
        $app = InternshipApplication::create([
            'group_id' => $this->groupA->id,
            'company_id' => $this->companyActive->id,
            'status' => 'ditolak',
            'applied_at' => now()->subDays(10),
            'responded_at' => now()->subDays(2),
        ]);

        // Coba ubah dari 'ditolak' menjadi 'diterima'
        $response = $this->actingAs($this->leaderA)
            ->patch(route('applications.update-status', $app), [
                'status' => 'diterima',
                'reason' => 'Mencoba bypass state machine jump.',
            ]);

        // Ditolak dengan error validasi status
        $response->assertSessionHasErrors('status');

        $app->refresh();
        $this->assertEquals('ditolak', $app->status);
    }

    /**
     * Skenario 9 (Celah 7 - Sanitasi WhatsApp & XSS):
     * Link WhatsApp membersihkan karakter asing dan menolak nomor tidak valid.
     */
    public function test_whatsapp_link_sanitizes_xss_and_phone_number(): void
    {
        // Nomor dengan spasi, strip, dan awalan 08
        $cleanPhone = WhatsappHelper::formatPhoneNumber('0812-3456-7890');
        $this->assertEquals('6281234567890', $cleanPhone);

        // Nomor tidak valid (huruf/simbol)
        $invalidPhone = WhatsappHelper::formatPhoneNumber('abcd-xxxx');
        $this->assertNull($invalidPhone);

        // Uji sanitasi pesan dari serangan XSS <script>
        $link = WhatsappHelper::generateLink('081234567890', '<script>alert(1)</script>Habib', '<b>Judul</b>');
        $this->assertNotNull($link);
        $this->assertStringNotContainsString('<script>', urldecode($link));
        $this->assertStringContainsString('Habib', urldecode($link));
    }

    /**
     * Skenario 10 (Celah 10 - Scheduler Idempotency):
     * Reminder tidak dikirim ganda jika sudah dikirim kurang dari 7 hari yang lalu.
     */
    public function test_scheduler_idempotency_prevents_duplicate_reminders_within_seven_days(): void
    {
        Mail::fake();

        // Pengajuan menggantung 20 hari lalu, tetapi sudah pernah diingatkan 2 hari yang lalu
        InternshipApplication::create([
            'group_id' => $this->groupA->id,
            'company_id' => $this->companyActive->id,
            'status' => 'menunggu',
            'applied_at' => Carbon::now()->subDays(20),
            'deadline_reminder' => Carbon::now()->subDays(2), // < 7 hari lalu
        ]);

        $this->artisan('simagang:send-reminders')
            ->expectsOutputToContain('Tidak ada permohonan magang berstatus "menunggu" yang mencapai batas 14 hari tanpa respon.')
            ->assertSuccessful();

        Mail::assertNothingSent();
    }
}
