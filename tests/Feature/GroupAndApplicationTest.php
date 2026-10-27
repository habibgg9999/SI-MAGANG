<?php

namespace Tests\Feature;

use App\Models\ApplicationStatusLog;
use App\Models\Company;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\InternshipApplication;
use App\Models\User;
use Database\Seeders\CompanySeeder;
use Database\Seeders\MagangInteractionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GroupAndApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);
        $this->seed(CompanySeeder::class);
        $this->seed(MagangInteractionSeeder::class);
    }

    public function test_student_in_active_group_cannot_create_another_group(): void
    {
        // Habib is the leader of DevForce Alpha (active group)
        $habib = User::where('email', 'habib@mahasiswa.ac.id')->first();
        $this->assertTrue($habib->hasActiveGroup());

        $response = $this->actingAs($habib)->post(route('groups.store'), [
            'name' => 'Kelompok Baru Habib',
            'max_members' => 5,
            'description' => 'Mencoba membuat kelompok kedua',
        ]);

        $response->assertRedirect(route('groups.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('groups', ['name' => 'Kelompok Baru Habib']);
    }

    public function test_student_without_group_can_create_group(): void
    {
        // Create student without group
        $newStudent = User::create([
            'nim' => '22081010999',
            'name' => 'Mahasiswa Bebas',
            'email' => 'bebas@mahasiswa.ac.id',
            'role' => 'mahasiswa',
            'password' => Hash::make('password123'),
        ]);

        $this->assertFalse($newStudent->hasActiveGroup());

        $response = $this->actingAs($newStudent)->post(route('groups.store'), [
            'name' => 'Kelompok Mahasiswa Bebas',
            'max_members' => 4,
            'description' => 'Fokus AI & Web',
        ]);

        $group = Group::where('name', 'Kelompok Mahasiswa Bebas')->first();
        $this->assertNotNull($group);
        $response->assertRedirect(route('groups.show', $group));
        $response->assertSessionHas('success');

        // Leader should be automatically registered as active member in group_members
        $this->assertDatabaseHas('group_members', [
            'group_id' => $group->id,
            'user_id' => $newStudent->id,
            'status' => 'aktif',
        ]);
    }

    public function test_student_in_active_group_cannot_join_another_group(): void
    {
        // Fauzi is already an active member of DevForce Alpha
        $fauzi = User::where('email', 'fauzi@mahasiswa.ac.id')->first();
        $this->assertTrue($fauzi->hasActiveGroup());

        // Group 2: CyberShield Squad
        $group2 = Group::where('name', 'CyberShield Squad')->first();

        // Fauzi tries to join Group 2
        $response = $this->actingAs($fauzi)->post(route('groups.join', $group2));

        $response->assertRedirect(route('groups.show', $group2));
        $response->assertSessionHas('error');

        // Fauzi should NOT be in CyberShield Squad
        $this->assertDatabaseMissing('group_members', [
            'group_id' => $group2->id,
            'user_id' => $fauzi->id,
        ]);
    }

    public function test_leader_cannot_add_member_who_already_has_active_group(): void
    {
        $budi = User::where('email', 'budi@mahasiswa.ac.id')->first();
        $group2 = Group::where('leader_id', $budi->id)->first();

        // Siti is already an active member of DevForce Alpha
        $siti = User::where('email', 'siti@mahasiswa.ac.id')->first();
        $this->assertTrue($siti->hasActiveGroup());

        // Budi attempts to add Siti to Group 2
        $response = $this->actingAs($budi)->post(route('groups.members.add', $group2), [
            'user_id' => $siti->id,
        ]);

        $response->assertRedirect(route('groups.show', $group2));
        $response->assertSessionHas('error');
    }

    public function test_only_leader_can_apply_internship(): void
    {
        // Siti is a member of DevForce Alpha, NOT the leader
        $siti = User::where('email', 'siti@mahasiswa.ac.id')->first();
        $group = Group::where('name', 'DevForce Alpha')->first();
        $company = Company::where('name', 'LIKE', '%Gamatechno%')->first();

        $response = $this->actingAs($siti)->post(route('applications.store'), [
            'group_id' => $group->id,
            'company_id' => $company->id,
            'notes' => 'Siti mencoba mendaftar',
        ]);

        $this->assertTrue(in_array($response->status(), [403, 302]));
        if ($response->status() === 302) {
            $this->assertTrue(session()->has('error') || session()->has('errors'));
        }
        $this->assertDatabaseMissing('internship_applications', [
            'group_id' => $group->id,
            'company_id' => $company->id,
        ]);
    }

    public function test_group_cannot_apply_if_has_pending_application(): void
    {
        // DevForce Alpha already has a pending application to PT Telkom
        $habib = User::where('email', 'habib@mahasiswa.ac.id')->first();
        $group = Group::where('leader_id', $habib->id)->first();
        $this->assertTrue($group->hasPendingApplication());

        $goto = Company::where('name', 'LIKE', '%GoTo%')->first();

        // Habib attempts to apply to another company while having a pending one
        $response = $this->actingAs($habib)->post(route('applications.store'), [
            'group_id' => $group->id,
            'company_id' => $goto->id,
            'notes' => 'Coba mendaftar ganda',
        ]);

        $this->assertTrue(session()->has('error') || session()->has('errors'));
        $this->assertDatabaseMissing('internship_applications', [
            'group_id' => $group->id,
            'company_id' => $goto->id,
        ]);
    }

    public function test_leader_can_update_status_and_it_records_status_log(): void
    {
        $habib = User::where('email', 'habib@mahasiswa.ac.id')->first();
        $group = Group::where('leader_id', $habib->id)->first();
        $application = $group->pendingApplication();
        $this->assertNotNull($application);
        $this->assertEquals('menunggu', $application->status);

        // Update status from 'menunggu' to 'diterima'
        $response = $this->actingAs($habib)->patch(route('applications.update-status', $application), [
            'status' => 'diterima',
            'reason' => 'Surat balasan resmi dari instansi menyatakan kelompok kami diterima.',
        ]);

        $response->assertSessionHas('success');

        // Check updated application
        $application->refresh();
        $this->assertEquals('diterima', $application->status);
        $this->assertNotNull($application->responded_at);

        // Check that application_status_logs recorded the change
        $this->assertDatabaseHas('application_status_logs', [
            'application_id' => $application->id,
            'status_before' => 'menunggu',
            'status_after' => 'diterima',
            'changed_by' => $habib->id,
            'reason' => 'Surat balasan resmi dari instansi menyatakan kelompok kami diterima.',
        ]);
    }

    public function test_leader_can_remove_member(): void
    {
        $habib = User::where('email', 'habib@mahasiswa.ac.id')->first();
        $group = Group::where('leader_id', $habib->id)->first();
        $fauzi = User::where('email', 'fauzi@mahasiswa.ac.id')->first();

        $this->assertTrue($fauzi->hasActiveGroup());

        // Pastikan kelompok tidak berstatus 'diterima' agar anggota sah dikeluarkan (Proteksi Ghost Quota Celah 5)
        \App\Models\InternshipApplication::where('group_id', $group->id)->where('status', 'diterima')->delete();

        $response = $this->actingAs($habib)->delete(route('groups.members.remove', [$group, $fauzi]));
        $response->assertRedirect(route('groups.show', $group));
        $response->assertSessionHas('success');

        // Fauzi status should be 'keluar'
        $fauzi->refresh();
        $this->assertFalse($fauzi->hasActiveGroup());
        $this->assertDatabaseHas('group_members', [
            'group_id' => $group->id,
            'user_id' => $fauzi->id,
            'status' => 'keluar',
        ]);
    }
}
