<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Modul 6: Automation & Reminder 14 Hari
// Jalankan otomatis setiap hari untuk mendeteksi permohonan magang berstatus 'menunggu' >= 14 hari
Schedule::command('simagang:send-reminders')
    ->daily()
    ->at('08:00')
    ->withoutOverlapping();
