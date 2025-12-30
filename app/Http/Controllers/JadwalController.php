<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $query = Jadwal::with('asset');

        if ($request->filled('hari')) {
            $query->where('hari', $request->input('hari'));
        }

        $jadwal = $query->orderBy('hari')->orderBy('jam_mulai')->paginate(15)->withQueryString();

        $jadwal->getCollection()->transform(function ($item) {
            $item->hari_tanggal = $this->formatHariTanggal($item->hari);
            return $item;
        });

        return view('jadwal.index', [
            'jadwal' => $jadwal,
        ]);
    }

    private function formatHariTanggal(string $hari): string
    {
        $map = [
            'Senin' => 'Monday',
            'Selasa' => 'Tuesday',
            'Rabu' => 'Wednesday',
            'Kamis' => 'Thursday',
            'Jumat' => 'Friday',
            'Sabtu' => 'Saturday',
            'Minggu' => 'Sunday',
        ];

        if (!isset($map[$hari])) {
            return $hari;
        }

        $today = Carbon::now();
        $target = $map[$hari];
        $date = $today->englishDayOfWeek === $target ? $today : $today->next($target);

        return $hari . ', ' . $date->format('d/m/Y');
    }

    private function hariFromDate(string $tanggal): string
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

        $englishDay = Carbon::parse($tanggal)->englishDayOfWeek;

        return $map[$englishDay] ?? 'Senin';
    }

    public function create()
    {
        return view('jadwal.create', [
            'assets' => Asset::orderBy('nama_aset')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'tanggal' => ['required', 'date'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $hari = $this->hariFromDate($validated['tanggal']);

        Jadwal::create([
            'asset_id' => $validated['asset_id'],
            'hari' => $hari,
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Jadwal $jadwal)
    {
        return view('jadwal.edit', [
            'jadwal' => $jadwal,
            'assets' => Asset::orderBy('nama_aset')->get(),
        ]);
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $jadwal->update([
            'asset_id' => $validated['asset_id'],
            'hari' => $validated['hari'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}
