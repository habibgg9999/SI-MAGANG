<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\InternshipApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_dashboard_with_onboarding_state(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Tahapan Progres Magang');
        $response->assertSee('Anda Belum Tergabung dalam Kelompok');
    }

    public function test_student_with_active_group_and_application_displays_stepper_correctly(): void
    {
        $leader = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Fadhil Pratama',
            'phone' => '081234567890',
        ]);

        $group = Group::create([
            'name' => 'CyberOps Alpha',
            'leader_id' => $leader->id,
            'max_members' => 3,
            'is_open' => true,
            'is_active' => true,
        ]);

        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $leader->id,
            'status' => 'aktif',
            'joined_at' => now(),
        ]);

        $company = Company::factory()->create([
            'name' => 'PT Telekomunikasi Indonesia',
            'quota' => 5,
            'occupied_slots' => 0,
            'is_active' => true,
            'is_approved' => true,
        ]);

        $app = InternshipApplication::create([
            'group_id' => $group->id,
            'company_id' => $company->id,
            'status' => 'menunggu',
            'applied_at' => now(),
            'deadline_reminder' => now()->addDays(14),
        ]);

        $response = $this->actingAs($leader)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('CyberOps Alpha');
        $response->assertSee('PT Telekomunikasi Indonesia');
        $response->assertSee('Menunggu Balasan');
        $response->assertSee('Tahap 3 dari 4');
        $response->assertSee('WhatsApp');
    }
}
