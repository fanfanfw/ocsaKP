<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetPart;
use Illuminate\Http\Request;

class AssetPartController extends Controller
{
    public function index(Asset $asset)
    {
        $parts = $asset->parts()->orderBy('nama_part')->paginate(15);

        return view('assets.parts.index', [
            'asset' => $asset,
            'parts' => $parts,
        ]);
    }

    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'nama_part' => ['required', 'string', 'max:100'],
            'kondisi' => ['nullable', 'string', 'max:50'],
            'jumlah' => ['nullable', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $asset->parts()->create([
            'nama_part' => $validated['nama_part'],
            'kondisi' => $validated['kondisi'] ?? null,
            'jumlah' => $validated['jumlah'] ?? 1,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return redirect()->route('assets.parts.index', $asset)->with('success', 'Part aset berhasil ditambahkan.');
    }

    public function edit(Asset $asset, AssetPart $part)
    {
        if ($part->asset_id !== $asset->id) {
            abort(404);
        }

        return view('assets.parts.edit', [
            'asset' => $asset,
            'part' => $part,
        ]);
    }

    public function update(Request $request, Asset $asset, AssetPart $part)
    {
        if ($part->asset_id !== $asset->id) {
            abort(404);
        }

        $validated = $request->validate([
            'nama_part' => ['required', 'string', 'max:100'],
            'kondisi' => ['nullable', 'string', 'max:50'],
            'jumlah' => ['nullable', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $part->update([
            'nama_part' => $validated['nama_part'],
            'kondisi' => $validated['kondisi'] ?? null,
            'jumlah' => $validated['jumlah'] ?? 1,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return redirect()->route('assets.parts.index', $asset)->with('success', 'Part aset berhasil diperbarui.');
    }

    public function destroy(Asset $asset, AssetPart $part)
    {
        if ($part->asset_id !== $asset->id) {
            abort(404);
        }

        $part->delete();

        return redirect()->route('assets.parts.index', $asset)->with('success', 'Part aset berhasil dihapus.');
    }
}
