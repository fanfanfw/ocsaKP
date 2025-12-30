<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Maintenance;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        $items = Maintenance::with('asset')->orderByDesc('tanggal')->paginate(15);

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
            'deskripsi' => ['required', 'string'],
            'tanggal' => ['required', 'date'],
            'part' => ['nullable', 'string', 'max:100'],
            'jenis_kerusakan' => ['nullable', 'string', 'max:100'],
            'tingkat' => ['nullable', 'in:Ringan,Sedang,Berat'],
            'tindakan' => ['nullable', 'string'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal'],
        ]);

        Maintenance::create($validated);

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
            'deskripsi' => ['required', 'string'],
            'tanggal' => ['required', 'date'],
            'part' => ['nullable', 'string', 'max:100'],
            'jenis_kerusakan' => ['nullable', 'string', 'max:100'],
            'tingkat' => ['nullable', 'in:Ringan,Sedang,Berat'],
            'tindakan' => ['nullable', 'string'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal'],
        ]);

        $maintenance->update($validated);

        return redirect()->route('maintenance.index')->with('success', 'Perawatan berhasil diperbarui.');
    }

    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();

        return redirect()->route('maintenance.index')->with('success', 'Perawatan berhasil dihapus.');
    }
}
