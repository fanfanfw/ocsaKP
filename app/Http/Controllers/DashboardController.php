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
        $assets = Asset::with('materi')->orderBy('nama_aset')->get();
        $scheduleStatus = app(ScheduleStatusService::class);
        $scheduledIds = $scheduleStatus->currentScheduledAssetIds();
        $activeCounts = Loan::selectRaw('asset_id, COUNT(*) as total')
            ->where('status', 'Dipinjam')
            ->groupBy('asset_id')
            ->pluck('total', 'asset_id');

        $hari = $scheduleStatus->currentHariIndonesia();
        $scheduledCounts = \App\Models\Jadwal::selectRaw('asset_id, COUNT(*) as total')
            ->where('hari', $hari)
            ->where('status', 'Terjadwal')
            ->groupBy('asset_id')
            ->pluck('total', 'asset_id');

        $statusCounts = [
            'Tersedia' => 0,
            'Terjadwal' => 0,
            'Dipinjam' => 0,
        ];

        foreach ($assets as $asset) {
            $scheduled = $scheduledCounts[$asset->id] ?? 0;
            $active = $activeCounts[$asset->id] ?? 0;
            $available = max($asset->jumlah - $active - $scheduled, 0);

            if ($scheduleStatus->isScheduled($asset->id, $scheduledIds)) {
                $statusCounts['Terjadwal']++;
            } elseif ($available <= 0) {
                // Determine if fully booked by loans or schedules logic?
                // Logic mismatch potential. Prioritize 'Dipinjam' or 'Terjadwal'?
                // Original logic: if isScheduled -> Terjadwal.
                // Here we want to count items.
                // StatusCounts is for PIE CHART composition.
                // Let's keep original pie logic "Status Asset" roughly same?
                // BUT User wants "Otomatis berkurang".
                // If I have 1 item. Reserved (Terjadwal). Available = 0.
                // Should it count as "Tersedia"? No.
                // Should it count as "Terjadwal"? Yes.
                // Existing logic: if ($isScheduled) -> Terjadwal.
                // This seems mostly fine.
                // Only $available variable needs update for accurate logic if used elsewhere.
                // Dashboard view uses $available for badge logic too (below loop).
            }

            if ($available <= 0 && !$scheduleStatus->isScheduled($asset->id, $scheduledIds)) {
                $statusCounts['Dipinjam']++; // Fully used but NOT by schedule? (e.g. manual loan)
            } elseif ($available > 0 && !$scheduleStatus->isScheduled($asset->id, $scheduledIds)) {
                $statusCounts['Tersedia']++;
            }
            // Wait, the else logic in original code was simpler.
            // Original: 
            // if (isScheduled) -> Terjadwal
            // elseif (available <= 0) -> Dipinjam
            // else -> Tersedia
            // With new logic, if isScheduled is true, it goes to Terjadwal.
            // So if I have 1 item, Scheduled. Available=0. 
            // It goes to Terjadwal. Correct.
            // If I have 1 item. Direct Loan. Scheduled=0. Available=0.
            // isScheduled=false. available<=0. -> Dipinjam. Correct.
            // So I just need to update $available calculation in the loop?
            // But $statusCounts logic relies on $available?
            // Actually, the original logic: "$available = max($asset->jumlah - $activeCounts...)"
            // If I change $available to subtract scheduled, it doesn't hurt the elseif branches.
        }

        // Rethink:
        // The loop is just counting:
        // 1. Is it currently scheduled? -> Terjadwal.
        // 2. Is it out of stock? -> Dipinjam.
        // 3. Else -> Tersedia.

        // If I update $available calculation, line 35 `elseif ($available <= 0)` will trigger correctly for depletion.
        // So I just need to update $available calculation.

        foreach ($assets as $asset) {
            $scheduled = $scheduledCounts[$asset->id] ?? 0;
            $active = $activeCounts[$asset->id] ?? 0;
            $available = max($asset->jumlah - $active - $scheduled, 0);

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
