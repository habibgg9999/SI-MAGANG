<?php

namespace Tests\Feature;

use App\Mail\ApplicationReminderMail;
use App\Models\Company;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\InternshipApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ApplicationReminderCommandTest extends TestCase
{
    use RefreshDatabase;

    protected User $leader;
    protected Group $group;
    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->leader = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Fadhil Pratama',
            'email' => 'fadhil@mahasiswa.ac.id',
        ]);

        $this->group = Group::create([
            'name' => 'Cloud Engineers Hub',
            'leader_id' => $this->leader->id,
            'max_members' => 4,
            'is_open' => false,
            'is_active' => true,
        ]);

        GroupMember::create([
            'group_id' => $this->group->id,
            'user_id' => $this->leader->id,
            'status' => 'aktif',
            'joined_at' => now()->subDays(20),
        ]);

        $this->company = Company::create([
            'name' => 'PT Cloud Hosting Indonesia',
            'type' => 'swasta',
            'quota' => 5,
            'city' => 'Jakarta Selatan',
            'field' => 'Cloud Computing',
            'contact_person' => 'Bpk. Ahmad PIC',
            'contact_phone' => '08123456789',
            'is_active' => true,
            'is_approved' => true,
        ]);
    }

    /**
     * Test 1: Command mengirim email pengingat jika pengajuan berstatus 'menunggu' dan >= 14 hari.
     */
    public function test_command_sends_email_to_leader_when_application_pending_14_days_or_more(): void
    {
        Mail::fake();

        $application = InternshipApplication::create([
            'group_id' => $this->group->id,
            'company_id' => $this->company->id,
            'status' => 'menunggu',
            'applied_at' => Carbon::now()->subDays(15),
            'deadline_reminder' => null,
        ]);

        $this->artisan('simagang:send-reminders')
            ->expectsOutputToContain('Ditemukan 1 permohonan magang yang memerlukan pengingat.')
            ->expectsOutputToContain('Proses selesai. Berhasil: 1, Gagal: 0.')
            ->assertSuccessful();

        // Verifikasi email Mailable terkirim ke alamat email ketua kelompok
        Mail::assertSent(ApplicationReminderMail::class, function ($mail) {
            return $mail->hasTo('fadhil@mahasiswa.ac.id') &&
                   $mail->application->group_id === $this->group->id;
        });

        // Verifikasi deadline_reminder di database diperbarui
        $application->refresh();
        $this->assertNotNull($application->deadline_reminder);
    }

    /**
     * Test 2: Command TIDAK mengirim email jika pengajuan masih kurang dari 14 hari.
     */
    public function test_command_does_not_send_email_if_application_is_under_14_days(): void
    {
        Mail::fake();

        InternshipApplication::create([
            'group_id' => $this->group->id,
            'company_id' => $this->company->id,
            'status' => 'menunggu',
            'applied_at' => Carbon::now()->subDays(7), // baru 7 hari
            'deadline_reminder' => null,
        ]);

        $this->artisan('simagang:send-reminders')
            ->expectsOutputToContain('Tidak ada permohonan magang berstatus "menunggu" yang mencapai batas 14 hari tanpa respon.')
            ->assertSuccessful();

        Mail::assertNothingSent();
    }

    /**
     * Test 3: Command TIDAK mengirim email jika status pengajuan BUKAN 'menunggu' (misal: 'diterima' / 'ditolak').
     */
    public function test_command_does_not_send_email_if_status_is_already_responded(): void
    {
        Mail::fake();

        // Pengajuan sudah diterima 20 hari lalu
        InternshipApplication::create([
            'group_id' => $this->group->id,
            'company_id' => $this->company->id,
            'status' => 'diterima',
            'applied_at' => Carbon::now()->subDays(25),
            'responded_at' => Carbon::now()->subDays(5),
            'deadline_reminder' => null,
        ]);

        $this->artisan('simagang:send-reminders')
            ->expectsOutputToContain('Tidak ada permohonan magang berstatus "menunggu" yang mencapai batas 14 hari tanpa respon.')
            ->assertSuccessful();

        Mail::assertNothingSent();
    }

    /**
     * Test 4: Mailable ApplicationReminderMail merender informasi kelompok, instansi, dan durasi dengan tepat.
     */
    public function test_application_reminder_mailable_content_and_subject(): void
    {
        $application = InternshipApplication::create([
            'group_id' => $this->group->id,
            'company_id' => $this->company->id,
            'status' => 'menunggu',
            'applied_at' => Carbon::now()->subDays(16),
        ]);

        $mailable = new ApplicationReminderMail($application);

        // Assert subject
        $envelope = $mailable->envelope();
        $this->assertStringContainsString('Pengingat Status Pengajuan Magang', $envelope->subject);
        $this->assertStringContainsString('PT Cloud Hosting Indonesia', $envelope->subject);

        // Assert rendered HTML view
        $mailable->assertSeeInHtml('Fadhil Pratama');
        $mailable->assertSeeInHtml('Cloud Engineers Hub');
        $mailable->assertSeeInHtml('PT Cloud Hosting Indonesia');
        $mailable->assertSeeInHtml('16 hari');
        $mailable->assertSeeInHtml('MENUNGGU');
    }

    /**
     * Test 5: Command terdaftar pada schedule harian di Laravel.
     */
    public function test_reminder_command_is_scheduled_daily(): void
    {
        $schedule = app()->make(Schedule::class);

        $events = collect($schedule->events())->filter(function ($event) {
            return str_contains($event->command, 'simagang:send-reminders');
        });

        $this->assertNotEmpty($events, 'Command simagang:send-reminders belum terdaftar di Schedule.');
        $this->assertEquals('0 8 * * *', $events->first()->expression);
    }
}
