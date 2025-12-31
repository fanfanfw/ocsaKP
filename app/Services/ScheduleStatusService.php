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

        $this->syncScheduledLoans($schedules);

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
        $now = Carbon::now()->format('H:i:s');

        return Jadwal::query()
            ->with(['asset', 'user'])
            ->where('hari', $hari)
            ->where('jam_mulai', '<=', $now)
            ->where('jam_selesai', '>=', $now)
            ->get();
    }

    private function syncScheduledLoans(Collection $schedules): void
    {
        if ($schedules->isEmpty()) {
            return;
        }

        $today = Carbon::today();

        foreach ($schedules as $jadwal) {
            if (!$jadwal->asset || !$jadwal->user || $jadwal->user->role !== 'tentor') {
                continue;
            }

            $startAt = $today->copy()->setTimeFromTimeString($jadwal->jam_mulai);
            $endAt = $today->copy()->setTimeFromTimeString($jadwal->jam_selesai);

            $alreadyScheduled = Loan::where('asset_id', $jadwal->asset_id)
                ->where('user_id', $jadwal->user_id)
                ->whereBetween('tanggal_pinjam', [$startAt, $endAt])
                ->exists();

            if ($alreadyScheduled) {
                continue;
            }

            $activeLoans = Loan::where('asset_id', $jadwal->asset_id)
                ->where('status', 'Dipinjam')
                ->count();

            if ($activeLoans >= $jadwal->asset->jumlah) {
                continue;
            }

            Loan::create([
                'user_id' => $jadwal->user_id,
                'asset_id' => $jadwal->asset_id,
                'tanggal_pinjam' => now(),
                'status' => 'Dipinjam',
            ]);

            $this->syncAssetStatus($jadwal->asset);
        }
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
