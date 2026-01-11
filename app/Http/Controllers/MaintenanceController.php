<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetItem;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaintenanceController extends Controller
{
    public function index()
    {
        $items = Maintenance::with(['asset', 'assetItem'])->orderByDesc('tanggal')->paginate(15);

        return view('maintenance.index', [
            'items' => $items,
        ]);
    }

    public function create()
    {
        return view('maintenance.create', [
            'assets' => Asset::orderBy('nama_aset')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'asset_item_id' => [
                'required',
                'integer',
                Rule::exists('asset_items', 'id')->where(function ($q) use ($request) {
                    $q->where('asset_id', $request->input('asset_id'))
                        ->where('is_available', true);
                }),
            ],
            'deskripsi' => ['required', 'string'],
            'tanggal' => ['required', 'date'],
            'jenis_kerusakan' => ['nullable', 'string', 'max:100'],
            'tingkat' => ['nullable', 'in:Ringan,Sedang,Berat'],
            'tindakan' => ['nullable', 'string'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal'],
        ]);

        $item = AssetItem::findOrFail($validated['asset_item_id']);
        if (!$item->is_available) {
            return back()->withErrors([
                'asset_item_id' => 'Kode Unit ini sedang tidak tersedia.',
            ])->withInput();
        }

        Maintenance::create([
            ...$validated,
            'part' => null,
        ]);

        $item->update(['is_available' => false]);

        return redirect()->route('maintenance.index')->with('success', 'Perawatan berhasil ditambahkan.');
    }

    public function edit(Maintenance $maintenance)
    {
        return view('maintenance.edit', [
            'maintenance' => $maintenance,
            'assets' => Asset::orderBy('nama_aset')->get(),
        ]);
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'asset_item_id' => [
                'required',
                'integer',
                Rule::exists('asset_items', 'id')->where(function ($q) use ($request) {
                    $q->where('asset_id', $request->input('asset_id'));
                }),
            ],
            'deskripsi' => ['required', 'string'],
            'tanggal' => ['required', 'date'],
            'jenis_kerusakan' => ['nullable', 'string', 'max:100'],
            'tingkat' => ['nullable', 'in:Ringan,Sedang,Berat'],
            'tindakan' => ['nullable', 'string'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal'],
        ]);

        $previousItemId = $maintenance->asset_item_id;
        $maintenance->update([
            ...$validated,
            'part' => null,
        ]);

        if ($previousItemId && $previousItemId !== (int) $validated['asset_item_id']) {
            $shouldReleaseOld = !Maintenance::where('asset_item_id', $previousItemId)
                ->whereNull('tanggal_selesai')
                ->where('id', '!=', $maintenance->id)
                ->exists();
            if ($shouldReleaseOld) {
                $hasActiveLoan = \App\Models\Loan::where('asset_item_id', $previousItemId)
                    ->where('status', 'Dipinjam')
                    ->exists();
                if (!$hasActiveLoan) {
                    AssetItem::whereKey($previousItemId)->update(['is_available' => true]);
                }
            }
        }

        $currentItem = AssetItem::findOrFail($validated['asset_item_id']);
        if (empty($validated['tanggal_selesai'])) {
            $currentItem->update(['is_available' => false]);
        } else {
            $hasActiveLoan = \App\Models\Loan::where('asset_item_id', $currentItem->id)
                ->where('status', 'Dipinjam')
                ->exists();
            if (!$hasActiveLoan) {
                $currentItem->update(['is_available' => true]);
            }
        }

        return redirect()->route('maintenance.index')->with('success', 'Perawatan berhasil diperbarui.');
    }

    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();

        return redirect()->route('maintenance.index')->with('success', 'Perawatan berhasil dihapus.');
    }
}
