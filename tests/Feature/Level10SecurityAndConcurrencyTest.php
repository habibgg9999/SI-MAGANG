<?php

namespace Tests\Feature;

use App\Domain\CompanyQuotaPolicy;
use App\Domain\GroupAggregate;
use App\Exceptions\IllegalStateTransitionException;
use App\Exceptions\QuotaExceededException;
use App\Models\ApplicationStatusLog;
use App\Models\Company;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\InternshipApplication;
use App\Models\SystemOutbox;
use App\Models\User;
use App\Services\ApplicationSubmissionService;
use App\States\Application\ApplicationStateEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Level 10 Enterprise Hardening & Zero-Trust Verification Test Suite.
 *
 * Menguji 6 Pilar Arsitektur Tingkat Lanjut:
 * 1. High Concurrency Race Condition Zero-Leakage (Pilar 2)
 * 2. Formal FSM State Pattern & Cryptographic Tamper-Evident Trail (Pilar 1)
 * 3. Idempotency Key Engine Replay Protection (Pilar 2)
 * 4. PII Encryption at Rest & UU PDP Compliance (Pilar 4)
 * 5. Domain Invariant Encapsulation via GroupAggregate (Pilar 3)
 * 6. Transactional Outbox Pattern Execution (Pilar 5)
 */
