<?php

namespace App\Providers;

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
        view()->composer('layouts.app', function ($view) {
            $pendingApprovalsCount = 0;
            $pendingReceivalCount = 0;

            if (auth()->check()) {
                if (auth()->user()->role === 'admin') {
                    $pendingApprovalsCount = \App\Models\Booking::where('status', 'pending')->count();
                } elseif (auth()->user()->role === 'tentor') {
                    $scheduleStatus = app(\App\Services\ScheduleStatusService::class);
                    $hari = $scheduleStatus->currentHariIndonesia();

                    $pendingReceivalCount = \App\Models\Jadwal::where('user_id', auth()->id())
                        ->where('status', 'Terjadwal')
                        ->where('hari', $hari)
                        ->count();
                }
            }

            $view->with('pendingApprovalsCount', $pendingApprovalsCount);
            $view->with('pendingReceivalCount', $pendingReceivalCount);
        });
    }
}
