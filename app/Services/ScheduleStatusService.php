<?php

namespace App\Services;

use App\Models\Jadwal;
use Carbon\Carbon;

class ScheduleStatusService
{
    public function currentScheduledAssetIds(): array
    {
        $hari = $this->currentHariIndonesia();
        $now = Carbon::now()->format('H:i:s');

        return Jadwal::query()
            ->where('hari', $hari)
            ->where('jam_mulai', '<=', $now)
            ->where('jam_selesai', '>=', $now)
            ->pluck('asset_id')
            ->unique()
            ->values()
            ->all();
    }

    public function isScheduled(int $assetId, array $scheduledIds): bool
    {
        return in_array($assetId, $scheduledIds, true);
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
}
