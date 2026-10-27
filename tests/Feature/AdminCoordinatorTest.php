<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\CompanyRequest;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\InternshipApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCoordinatorTest extends TestCase
{
    use RefreshDatabase;

    protected User $coordinator;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->coordinator = User::factory()->create([
            'role' => 'koordinator',
            'name' => 'Dr. Hendra Wijaya',
            'email' => 'hendra@univ.ac.id',
        ]);

        $this->student = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Habib Ahmad',
            'email' => 'habib@mhs.ac.id',
            'nim' => '220101001',
        ]);
    }

    /**
     * Test 1: Middleware - Hanya role 'koordinator' yang bisa akses prefix /admin.
     */
    public function test_guest_is_redirected_when_accessing_admin(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect(route('companies.index'));
        $response->assertSessionHas('error');
    }

    public function test_student_gets_403_when_accessing_admin(): void
    {
        $response = $this->actingAs($this->student)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_coordinator_can_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->coordinator)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Koordinator Magang');
    }

    /**
     * Test 2: Statistik Utama & Pengajuan Menggantung (> 14 hari).
     */
    public function test_dashboard_displays_correct_statistics_and_hanging_applications(): void
    {
        // Setup additional students
        $student2 = User::factory()->create(['role' => 'mahasiswa', 'nim' => '220101002']);
        $student3 = User::factory()->create(['role' => 'mahasiswa', 'nim' => '220101003']);

        // Company
        $company = Company::factory()->create([
            'name' => 'PT Tokopedia',
            'quota' => 5,
            'is_active' => true,
            'is_approved' => true,
        ]);

        // Group 1: diterima di PT Tokopedia (2 anggota)
        $group1 = Group::factory()->create(['leader_id' => $this->student->id]);
        GroupMember::create(['group_id' => $group1->id, 'user_id' => $this->student->id, 'status' => 'aktif', 'joined_at' => now()]);
        GroupMember::create(['group_id' => $group1->id, 'user_id' => $student2->id, 'status' => 'aktif', 'joined_at' => now()]);

        InternshipApplication::create([
            'group_id' => $group1->id,
            'company_id' => $company->id,
            'status' => 'diterima',
            'applied_at' => now()->subDays(20),
            'responded_at' => now()->subDays(5),
        ]);

        // Group 2: status 'menunggu' diajukan 18 hari yang lalu (> 14 hari) -> HANGING
        $group2 = Group::factory()->create(['name' => 'Kelompok Menggantung', 'leader_id' => $student3->id]);
        GroupMember::create(['group_id' => $group2->id, 'user_id' => $student3->id, 'status' => 'aktif', 'joined_at' => now()]);

        $hangingApp = InternshipApplication::create([
            'group_id' => $group2->id,
            'company_id' => $company->id,
            'status' => 'menunggu',
            'applied_at' => Carbon::now()->subDays(18),
        ]);

        $response = $this->actingAs($this->coordinator)->get('/admin');
        $response->assertStatus(200);

        // Verify stats in view
        $response->assertSee('PT Tokopedia');
        $response->assertSee('Kelompok Menggantung');
        $response->assertSee('18 Hari Lalu');
        $response->assertSee('Perlu Tindakan Segera');
    }

    /**
     * Test 3: Review Usulan Instansi (Approve).
     */
    public function test_coordinator_can_approve_company_request(): void
    {
        $request = CompanyRequest::create([
            'name' => 'Startup AI Nusantara',
            'type' => 'swasta',
            'field' => 'Artificial Intelligence',
            'city' => 'Bandung',
            'address' => 'Jl. Dago No. 12',
            'quota_estimate' => 3,
            'contact_person' => 'Ibu Rina',
            'reason' => 'Ingin magang di bidang AI yang sedang berkembang.',
            'status' => 'pending',
            'requested_by' => $this->student->id,
        ]);

        $response = $this->actingAs($this->coordinator)
            ->post(route('admin.requests.approve', $request));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert request status updated
        $request->refresh();
        $this->assertEquals('approved', $request->status);
        $this->assertEquals($this->coordinator->id, $request->reviewed_by);
        $this->assertNotNull($request->reviewed_at);

        // Assert company officially created in companies catalog
        $this->assertDatabaseHas('companies', [
            'name' => 'Startup AI Nusantara',
            'type' => 'swasta',
            'quota' => 3,
            'is_active' => true,
            'is_approved' => true,
            'requested_by' => $this->student->id,
        ]);
    }

    /**
     * Test 4: Review Usulan Instansi (Reject dengan Alasan).
     */
    public function test_coordinator_can_reject_company_request_with_reason(): void
    {
        $request = CompanyRequest::create([
            'name' => 'Toko Kelontong Mandiri',
            'type' => 'swasta',
            'field' => 'Retail',
            'city' => 'Yogyakarta',
            'quota_estimate' => 1,
            'status' => 'pending',
            'requested_by' => $this->student->id,
        ]);

        $response = $this->actingAs($this->coordinator)
            ->post(route('admin.requests.reject', $request), [
                'reject_reason' => 'Bidang usaha tidak sesuai dengan kompetensi kurikulum program studi Informatika.',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $request->refresh();
        $this->assertEquals('rejected', $request->status);
        $this->assertEquals('Bidang usaha tidak sesuai dengan kompetensi kurikulum program studi Informatika.', $request->reject_reason);
        $this->assertEquals($this->coordinator->id, $request->reviewed_by);
    }

    public function test_reject_request_fails_if_reason_is_empty(): void
    {
        $request = CompanyRequest::create([
            'name' => 'PT Testing',
            'type' => 'swasta',
            'status' => 'pending',
            'requested_by' => $this->student->id,
        ]);

        $response = $this->actingAs($this->coordinator)
            ->post(route('admin.requests.reject', $request), [
                'reject_reason' => '',
            ]);

        $response->assertSessionHasErrors('reject_reason');
    }

    /**
     * Test 5: CRUD Instansi & Atur Kuota Resmi.
     */
    public function test_coordinator_can_create_company_with_quota(): void
    {
        $response = $this->actingAs($this->coordinator)
            ->post(route('admin.companies.store'), [
                'name' => 'Bank Mandiri Digital Tech',
                'type' => 'BUMN',
                'quota' => 8,
                'city' => 'Jakarta Pusat',
                'field' => 'Financial Technology',
                'address' => 'Plaza Mandiri, Jl. Jend. Gatot Subroto',
                'contact_person' => 'Pak Joko',
                'contact_phone' => '08129998881',
                'is_active' => '1',
                'is_approved' => '1',
            ]);

        $response->assertRedirect(route('admin.companies.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('companies', [
            'name' => 'Bank Mandiri Digital Tech',
            'type' => 'BUMN',
            'quota' => 8,
            'city' => 'Jakarta Pusat',
            'is_active' => true,
        ]);
    }

    public function test_coordinator_can_update_company_and_quota(): void
    {
        $company = Company::factory()->create([
            'name' => 'PT Bukalapak',
            'quota' => 4,
            'type' => 'swasta',
        ]);

        $response = $this->actingAs($this->coordinator)
            ->put(route('admin.companies.update', $company), [
                'name' => 'PT Bukalapak Indonesia Tbk',
                'type' => 'swasta',
                'quota' => 10,
                'city' => 'Jakarta Selatan',
                'field' => 'E-Commerce',
                'is_active' => '1',
                'is_approved' => '1',
            ]);

        $response->assertRedirect(route('admin.companies.index'));
        $company->refresh();

        $this->assertEquals('PT Bukalapak Indonesia Tbk', $company->name);
        $this->assertEquals(10, $company->quota);
    }

    public function test_coordinator_can_toggle_company_active_status(): void
    {
        $company = Company::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->coordinator)
            ->post(route('admin.companies.toggle-active', $company));

        $response->assertRedirect();
        $company->refresh();
        $this->assertFalse($company->is_active);
    }

    /**
     * Test 6: Export Distribusi Mahasiswa ke CSV.
     */
    public function test_coordinator_can_download_distribution_recap_csv(): void
    {
        $response = $this->actingAs($this->coordinator)->get(route('admin.export.csv'));

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('Content-Disposition'), 'attachment; filename="rekap_distribusi_magang_'));
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'text/csv'));

        // Stream and check content
        $streamContent = $response->streamedContent();
        $this->assertStringContainsString('NIM', $streamContent);
        $this->assertStringContainsString('Nama Mahasiswa', $streamContent);
        $this->assertStringContainsString('Nama Instansi Magang', $streamContent);
        $this->assertStringContainsString($this->student->name, $streamContent);
    }
}
