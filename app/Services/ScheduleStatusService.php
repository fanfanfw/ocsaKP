<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetStatusLog;
use App\Models\Jadwal;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ScheduleStatusService
{
    public function currentScheduledAssetIds(): array
    {
        $schedules = $this->currentActiveSchedules();

        return $schedules
            ->pluck('asset_id')
            ->unique()
            ->values()
            ->all();
    }

    public function isScheduled(int $assetId, array $scheduledIds): bool
    {
        return in_array($assetId, $scheduledIds, true);
    }

    private function currentActiveSchedules(): Collection
    {
        $hari = $this->currentHariIndonesia();
        // Return schedules for today, regardless of time, to indicate reservation?
        // Wait, currentScheduledAssetIds is used for BADGES "Terjadwal".
        // If we want badges to show "Terjadwal" only during the time, keep time check.
        // User said "without waiting realtime" for DEDUCTION.
        // But for "Status" badge? Probably also "Terjadwal" for the whole day?
        // Let's stick to cleaning up syncScheduledLoans first.
        $now = Carbon::now()->format('H:i:s');

        return Jadwal::query()
            ->with(['asset', 'user'])
            ->where('hari', $hari)
            ->where('jam_mulai', '<=', $now)
            ->where('jam_selesai', '>=', $now)
            ->get();
    }

    public function currentHariIndonesia(): string
    {
        $map = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];

        $englishDay = Carbon::now()->englishDayOfWeek;

        return $map[$englishDay] ?? 'Senin';
    }

    private function syncAssetStatus(Asset $asset): void
    {
        $activeLoans = Loan::where('asset_id', $asset->id)
            ->where('status', 'Dipinjam')
            ->count();
        $newStatus = $activeLoans >= $asset->jumlah ? 'Dipinjam' : 'Tersedia';

        if ($asset->status !== $newStatus) {
            $asset->update(['status' => $newStatus]);
            AssetStatusLog::create([
                'asset_id' => $asset->id,
                'status' => $newStatus,
                'updated_at' => now(),
            ]);
        }
    }
}
