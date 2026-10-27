<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Status Pengajuan Magang</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 40px 16px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            padding: 32px 24px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 6px 0 0 0;
            font-size: 13px;
            color: #bfdbfe;
        }
        .content {
            padding: 32px 28px;
        }
        .badge-warning {
            display: inline-block;
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .greeting {
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 12px;
        }
        .text {
            font-size: 14px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 24px;
        }
        .card-details {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #edf2f7;
            font-size: 13px;
        }
        .detail-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .detail-label {
            color: #64748b;
            font-weight: 500;
        }
        .detail-value {
            color: #0f172a;
            font-weight: 700;
            text-align: right;
        }
        .cta-container {
            text-align: center;
            margin: 32px 0 20px 0;
        }
        .btn-primary {
            display: inline-block;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        }
        .instructions {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 14px 18px;
            border-radius: 0 8px 8px 0;
            font-size: 13px;
            color: #1e40af;
            margin-bottom: 24px;
            line-height: 1.5;
        }
        .footer {
            background-color: #f1f5f9;
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <!-- HEADER -->
            <div class="header">
                <h1>SIMAGANG</h1>
                <p>Sistem Informasi Magang & Manajemen Kuota Kampus</p>
            </div>

            <!-- CONTENT -->
            <div class="content">
                <div class="badge-warning">
                    ⚠️ Pengajuan Menggantung &ge; 14 Hari
                </div>

                <h2 class="greeting">
                    Halo, {{ $leader ? $leader->name : 'Ketua Kelompok' }}!
                </h2>

                <p class="text">
                    Sistem mendeteksi bahwa permohonan magang kelompok Anda saat ini masih berstatus <strong>"Menunggu"</strong> dan telah melewati <strong>{{ $daysWaiting }} hari</strong> kalender sejak pertama kali diajukan.
                </p>

                <!-- DETAIL CARD -->
                <div class="card-details">
                    <div class="detail-row">
                        <span class="detail-label">Nama Kelompok</span>
                        <span class="detail-value">{{ $group ? $group->name : '-' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Instansi Tujuan</span>
                        <span class="detail-value">{{ $company ? $company->name : '-' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Tanggal Diajukan</span>
                        <span class="detail-value">{{ $application->applied_at ? $application->applied_at->format('d F Y') : '-' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status Terakhir</span>
                        <span class="detail-value" style="color: #d97706;">MENUNGGU</span>
                    </div>
                    @if($company && $company->contact_person)
                    <div class="detail-row">
                        <span class="detail-label">PIC Instansi</span>
                        <span class="detail-value">{{ $company->contact_person }} ({{ $company->contact_phone ?? 'Telp belum ada' }})</span>
                    </div>
                    @endif
                </div>

                <!-- INSTRUKSI -->
                <div class="instructions">
                    <strong>Tindakan yang perlu dilakukan Ketua Kelompok:</strong>
                    <ul style="margin: 6px 0 0 0; padding-left: 20px;">
                        <li>Jika sudah ada surat/kabar resmi <strong>Diterima</strong> atau <strong>Ditolak</strong>, segera perbarui status pengajuan kelompok Anda di SIMAGANG.</li>
                        <li>Jika instansi belum merespon, segera tindak lanjuti narahubung instansi atau hubungi Koordinator Magang Kampus.</li>
                    </ul>
                </div>

                <!-- CTA BUTTON -->
                <div class="cta-container">
                    @if($group)
                        <a href="{{ route('groups.show', $group) }}" class="btn-primary" target="_blank">
                            Buka Dashboard Kelompok & Perbarui Status
                        </a>
                    @else
                        <a href="{{ url('/') }}" class="btn-primary" target="_blank">
                            Buka Aplikasi SIMAGANG
                        </a>
                    @endif
                </div>

                <p class="text" style="font-size: 12px; color: #94a3b8; text-align: center; margin-top: 20px;">
                    Pembaruan status tepat waktu membantu mempercepat pemetaan kuota resmi magang mahasiswa lainnya.
                </p>
            </div>

            <!-- FOOTER -->
            <div class="footer">
                <p style="margin: 0 0 4px 0;"><strong>Pusat Karir & Magang Mahasiswa</strong></p>
                <p style="margin: 0;">Email ini dikirim secara otomatis oleh sistem penjadwalan SIMAGANG. Mohon tidak membalas langsung ke alamat ini.</p>
            </div>
        </div>
    </div>
</body>
</html>
