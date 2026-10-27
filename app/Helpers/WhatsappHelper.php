<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class WhatsappHelper
{
    /**
     * Format nomor HP Indonesia ke format internasional WA (628xxx).
     */
    public static function formatPhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Hapus semua karakter non-digit dan spasi
        $clean = preg_replace('/[^0-9]/', '', $phone);

        // Ubah awalan 0 menjadi 62
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62' . $clean;
        }

        // Validasi panjang nomor telepon internasional (minimal 10 digit, maksimal 16 digit)
        if (strlen($clean) < 10 || strlen($clean) > 16) {
            return null;
        }

        return $clean;
    }

    /**
     * Masking nomor HP untuk kepatuhan UU PDP & anti-scraping publik bot.
     * Contoh: 081234567890 -> 0812-****-7890
     */
    public static function maskPhoneNumber(?string $phone): string
    {
        if (empty($phone)) {
            return '-';
        }

        $clean = preg_replace('/[^0-9]/', '', $phone);
        $len = strlen($clean);

        if ($len < 8) {
            return '********';
        }

        $start = substr($clean, 0, 4);
        $end = substr($clean, -4);

        return "{$start}-****-{$end}";
    }

    /**
     * Buat URL WhatsApp terintegrasi dengan pesan otomatis anti-XSS & Dynamic Auth-Protection.
     * Format: https://wa.me/{nomor_hp}?text={pesan_otomatis}
     * 
     * UU PDP Compliance: Link langsung hanya digenerate jika user terautentikasi (mencegah bot crawler scraping).
     */
    public static function generateLink(?string $phone, string $authorName, string $postTitle, bool $checkAuth = false): ?string
    {
        if ($checkAuth && !Auth::check()) {
            return null;
        }

        $formattedPhone = self::formatPhoneNumber($phone);
        if (!$formattedPhone) {
            return null;
        }

        // Sanitasi terhadap script injection & XSS
        $safeAuthor = htmlspecialchars(strip_tags($authorName), ENT_QUOTES, 'UTF-8');
        $safeTitle = htmlspecialchars(strip_tags($postTitle), ENT_QUOTES, 'UTF-8');

        $message = "Halo {$safeAuthor}, saya melihat postingan Anda di SIMAGANG mengenai {$safeTitle}. Apakah slot masih tersedia dan bisa berdiskusi lebih lanjut?";

        return "https://wa.me/{$formattedPhone}?text=" . urlencode($message);
    }
}
