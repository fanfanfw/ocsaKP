<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Loan;
use App\Models\Maintenance;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAssets = Asset::count();
        $availableAssets = Asset::where('status', 'Tersedia')->count();
        $scheduledAssets = Asset::where('status', 'Terjadwal')->count();
        $activeLoans = Loan::where('status', 'Dipinjam')->count();
        $maintenanceOpen = Maintenance::whereNull('tanggal_selesai')->count();

        $statusCounts = Asset::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('dashboard', [
            'totalAssets' => $totalAssets,
            'availableAssets' => $availableAssets,
            'scheduledAssets' => $scheduledAssets,
            'activeLoans' => $activeLoans,
            'maintenanceOpen' => $maintenanceOpen,
            'statusCounts' => $statusCounts,
        ]);
    }
}
