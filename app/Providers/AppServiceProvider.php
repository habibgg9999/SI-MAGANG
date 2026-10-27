<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Group;
use App\Models\InternshipApplication;
use App\Policies\ApplicationPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\GroupPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Authorization Policies
        Gate::policy(Group::class, GroupPolicy::class);
        Gate::policy(InternshipApplication::class, ApplicationPolicy::class);
        Gate::policy(Company::class, CompanyPolicy::class);

        // Pilar 4: Multi-Layer Rate Limiting
        // 1. /login: 5 percobaan per menit per IP
        RateLimiter::for('login', function (Request $request) {
            return app()->environment('testing') 
                ? Limit::none() 
                : Limit::perMinute(5)->by($request->ip());
        });

        // 2. Pembuatan kelompok: 3 request per 10 menit per user ID
        RateLimiter::for('group-creation', function (Request $request) {
            return app()->environment('testing') 
                ? Limit::none() 
                : Limit::perMinutes(10, 3)->by($request->user()?->id ?: $request->ip());
        });

        // 3. Postingan forum: 3 request per 10 menit per user ID
        RateLimiter::for('forum-post', function (Request $request) {
            return app()->environment('testing') 
                ? Limit::none() 
                : Limit::perMinutes(10, 3)->by($request->user()?->id ?: $request->ip());
        });

        // 4. Tombol "Ajukan Magang": 1 submission per 60 detik per kelompok
        RateLimiter::for('application-submission', function (Request $request) {
            if (app()->environment('testing')) {
                return Limit::none();
            }
            $groupId = $request->input('group_id') ?? $request->user()?->currentActiveGroup()?->id;
            return Limit::perMinute(1)->by($groupId ?: ($request->user()?->id ?: $request->ip()));
        });
    }
}
