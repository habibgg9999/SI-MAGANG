<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Pilar 2 & 3: Tambah occupied_slots pada companies (Read-Projection / CQRS Lite)
        if (!Schema::hasColumn('companies', 'occupied_slots')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->unsignedInteger('occupied_slots')->default(0)->after('quota');
            });
        }

        // 2. Pilar 1: Tambah kolom Cryptographic Audit Trail pada application_status_logs
        Schema::table('application_status_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('application_status_logs', 'actor_id')) {
                $table->foreignId('actor_id')->nullable()->after('changed_by')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('application_status_logs', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('actor_id');
            }
            if (!Schema::hasColumn('application_status_logs', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
            if (!Schema::hasColumn('application_status_logs', 'hash_signature')) {
                $table->string('hash_signature', 64)->nullable()->after('user_agent');
            }
            if (!Schema::hasColumn('application_status_logs', 'microtime')) {
                $table->string('microtime', 30)->nullable()->after('hash_signature');
            }
        });

        // 3. Pilar 5: Tabel Transactional Outbox Pattern untuk Email & Scheduler
        if (!Schema::hasTable('system_outbox')) {
            Schema::create('system_outbox', function (Blueprint $table) {
                $table->id();
                $table->string('event_type')->index();
                $table->json('payload');
                $table->enum('status', ['pending', 'processing', 'processed', 'failed'])->default('pending')->index();
                $table->unsignedInteger('retry_count')->default(0);
                $table->text('error_message')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();
            });
        }

        // 4. Pilar 2: Invariant Enforcer - Unique Index Parsial Keanggotaan Aktif (SQLite & Postgres)
        // Mahasiswa hanya boleh memiliki tepat 1 baris aktif di group_members
        try {
            DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS unique_user_active_membership ON group_members(user_id) WHERE status = 'aktif'");
        } catch (\Throwable $e) {
            // Abaikan jika driver database tidak mendukung partial index (misal MySQL lama)
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement("DROP INDEX IF EXISTS unique_user_active_membership");
        } catch (\Throwable $e) {
            //
        }

        Schema::dropIfExists('system_outbox');

        Schema::table('application_status_logs', function (Blueprint $table) {
            $table->dropColumn(['actor_id', 'ip_address', 'user_agent', 'hash_signature', 'microtime']);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('occupied_slots');
        });
    }
};
