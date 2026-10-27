<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Group;
use App\Models\InternshipApplication;
use App\Models\User;
use Database\Seeders\CompanySeeder;
use Database\Seeders\MagangInteractionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyRadarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);
        $this->seed(CompanySeeder::class);
        $this->seed(MagangInteractionSeeder::class);
    }

    public function test_radar_instansi_index_page_loads_successfully(): void
    {
        $response = $this->get(route('companies.index'));

        $response->assertStatus(200);
        $response->assertSee('Radar Kuota Instansi Magang');
        $response->assertSee('PT Telkom Indonesia');
        $response->assertSee('Dinas Komunikasi');
    }

    public function test_traffic_light_calculation_and_badges(): void
    {
        // Telkom (quota 10, accepted 0) -> <50% -> Green / Aman
        $telkom = Company::where('name', 'LIKE', '%Telkom%')->first();
        $this->assertEquals(0, $telkom->accepted_members_count);
        $this->assertEquals(0.0, $telkom->quota_percentage);
        $this->assertEquals('green', $telkom->quota_status['level']);
        $this->assertEquals('Aman', $telkom->quota_status['label']);

        // Diskominfo (quota 6, accepted 3) -> 50% -> Yellow / Terbatas
        $diskominfo = Company::where('name', 'LIKE', '%Dinas Komunikasi%')->first();
        $this->assertEquals(3, $diskominfo->accepted_members_count);
        $this->assertEquals(50.0, $diskominfo->quota_percentage);
        $this->assertEquals('yellow', $diskominfo->quota_status['level']);
        $this->assertEquals('Terbatas', $diskominfo->quota_status['label']);

        // Bappeda (quota 4, accepted 4) -> 100% -> Red / Kritis/Penuh
        $bappeda = Company::where('name', 'LIKE', '%Bappeda%')->first();
        $this->assertEquals(4, $bappeda->accepted_members_count);
        $this->assertEquals(100.0, $bappeda->quota_percentage);
        $this->assertEquals('red', $bappeda->quota_status['level']);
        $this->assertStringContainsString('Kritis', $bappeda->quota_status['label']);
    }

    public function test_filter_and_search_features(): void
    {
        // Filter by search name
        $response = $this->get(route('companies.index', ['search' => 'Telkom']));
        $response->assertStatus(200);
        $response->assertSee('PT Telkom Indonesia');
        $response->assertDontSee('PT Gamatechno Indonesia');

        // Filter by type
        $responseType = $this->get(route('companies.index', ['type' => 'pemerintah']));
        $responseType->assertStatus(200);
        $responseType->assertSee('Dinas Komunikasi');

        // Filter by status traffic light
        $responseGreen = $this->get(route('companies.index', ['status' => 'green']));
        $responseGreen->assertStatus(200);
        $responseGreen->assertSee('PT Telkom Indonesia');

        $responseRed = $this->get(route('companies.index', ['status' => 'red']));
        $responseRed->assertStatus(200);
        $responseRed->assertSee('Bappeda');
    }

    public function test_company_detail_page_shows_applicants_list(): void
    {
        $diskominfo = Company::where('name', 'LIKE', '%Dinas Komunikasi%')->first();
        $response = $this->get(route('companies.show', $diskominfo));

        $response->assertStatus(200);
        $response->assertSee($diskominfo->name);
        $response->assertSee('DevForce Alpha');
        $response->assertSee('DITERIMA');
    }

    public function test_group_leader_can_apply_internship(): void
    {
        $leader = User::where('email', 'budi@mahasiswa.ac.id')->first();
        $group = Group::where('leader_id', $leader->id)->first();
        $company = Company::where('name', 'LIKE', '%GoTo%')->first();

        $response = $this->actingAs($leader)
            ->post(route('companies.apply', $company), [
                'group_id' => $group->id,
                'notes' => 'Permohonan magang bidang Software Engineering untuk 1 anggota.',
            ]);

        $response->assertRedirect(route('companies.show', $company));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('internship_applications', [
            'group_id' => $group->id,
            'company_id' => $company->id,
            'status' => 'menunggu',
        ]);

        $this->assertDatabaseHas('application_status_logs', [
            'status_after' => 'menunggu',
            'changed_by' => $leader->id,
        ]);
    }

    public function test_non_leader_cannot_apply_for_a_group(): void
    {
        $nonLeader = User::where('email', 'siti@mahasiswa.ac.id')->first();
        $group = Group::where('name', 'DevForce Alpha')->first();
        $company = Company::where('name', 'LIKE', '%GoTo%')->first();

        $response = $this->actingAs($nonLeader)
            ->post(route('companies.apply', $company), [
                'group_id' => $group->id,
                'notes' => 'Coba mendaftar tanpa izin ketua',
            ]);

        // Terproteksi dengan 403 Forbidden atau redirect error
        $this->assertTrue(in_array($response->status(), [403, 302]));
        if ($response->status() === 302) {
            $response->assertSessionHas('error');
        }
    }

    public function test_prevent_duplicate_application(): void
    {
        $leader = User::where('email', 'habib@mahasiswa.ac.id')->first();
        $group = Group::where('name', 'DevForce Alpha')->first();
        $company = Company::where('name', 'LIKE', '%Telkom%')->first();

        // Group 1 already has pending application to Telkom from seeder
        $response = $this->actingAs($leader)
            ->from(route('companies.show', $company))
            ->post(route('companies.apply', $company), [
                'group_id' => $group->id,
                'notes' => 'Pengajuan duplikat',
            ]);

        $response->assertRedirect(route('companies.show', $company));
        $this->assertTrue(session()->has('error') || session()->has('errors'));
    }
}
