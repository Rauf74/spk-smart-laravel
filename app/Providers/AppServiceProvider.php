<?php

namespace App\Providers;

use App\Models\Penilaian;
use App\Observers\AuditObserver;
use App\Observers\PenilaianObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function ($event) {
            $event->user->update(['is_logged_in' => true]);
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Logout::class, function ($event) {
            if ($event->user) {
                $event->user->update(['is_logged_in' => false]);
            }
        });

        // Gate: Hanya Guru BK yang bisa akses Master Data
        \Illuminate\Support\Facades\Gate::define('access-master-data', function ($user) {
            return $user->role === 'Guru BK';
        });

        // Observer: auto-kirim email ke Guru BK saat siswa submit penilaian
        Penilaian::observe(PenilaianObserver::class);

        // Observer: audit log untuk model-model master
        $auditObserver = new AuditObserver();
        foreach (AuditObserver::auditableModels() as $modelClass) {
            $modelClass::observe($auditObserver);
        }

        // Behind Render's TLS-terminating proxy the request arrives as HTTP, so
        // Laravel would otherwise generate http:// asset and form URLs. Force
        // HTTPS in production to avoid Mixed Content errors.
        if (env('APP_ENV') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