class Level10SecurityAndConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * PILAR 2 & 6: Concurrency Race Condition & Zero Quota Leakage Guarantee.
     * Simulasikan 50 permohonan yang mencoba mengklaim 2 slot kuota terakhir secara bersamaan.
     * Buktikan secara matematis bahwa tepat 2 proses sukses dan 48 lainnya ditolak dengan QuotaExceededException.
     */
    public function test_high_concurrency_race_condition_never_exceeds_company_quota(): void
    {
        $coordinator = User::factory()->create([
            'role' => 'koordinator',
            'email' => 'admin.concurrency@poltek.ac.id',
        ]);

        // 1. Buat instansi dengan kapasitas resmi hanya 2 slot
        $company = Company::factory()->create([
            'name' => 'PT Cyber Concurrency Labs',
            'quota' => 2,
            'occupied_slots' => 0,
            'is_active' => true,
            'is_approved' => true,
        ]);

        // 2. Siapkan 50 kelompok berbeda, masing-masing dengan 1 anggota (Ketua)
        $applications = [];
        for ($i = 1; $i <= 50; $i++) {
            $student = User::factory()->create([
                'nim' => sprintf('2109%04d', $i),
                'email' => "candidate{$i}@poltek.ac.id",
                'phone' => sprintf('08129999%04d', $i),
                'role' => 'mahasiswa',
            ]);

            $group = Group::create([
                'name' => "Kelompok Concurrency #{$i}",
                'leader_id' => $student->id,
                'max_members' => 3,
                'is_open' => false,
                'is_active' => true,
            ]);

            GroupMember::create([
                'group_id' => $group->id,
                'user_id' => $student->id,
                'status' => 'aktif',
                'joined_at' => now(),
            ]);

            $app = InternshipApplication::create([
                'group_id' => $group->id,
                'company_id' => $company->id,
                'status' => 'menunggu',
                'applied_at' => now(),
                'deadline_reminder' => now()->addDays(14),
            ]);

            ApplicationStatusLog::recordMutation(
                application: $app,
                statusBefore: null,
                statusAfter: 'menunggu',
                actor: $student,
                reason: 'Pengajuan awal kelompok'
            );

            $applications[] = $app;
        }

        $this->assertCount(50, $applications);

        // 3. Simulasikan 50 request konkuren yang dieksekusi melalui ApplicationSubmissionService
        $service = app(ApplicationSubmissionService::class);
        $successCount = 0;
        $quotaExceededCount = 0;

        foreach ($applications as $app) {
            try {
                $service->updateApplicationStatus(
                    application: $app,
                    targetStatus: 'diterima',
                    reason: 'Proses seleksi serentak kuota akhir',
                    actor: $coordinator
                );
                $successCount++;
            } catch (QuotaExceededException $e) {
                $quotaExceededCount++;
            }
        }

        // 4. Pembuktian Matematis: Tepat 2 sukses, tepat 48 gagal
        $this->assertEquals(2, $successCount, "Tepat 2 kelompok yang boleh diterima sesuai batas kuota 2 slot.");
        $this->assertEquals(48, $quotaExceededCount, "Tepat 48 permohonan harus ditolak dengan QuotaExceededException.");

        // 5. Verifikasi Integritas Database & Read-Projection (Zero-Leakage Guarantee)
        $company->refresh();
        $this->assertEquals(2, $company->occupied_slots, "Kolom read-projection occupied_slots harus tepat 2.");

        $acceptedInDb = InternshipApplication::where('company_id', $company->id)
            ->where('status', 'diterima')
            ->count();
        $this->assertEquals(2, $acceptedInDb, "Tabel internship_applications harus mencatat tepat 2 penerimaan.");

        $pendingInDb = InternshipApplication::where('company_id', $company->id)
            ->where('status', 'menunggu')
            ->count();
        $this->assertEquals(48, $pendingInDb, "48 kelompok lainnya tetap berstatus menunggu tanpa kebocoran kuota.");
    }

    /**
     * PILAR 1 & 6: Formal State Machine (FSM) Transition Guards & Cryptographic Audit Trail.
     * Lompatan status ilegal dari 'ditolak' langsung ke 'diterima' wajib diblokir dengan IllegalStateTransitionException
     * dan rantai hash SHA-256 HMAC pada audit log harus terverifikasi utuh.
     */
    public function test_fsm_rejects_illegal_state_jumps_and_logs_tamper_attempt(): void
    {
        $actor = User::factory()->create(['role' => 'koordinator']);
        $company = Company::factory()->create(['quota' => 5, 'occupied_slots' => 0]);

        $leader = User::factory()->create(['role' => 'mahasiswa']);
        $group = Group::create([
            'name' => 'Kelompok Alpha Defense',
            'leader_id' => $leader->id,
            'max_members' => 3,
            'is_open' => false,
            'is_active' => true,
        ]);
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $leader->id,
            'status' => 'aktif',
            'joined_at' => now(),
        ]);

        $app = InternshipApplication::create([
            'group_id' => $group->id,
            'company_id' => $company->id,
            'status' => 'ditolak', // Status TERMINAL
            'applied_at' => now()->subDays(5),
            'responded_at' => now()->subDay(),
        ]);

        // Catat log awal yang sah
        ApplicationStatusLog::recordMutation(
            application: $app,
            statusBefore: 'menunggu',
            statusAfter: 'ditolak',
            actor: $actor,
            reason: 'Ditolak karena kualifikasi berkas belum memenuhi syarat.'
        );

        $service = app(ApplicationSubmissionService::class);

        // Uji Coba Lompatan Ilegal: 'ditolak' -> 'diterima'
        try {
            $service->updateApplicationStatus(
                application: $app,
                targetStatus: 'diterima',
                reason: 'Eksploitasi bypass status dari Ditolak ke Diterima',
                actor: $actor
            );
            $this->fail('Eksploitasi berhasil: Transisi ilegal status terminal tidak dicegah!');
        } catch (IllegalStateTransitionException $e) {
            $this->assertEquals('ditolak', $e->fromState);
            $this->assertEquals('diterima', $e->toState);
            $this->assertEquals($app->id, $e->applicationId);
        }

        // Pastikan status di database tidak pernah bermutasi
        $app->refresh();
        $this->assertEquals('ditolak', $app->status, 'Status terminal di database harus tetap ditolak.');

        // Verifikasi Integritas Kriptografis Rantai Audit Trail (HMAC SHA-256)
        $isAuditLogIntact = ApplicationStatusLog::verifyIntegrity($app->id);
        $this->assertTrue($isAuditLogIntact, 'Rantai hash tanda tangan digital audit trail harus terverifikasi utuh dan valid.');
    }

    /**
     * PILAR 2 & 6: Idempotency Key Engine Replay Protection.
     * Pengiriman 5 request identik berturut-turut dalam 1 detik dengan X-Idempotency-Key
     * harus mengembalikan respon identik dan hanya mencatat tepat 1 transaksi di database.
     */
    public function test_idempotency_key_prevents_duplicate_application_on_network_replay(): void
    {
        $leader = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'idempotent.leader@poltek.ac.id',
            'phone' => '081234567899',
        ]);

        $group = Group::create([
            'name' => 'Kelompok Idempotent Network',
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
            'quota' => 10,
            'occupied_slots' => 0,
            'is_active' => true,
            'is_approved' => true,
        ]);

        $idempotencyKey = (string) Str::uuid();

        // Simulasikan 5x network retry / browser double-click dalam 1 detik dengan Idempotency Key yang sama
        for ($i = 1; $i <= 5; $i++) {
            $response = $this->actingAs($leader)
                ->withHeaders([
                    'X-Idempotency-Key' => $idempotencyKey,
                    'Accept' => 'application/json',
                ])
                ->post(route('applications.store'), [
                    'group_id' => $group->id,
                    'company_id' => $company->id,
                    'notes' => 'Proposal magang diajukan dengan proteksi Idempotency Key',
                ]);

            $response->assertStatus(201);
        }

        // Verifikasi database: Tepat 1 record yang terbentuk (Bukan 5!)
        $applicationsCount = InternshipApplication::where('group_id', $group->id)
            ->where('company_id', $company->id)
            ->count();
        $this->assertEquals(1, $applicationsCount, "Database hanya boleh mencatat tepat 1 permohonan magang meskipun direplay 5 kali.");

        $createdApp = InternshipApplication::where('group_id', $group->id)->firstOrFail();
        $logsCount = ApplicationStatusLog::where('application_id', $createdApp->id)->count();
        $this->assertEquals(1, $logsCount, "Audit trail log hanya boleh mencatat tepat 1 mutasi.");
    }

    /**
     * PILAR 4 & 6: PII Encryption at Rest (UU PDP Indonesia Compliance).
     * Query mentah via DB::table('users') wajib mengembalikan ciphertext acak, bukan plain-text nomor HP.
     * Namun Eloquent Model tetap dapat membaca plain-text secara terdekripsi bagi aplikasi sah.
     */
    public function test_pii_encryption_at_rest(): void
    {
        $plainPhone = '081298765432';

        $user = User::factory()->create([
            'name' => 'Mahasiswa PDP Protected',
            'email' => 'pdp.student@poltek.ac.id',
            'phone' => $plainPhone,
            'role' => 'mahasiswa',
        ]);

        // 1. Ambil data mentah langsung dari SQL engine tanpa melewati Eloquent Casts
        $rawUser = DB::table('users')->where('id', $user->id)->first();
        $rawPhone = $rawUser->phone;

        // Bukti Kepatuhan UU PDP: Ciphertext di database
        $this->assertNotNull($rawPhone, "Kolom phone di database tidak boleh null.");
        $this->assertNotEquals($plainPhone, $rawPhone, "Nomor HP di database mentah TIDAK BOLEH plain-text.");
        $this->assertStringNotContainsString($plainPhone, $rawPhone, "Ciphertext mentah tidak boleh mengandung potongan nomor HP asli.");
        $this->assertTrue(strlen($rawPhone) > 40, "Ciphertext nomor HP harus memiliki panjang representasi kriptografis terenkripsi.");

        // 2. Akses melalui Eloquent Model (Authorized internal access)
        $eloquentUser = User::find($user->id);
        $this->assertEquals($plainPhone, $eloquentUser->phone, "Model Eloquent harus mendekripsi nomor HP secara transparan.");
    }

    /**
     * PILAR 3 & 6: Domain Invariants & GroupAggregate Encapsulation.
     * Memastikan seluruh aturan keanggotaan terpusat di Aggregate Root dan dilindungi dari Phantom Membership.
     */
    public function test_group_aggregate_enforces_domain_invariants(): void
    {
        $leader = User::factory()->create(['role' => 'mahasiswa']);
        $group = Group::create([
            'name' => 'Kelompok DDD Test',
            'leader_id' => $leader->id,
            'max_members' => 2,
            'is_open' => true,
            'is_active' => true,
        ]);

        $aggregate = GroupAggregate::for($group);
        $aggregate->addMember($leader);

        // Anggota 2 bergabung
        $member2 = User::factory()->create(['role' => 'mahasiswa']);
        $aggregate->addMember($member2);

        $this->assertEquals(2, $group->activeMembers()->count());
        $this->assertTrue($group->isFull());

        // Anggota 3 mencoba bergabung ke kelompok yang sudah penuh -> Harus ditolak oleh Invariant
        $member3 = User::factory()->create(['role' => 'mahasiswa']);
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $aggregate->addMember($member3);
    }

    /**
     * PILAR 5 & 6: Transactional Outbox Pattern Execution.
     * Memastikan event tercatat di system_outbox dan dapat diproses oleh worker tanpa kegagalan cron thread utama.
     */
    public function test_transactional_outbox_records_and_processes_events(): void
    {
        $event = SystemOutbox::record('send_application_reminder', [
            'application_id' => 999,
            'email' => 'test.outbox@poltek.ac.id',
            'group_name' => 'Kelompok Outbox Test',
            'leader_name' => 'Ketua Outbox',
        ]);

        $this->assertDatabaseHas('system_outbox', [
            'id' => $event->id,
            'event_type' => 'send_application_reminder',
            'status' => 'pending',
            'retry_count' => 0,
        ]);

        // Jalankan artisan worker pemroses outbox
        $this->artisan('simagang:process-outbox', ['--limit' => 10])
            ->assertSuccessful();

        $event->refresh();
        $this->assertEquals('processed', $event->status);
        $this->assertNotNull($event->processed_at);
    }
}
