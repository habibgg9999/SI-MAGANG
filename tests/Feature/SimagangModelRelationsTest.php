<?php

namespace Tests\Feature;

use App\Models\ApplicationStatusLog;
use App\Models\Company;
use App\Models\CompanyRequest;
use App\Models\ForumPost;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\InternshipApplication;
use App\Models\User;
use Database\Seeders\CompanySeeder;
use Database\Seeders\MagangInteractionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimagangModelRelationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);
        $this->seed(CompanySeeder::class);
        $this->seed(MagangInteractionSeeder::class);
    }

    public function test_users_and_seeders_are_created_properly(): void
    {
        $koordinator = User::where('role', 'koordinator')->first();
        $this->assertNotNull($koordinator);
        $this->assertTrue($koordinator->isKoordinator());
        $this->assertFalse($koordinator->isMahasiswa());

        $mhs = User::where('email', 'habib@mahasiswa.ac.id')->first();
        $this->assertNotNull($mhs);
        $this->assertTrue($mhs->isMahasiswa());
        $this->assertEquals('21081010001', $mhs->nim);
    }

    public function test_group_and_user_many_to_many_relationships(): void
    {
        $group = Group::where('name', 'DevForce Alpha')->first();
        $this->assertNotNull($group);
        $this->assertEquals('Muhammad Habib Hanafi', $group->leader->name);

        // Many-to-many relationship
        $this->assertCount(3, $group->users);
        $this->assertTrue($group->users->contains('email', 'habib@mahasiswa.ac.id'));
        $this->assertTrue($group->users->contains('email', 'fauzi@mahasiswa.ac.id'));
        $this->assertTrue($group->users->contains('email', 'siti@mahasiswa.ac.id'));

        // Pivot access
        $firstMember = $group->users->first();
        $this->assertEquals('aktif', $firstMember->pivot->status);

        // Reverse Many-to-many from User
        $user = User::where('email', 'habib@mahasiswa.ac.id')->first();
        $this->assertCount(1, $user->ledGroups);
        $this->assertCount(1, $user->groups);
        $this->assertEquals('DevForce Alpha', $user->groups->first()->name);
    }

    public function test_company_and_internship_applications_relationships(): void
    {
        $company = Company::where('name', 'LIKE', '%Telkom%')->first();
        $this->assertNotNull($company);
        $this->assertEquals('BUMN', $company->type);
        $this->assertNotEmpty($company->applications);

        $app = $company->applications->first();
        $this->assertEquals('DevForce Alpha', $app->group->name);
        $this->assertEquals($company->id, $app->company->id);

        // Test status logs
        $this->assertNotEmpty($app->statusLogs);
        $log = $app->statusLogs->first();
        $this->assertEquals('menunggu', $log->status_after);
        $this->assertNotNull($log->changedBy);
    }

    public function test_forum_posts_relationships(): void
    {
        $postAnggota = ForumPost::cariAnggota()->first();
        $this->assertNotNull($postAnggota);
        $this->assertEquals('cari_anggota', $postAnggota->type);
        $this->assertNotNull($postAnggota->user);
        $this->assertNotNull($postAnggota->group);
        $this->assertEquals('DevForce Alpha', $postAnggota->group->name);

        $postKelompok = ForumPost::cariKelompok()->first();
        $this->assertNotNull($postKelompok);
        $this->assertEquals('cari_kelompok', $postKelompok->type);
        $this->assertNotNull($postKelompok->user);
        $this->assertNull($postKelompok->group);
    }

    public function test_company_requests_relationships(): void
    {
        $approvedRequest = CompanyRequest::approved()->first();
        $this->assertNotNull($approvedRequest);
        $this->assertNotNull($approvedRequest->requestedBy);
        $this->assertEquals('mahasiswa', $approvedRequest->requestedBy->role);
        $this->assertNotNull($approvedRequest->reviewedBy);
        $this->assertEquals('koordinator', $approvedRequest->reviewedBy->role);

        $pendingRequest = CompanyRequest::pending()->first();
        $this->assertNotNull($pendingRequest);
        $this->assertNotNull($pendingRequest->requestedBy);
        $this->assertNull($pendingRequest->reviewedBy);
    }

    public function test_company_scopes(): void
    {
        $this->assertGreaterThan(0, Company::active()->count());
        $this->assertGreaterThan(0, Company::approved()->count());
        $this->assertGreaterThan(0, Company::byType('pemerintah')->count());
        $this->assertGreaterThan(0, Company::byType('swasta')->count());
        $this->assertGreaterThan(0, Company::byType('BUMN')->count());
        $this->assertGreaterThan(0, Company::byType('NGO')->count());
    }
}
