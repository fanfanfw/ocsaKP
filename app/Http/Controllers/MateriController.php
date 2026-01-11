<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use Illuminate\Http\Request;

class MateriController extends Controller
{
    /**
     * Display a listing of materi.
     */
    public function index(Request $request)
    {
        $query = Materi::query();

        if ($request->filled('q')) {
            $query->where('nama', 'like', '%' . $request->input('q') . '%');
        }

        $materi = $query->withCount(['assets', 'jadwal'])->orderBy('nama')->paginate(15)->withQueryString();

        return view('materi.index', [
            'materi' => $materi,
        ]);
    }

    /**
     * Show the form for creating a new materi.
     */
    public function create()
    {
        return view('materi.create', [
            'assets' => \App\Models\Asset::orderBy('nama_aset')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:materi,nama'],
            'asset_ids' => ['nullable', 'array'],
            'asset_ids.*' => ['exists:assets,id'],
        ]);

        $materi = Materi::create([
            'nama' => $validated['nama'],
        ]);

        if (!empty($validated['asset_ids'])) {
            $materi->assets()->sync($validated['asset_ids']);
        }

        return redirect()->route('materi.index')->with('success', 'Materi berhasil ditambahkan.');
    }

    public function edit(Materi $materi)
    {
        $lockedAssetIds = \App\Models\Jadwal::where('materi_id', $materi->id)
            ->distinct()
            ->pluck('asset_id')
            ->toArray();

        return view('materi.edit', [
            'materi' => $materi,
            'assets' => \App\Models\Asset::orderBy('nama_aset')->get(),
            'lockedAssetIds' => $lockedAssetIds,
        ]);
    }

    public function update(Request $request, Materi $materi)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:materi,nama,' . $materi->id],
            'asset_ids' => ['nullable', 'array'],
            'asset_ids.*' => ['exists:assets,id'],
        ]);

        $materi->update([
            'nama' => $validated['nama'],
        ]);

        if (isset($validated['asset_ids'])) {
            $materi->assets()->sync($validated['asset_ids']);
        } else {
            $materi->assets()->sync($validated['asset_ids'] ?? []);
        }

        return redirect()->route('materi.index')->with('success', 'Materi berhasil diperbarui.');
    }

    /**
     * Remove the specified materi.
     */
    public function destroy(Materi $materi)
    {


        // Check if materi is connected to any jadwal
        if ($materi->jadwal()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Materi tidak dapat dihapus karena masih terhubung dengan jadwal.',
            ], 400);
        }

        $materi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Materi berhasil dihapus.',
        ]);
    }

    /**
     * Get assets by materi (API endpoint for AJAX).
     */
    public function getAssets(Materi $materi)
    {
        $assets = $materi->assets()->orderBy('nama_aset')->get(['assets.id', 'nama_aset']);

        return response()->json($assets);
    }
}
