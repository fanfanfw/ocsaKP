<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetItem;
use App\Models\AssetPart;
use Illuminate\Http\Request;

class AssetPartController extends Controller
{
    public function index(Asset $asset)
    {
        $items = $asset->items()
            ->where('is_available', true)
            ->orderBy('code')
            ->paginate(15);

        return view('assets.parts.index', [
            'asset' => $asset,
            'items' => $items,
        ]);
    }

    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'nama_part' => ['required', 'string', 'max:100'],
            'kode_unit' => [
                'required',
                'string',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail) use ($asset) {
                    $value = trim((string) $value);
                    $existingItem = AssetItem::where('code', $value)->first();
                    if ($existingItem && $existingItem->asset_id !== $asset->id) {
                        $fail('Kode Unit sudah digunakan pada alat lain.');
                        return;
                    }

                    if ($existingItem && AssetPart::where('asset_item_id', $existingItem->id)->exists()) {
                        $fail('Kode Unit sudah digunakan pada data part lain.');
                    }
                },
            ],
            'kondisi' => ['nullable', 'string', 'max:50'],
            'jumlah' => ['nullable', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $kodeUnit = trim((string) $validated['kode_unit']);
        $assetItem = AssetItem::where('code', $kodeUnit)->first();
        if (!$assetItem) {
            $assetItem = AssetItem::create([
                'asset_id' => $asset->id,
                'code' => $kodeUnit,
                'condition' => 'Baik',
                'is_available' => true,
            ]);
        }

        $asset->parts()->create([
            'nama_part' => $validated['nama_part'],
            'asset_item_id' => $assetItem->id,
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

        $part->load('assetItem');

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
            'kode_unit' => [
                'required',
                'string',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail) use ($asset, $part) {
                    $value = trim((string) $value);
                    $existingItem = AssetItem::where('code', $value)->first();
                    if ($existingItem && $existingItem->asset_id !== $asset->id) {
                        $fail('Kode Unit sudah digunakan pada alat lain.');
                        return;
                    }

                    if (
                        $existingItem
                        && AssetPart::where('asset_item_id', $existingItem->id)
                            ->where('id', '!=', $part->id)
                            ->exists()
                    ) {
                        $fail('Kode Unit sudah digunakan pada data part lain.');
                    }
                },
            ],
            'kondisi' => ['nullable', 'string', 'max:50'],
            'jumlah' => ['nullable', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $kodeUnit = trim((string) $validated['kode_unit']);
        $assetItem = AssetItem::where('code', $kodeUnit)->first();
        if (!$assetItem) {
            $assetItem = AssetItem::create([
                'asset_id' => $asset->id,
                'code' => $kodeUnit,
                'condition' => 'Baik',
                'is_available' => true,
            ]);
        }

        $part->update([
            'nama_part' => $validated['nama_part'],
            'asset_item_id' => $assetItem->id,
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
