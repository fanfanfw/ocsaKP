<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetItem;
use App\Models\AssetStatusLog;
use App\Services\ScheduleStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

        $assets = $query
            ->withCount([
                'items',
                'items as available_items_count' => function ($q) {
                    $q->where('is_available', true);
                },
            ])
            ->orderBy('nama_aset')
            ->paginate(15)
            ->withQueryString();
        $scheduleStatus = app(ScheduleStatusService::class);
        $scheduledIds = $scheduleStatus->currentScheduledAssetIds();

        $hari = $scheduleStatus->currentHariIndonesia();


        $today = now()->format('Y-m-d');

        // Active Loans (Real time)
        $activeCounts = \App\Models\Loan::selectRaw('asset_id, COUNT(*) as total')
            ->where('status', 'Dipinjam')
            ->groupBy('asset_id')
            ->pluck('total', 'asset_id');

        // Approved Bookings for Today
        $bookingCounts = \App\Models\Booking::selectRaw('asset_id, SUM(jumlah) as total')
            ->where('status', 'approved')
            ->whereDate('tanggal', $today)
            ->groupBy('asset_id')
            ->pluck('total', 'asset_id');

        // Merge counts
        $finalCounts = clone $activeCounts;
        foreach ($bookingCounts as $assetId => $count) {
            $finalCounts[$assetId] = ($finalCounts[$assetId] ?? 0) + $count;
        }

        // Pass 'activeCounts' as final counts to view (reusing variable name to avoid view changes)
        // But better to be explicit. Let's pass $finalCounts as $activeCounts to view.
        // View uses $activeCounts to deduct.

        return view('assets.index', [
            'assets' => $assets,
            'scheduledIds' => $scheduledIds,
            'activeCounts' => $finalCounts,
        ]);
    }

    public function create()
    {
        return view('assets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_aset' => ['required', 'string', 'max:255'],
            'jumlah' => ['nullable', 'integer', 'min:1'],
            'kode_unit_prefix' => ['required', 'string', 'max:30'],
        ]);

        $jumlah = (int) ($validated['jumlah'] ?? 1);
        $prefix = Str::slug($validated['kode_unit_prefix'], '-');
        if ($prefix === '') {
            return back()->withErrors([
                'kode_unit_prefix' => 'Kode Unit (Prefix) tidak valid.',
            ])->withInput();
        }

        $codes = [];
        for ($i = 1; $i <= $jumlah; $i++) {
            $codes[] = $prefix . '-' . $i;
        }

        $existingCodes = AssetItem::whereIn('code', $codes)->pluck('code')->all();
        if (!empty($existingCodes)) {
            $preview = array_slice($existingCodes, 0, 5);
            $suffix = count($existingCodes) > 5 ? '…' : '';
            return back()->withErrors([
                'kode_unit_prefix' => 'Kode Unit sudah ada: ' . implode(', ', $preview) . $suffix,
            ])->withInput();
        }

        $asset = DB::transaction(function () use ($validated, $jumlah, $codes) {
            $asset = Asset::create([
                'nama_aset' => $validated['nama_aset'],
                'status' => 'Tersedia',
                'tahun' => null,
                'harga' => null,
                'jumlah' => $jumlah,
            ]);

            foreach ($codes as $code) {
                AssetItem::create([
                    'asset_id' => $asset->id,
                    'code' => $code,
                    'condition' => 'Baik',
                    'is_available' => true,
                ]);
            }

            $this->logStatus($asset, $asset->status);

            return $asset;
        });

        return redirect()->route('assets.index')->with('success', 'Aset berhasil ditambahkan.');
    }

    public function edit(Asset $asset)
    {
        return view('assets.edit', [
            'asset' => $asset,
        ]);
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'nama_aset' => ['required', 'string', 'max:255'],
            'jumlah' => ['nullable', 'integer', 'min:1'],
        ]);

        $asset->update([
            'nama_aset' => $validated['nama_aset'],
            'jumlah' => $validated['jumlah'] ?? 1,
        ]);

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
        $itemsQuery = $asset->items()->orderBy('code');
        if (!request()->boolean('all')) {
            $itemsQuery->where('is_available', true);
        }

        $items = $itemsQuery->get(['id', 'code', 'condition', 'is_available']);
        return response()->json($items);
    }
}
