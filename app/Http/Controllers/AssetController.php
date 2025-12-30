<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetStatusLog;
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

        return view('assets.index', [
            'assets' => $assets,
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
            'kategori' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'max:50'],
            'tahun' => ['nullable', 'integer'],
            'harga' => ['nullable', 'integer', 'min:0'],
            'jumlah' => ['nullable', 'integer', 'min:1'],
        ]);

        $asset = Asset::create([
            'nama_aset' => $validated['nama_aset'],
            'kategori' => $validated['kategori'] ?? null,
            'status' => $validated['status'] ?? 'Tersedia',
            'tahun' => $validated['tahun'] ?? null,
            'harga' => $validated['harga'] ?? null,
            'jumlah' => $validated['jumlah'] ?? 1,
        ]);

        $this->logStatus($asset, $asset->status);

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
            'kategori' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'max:50'],
            'tahun' => ['nullable', 'integer'],
            'harga' => ['nullable', 'integer', 'min:0'],
            'jumlah' => ['nullable', 'integer', 'min:1'],
        ]);

        $oldStatus = $asset->status;

        $asset->update([
            'nama_aset' => $validated['nama_aset'],
            'kategori' => $validated['kategori'] ?? null,
            'status' => $validated['status'] ?? 'Tersedia',
            'tahun' => $validated['tahun'] ?? null,
            'harga' => $validated['harga'] ?? null,
            'jumlah' => $validated['jumlah'] ?? 1,
        ]);

        if ($oldStatus !== $asset->status) {
            $this->logStatus($asset, $asset->status);
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
}
