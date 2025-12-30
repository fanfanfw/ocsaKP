<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Loan;
use App\Models\Maintenance;
use App\Services\ScheduleStatusService;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAssets = Asset::sum('jumlah');
        $activeLoans = Loan::where('status', 'Dipinjam')->count();
        $maintenanceOpen = Maintenance::whereNull('tanggal_selesai')->count();
        $assets = Asset::orderBy('nama_aset')->get();
        $scheduleStatus = app(ScheduleStatusService::class);
        $scheduledIds = $scheduleStatus->currentScheduledAssetIds();
        $activeCounts = Loan::selectRaw('asset_id, COUNT(*) as total')
            ->where('status', 'Dipinjam')
            ->groupBy('asset_id')
            ->pluck('total', 'asset_id');

        $statusCounts = [
            'Tersedia' => 0,
            'Terjadwal' => 0,
            'Dipinjam' => 0,
        ];

        foreach ($assets as $asset) {
            $available = max($asset->jumlah - ($activeCounts[$asset->id] ?? 0), 0);
            if ($scheduleStatus->isScheduled($asset->id, $scheduledIds)) {
                $statusCounts['Terjadwal']++;
            } elseif ($available <= 0) {
                $statusCounts['Dipinjam']++;
            } else {
                $statusCounts['Tersedia']++;
            }
        }

        $availableAssets = $statusCounts['Tersedia'];
        $scheduledAssets = $statusCounts['Terjadwal'];

        return view('dashboard', [
            'totalAssets' => $totalAssets,
            'availableAssets' => $availableAssets,
            'scheduledAssets' => $scheduledAssets,
            'activeLoans' => $activeLoans,
            'maintenanceOpen' => $maintenanceOpen,
            'statusCounts' => $statusCounts,
            'assets' => $assets,
            'scheduledIds' => $scheduledIds,
            'activeCounts' => $activeCounts,
        ]);
    }
}
