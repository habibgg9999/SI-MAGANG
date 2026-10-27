<?php

namespace Tests\Feature;

use App\Helpers\WhatsappHelper;
use App\Models\ForumPost;
use App\Models\Group;
use App\Models\User;
use Database\Seeders\CompanySeeder;
use Database\Seeders\MagangInteractionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ForumMatchmakingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);
        $this->seed(CompanySeeder::class);
        $this->seed(MagangInteractionSeeder::class);
    }

    public function test_forum_feed_loads_and_displays_tabs(): void
    {
        $response = $this->get(route('forum.index'));

        $response->assertStatus(200);
        $response->assertSee('Forum Pencarian Tim Magang');
        $response->assertSee('Butuh Anggota');
        $response->assertSee('Cari Kelompok');
    }

    public function test_whatsapp_link_generator_format(): void
    {
        // 1. Test helper directly
        $phone = '082198765432';
        $authorName = 'Habib Hanafi';
        $title = 'Dicari Frontend Developer React';

        $waLink = WhatsappHelper::generateLink($phone, $authorName, $title);

        $this->assertNotNull($waLink);
        $this->assertStringStartsWith('https://wa.me/6282198765432?text=', $waLink);
        $this->assertStringContainsString(urlencode('Halo Habib Hanafi'), $waLink);
        $this->assertStringContainsString(urlencode('saya melihat postingan Anda di SIMAGANG mengenai Dicari Frontend Developer React'), $waLink);

        // 2. Test via Model accessor
        $post = ForumPost::first();
        $this->assertNotNull($post);
        $this->assertNotNull($post->whatsapp_url);
        $this->assertStringStartsWith('https://wa.me/', $post->whatsapp_url);
    }

    public function test_group_leader_can_post_cari_anggota(): void
    {
        $habib = User::where('email', 'habib@mahasiswa.ac.id')->first();
        $group = Group::where('leader_id', $habib->id)->first();
        $this->assertNotNull($group);

        $response = $this->actingAs($habib)->post(route('forum.store'), [
            'type' => 'cari_anggota',
            'title' => 'Dibutuhkan 1 Backend Go / Laravel',
            'body' => 'Kelompok kami fokus pada integrasi API instansi SPBE.',
            'field' => 'Backend Developer',
            'target_company' => 'Diskominfo Provinsi',
            'slots_needed' => 1,
        ]);

        $response->assertRedirect(route('forum.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('forum_posts', [
            'user_id' => $habib->id,
            'group_id' => $group->id,
            'type' => 'cari_anggota',
            'title' => 'Dibutuhkan 1 Backend Go / Laravel',
            'slots_needed' => 1,
        ]);
    }

    public function test_solo_student_can_post_cari_kelompok(): void
    {
        // Create student without group
        $soloStudent = User::create([
            'nim' => '22081010888',
            'name' => 'Solo Student UI/UX',
            'email' => 'solo@mahasiswa.ac.id',
            'phone' => '081233445588',
            'prodi' => 'Sistem Informasi',
            'role' => 'mahasiswa',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($soloStudent)->post(route('forum.store'), [
            'type' => 'cari_kelompok',
            'title' => 'UI/UX Designer Berpengalaman Figma Mencari Kelompok BUMN',
            'body' => 'Halo, saya memiliki portofolio 3 desain aplikasi mobile dan siap berkontribusi.',
            'field' => 'UI/UX Designer',
            'target_company' => 'PT Telkom Indonesia',
        ]);

        $response->assertRedirect(route('forum.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('forum_posts', [
            'user_id' => $soloStudent->id,
            'group_id' => null,
            'type' => 'cari_kelompok',
            'title' => 'UI/UX Designer Berpengalaman Figma Mencari Kelompok BUMN',
        ]);
    }

    public function test_non_leader_cannot_post_cari_anggota(): void
    {
        // Siti is a member of DevForce Alpha, not the leader
        $siti = User::where('email', 'siti@mahasiswa.ac.id')->first();

        $response = $this->actingAs($siti)->post(route('forum.store'), [
            'type' => 'cari_anggota',
            'title' => 'Post ilegal bukan ketua',
            'body' => 'Mencoba membuat postingan cari anggota tanpa hak ketua.',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('forum_posts', [
            'title' => 'Post ilegal bukan ketua',
        ]);
    }

    public function test_forum_filters_by_type_and_field(): void
    {
        // Filter by type: cari_anggota
        $responseAnggota = $this->get(route('forum.index', ['type' => 'cari_anggota']));
        $responseAnggota->assertStatus(200);
        $responseAnggota->assertSee('Butuh Anggota');

        // Filter by type: cari_kelompok
        $responseKelompok = $this->get(route('forum.index', ['type' => 'cari_kelompok']));
        $responseKelompok->assertStatus(200);
        $responseKelompok->assertSee('Cari Kelompok');

        // Filter by field
        $responseField = $this->get(route('forum.index', ['field' => 'Frontend Developer']));
        $responseField->assertStatus(200);
        $responseField->assertSee('Frontend Engineer');
    }

    public function test_author_can_toggle_fulfilled_and_delete_post(): void
    {
        $habib = User::where('email', 'habib@mahasiswa.ac.id')->first();
        $post = ForumPost::where('user_id', $habib->id)->first();
        $this->assertNotNull($post);
        $this->assertFalse($post->is_fulfilled);

        // 1. Toggle fulfilled
        $responseToggle = $this->actingAs($habib)->patch(route('forum.toggle', $post));
        $responseToggle->assertSessionHas('success');

        $post->refresh();
        $this->assertTrue($post->is_fulfilled);

        // 2. Delete post
        $responseDelete = $this->actingAs($habib)->delete(route('forum.destroy', $post));
        $responseDelete->assertSessionHas('success');

        $this->assertDatabaseMissing('forum_posts', [
            'id' => $post->id,
        ]);
    }
}
