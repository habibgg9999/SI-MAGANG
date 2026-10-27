<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Idempotency Engine Middleware (Pilar 2)
 *
 * Mencegah duplikasi transaksi pada network replay, timeout browser, dan double-click.
 * Jika X-Idempotency-Key dikirim ulang dalam waktu 120 detik, server langsung mengembalikan
 * respon identik yang telah tersimpan di cache tanpa memicu re-eksekusi controller atau kena blokir rate limiter.
 */
class EnsureIdempotency
{
    public function handle(Request $request, Closure $next): Response
    {
        $idempotencyKey = $request->header('X-Idempotency-Key');

        if (!$idempotencyKey) {
            return $next($request);
        }

        $cacheKey = "idempotency_http_resp:{$idempotencyKey}";
        $cached = Cache::get($cacheKey);

        if ($cached) {
            return response($cached['content'], $cached['status'])
                ->header('Content-Type', $cached['content_type'] ?? 'application/json')
                ->header('X-Idempotent-Replay', 'true');
        }

        $response = $next($request);

        // Simpan respon sukses (2xx) ke cache selama 120 detik
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            Cache::put($cacheKey, [
                'content' => $response->getContent(),
                'status' => $response->getStatusCode(),
                'content_type' => $response->headers->get('Content-Type', 'application/json'),
            ], 120);
        }

        return $response;
    }
}
