<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsKoordinator
{
    /**
     * Handle an incoming request.
     * Pastikan hanya user dengan role == 'koordinator' yang bisa mengakses prefix /admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('companies.index')
                ->with('error', 'Akses ditolak. Silakan login terlebih dahulu sebagai Koordinator Magang.');
        }

        if (Auth::user()->role !== 'koordinator') {
            abort(403, 'Akses Terlarang. Halaman ini hanya dapat diakses oleh Koordinator Magang Kampus.');
        }

        return $next($request);
    }
}
