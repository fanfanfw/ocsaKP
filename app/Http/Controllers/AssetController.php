<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetStatusLog;
use App\Services\ScheduleStatusService;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::query();

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('nama_aset', 'like', '%' . $search . '%')
                    ->orWhere('kategori', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%');
            });
        }

        $assets = $query->orderBy('nama_aset')->paginate(15)->withQueryString();
        $scheduleStatus = app(ScheduleStatusService::class);
        $scheduledIds = $scheduleStatus->currentScheduledAssetIds();

        $hari = $scheduleStatus->currentHariIndonesia();
        $scheduledCounts = \App\Models\Jadwal::selectRaw('asset_id, COUNT(*) as total')
            ->where('hari', $hari)
            ->where('status', 'Terjadwal')
            ->groupBy('asset_id')
            ->pluck('total', 'asset_id');

        $activeCounts = \App\Models\Loan::selectRaw('asset_id, COUNT(*) as total')
            ->where('status', 'Dipinjam')
            ->groupBy('asset_id')
            ->pluck('total', 'asset_id');

        return view('assets.index', [
            'assets' => $assets,
            'scheduledIds' => $scheduledIds,
            'activeCounts' => $activeCounts,
            'scheduledCounts' => $scheduledCounts,
        ]);
    }

    public function create()
    {
        return view('assets.create', [
            'materi_list' => \App\Models\Materi::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_aset' => ['required', 'string', 'max:255'],
            'jumlah' => ['nullable', 'integer', 'min:1'],
            'materi_ids' => ['nullable', 'array'],
            'materi_ids.*' => ['exists:materi,id'],
        ]);

        $asset = Asset::create([
            'nama_aset' => $validated['nama_aset'],
            'status' => 'Tersedia',
            'tahun' => null,
            'harga' => null,
            'jumlah' => $validated['jumlah'] ?? 1,
        ]);

        if (!empty($validated['materi_ids'])) {
            $asset->materi()->sync($validated['materi_ids']);
        }

        $this->logStatus($asset, $asset->status);

        return redirect()->route('assets.index')->with('success', 'Aset berhasil ditambahkan.');
    }

    public function edit(Asset $asset)
    {
        $lockedMateriIds = \App\Models\Jadwal::where('asset_id', $asset->id)
            ->distinct()
            ->pluck('materi_id')
            ->toArray();

        return view('assets.edit', [
            'asset' => $asset,
            'materi_list' => \App\Models\Materi::orderBy('nama')->get(),
            'lockedMateriIds' => $lockedMateriIds,
        ]);
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'nama_aset' => ['required', 'string', 'max:255'],
            'jumlah' => ['nullable', 'integer', 'min:1'],
            'materi_ids' => ['nullable', 'array'],
            'materi_ids.*' => ['exists:materi,id'],
        ]);

        $asset->update([
            'nama_aset' => $validated['nama_aset'],
            'jumlah' => $validated['jumlah'] ?? 1,
        ]);

        if (isset($validated['materi_ids'])) {
            $asset->materi()->sync($validated['materi_ids']);
        } else {
            // Jika array kosong tapi field ada di request (hidden input trik), sync empty
            // Untuk amannya, kita asumsikan form selalu kirim materi_ids minimal sebagai array kosong atau tidak kirim
            // Tapi karena checkbox HTML sifatnya kalau tidak dicentang tidak dikirim, kita perlu penanganan khusus di view.
            // Di sini kita asumsikan kalau user kirim update, kita update relasinya.
            // Namun, behaviour 'tidak centang semua' = 'hapus semua relasi' harus dihandle.
            // Laravel validate nullable array akan lolos jika null/tidak ada.
            // Kita gunakan $request->has('materi_ids') untuk cek apakah form field tersebut ada?
            // Biasanya di view kita kasih hidden input materi_ids = [] agar selalu terkirim.
            // Kita sync saja jika ada, atau kosongkan jika user bermaksud kosongkan.
            // Untuk simplicity: jika key exists, sync.
            // Mari kita pastikan di view nanti ada hidden input.
            $asset->materi()->sync($validated['materi_ids'] ?? []);
        }

        return redirect()->route('assets.index')->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();

        return redirect()->route('assets.index')->with('success', 'Aset berhasil dihapus.');
    }

    private function logStatus(Asset $asset, string $status): void
    {
        AssetStatusLog::create([
            'asset_id' => $asset->id,
            'status' => $status,
            'updated_at' => now(),
        ]);
    }


    public function getAvailableItems(Asset $asset)
    {
        $items = $asset->items()->where('is_available', true)->get(['id', 'code', 'condition']);
        return response()->json($items);
    }
}
