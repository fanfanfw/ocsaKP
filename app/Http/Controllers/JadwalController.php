<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $query = Jadwal::with(['asset', 'user']);

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

    private function dateFromHari(string $hari): string
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
            return Carbon::now()->format('Y-m-d');
        }

        $today = Carbon::now();
        $target = $map[$hari];
        $date = $today->englishDayOfWeek === $target ? $today : $today->next($target);

        return $date->format('Y-m-d');
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
            'tentor' => User::where('role', 'tentor')->orderBy('name')->get(),
            'materi_list' => \App\Models\Materi::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'materi_id' => ['required', 'exists:materi,id'],
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where('role', 'tentor'),
            ],
            'tanggal' => ['required', 'date'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'jumlah' => ['nullable', 'integer', 'min:1'],
        ]);

        $hari = $this->hariFromDate($validated['tanggal']);
        $jumlah = (int) $request->input('jumlah', 1);

        // Validation: Check availability
        $asset = Asset::findOrFail($validated['asset_id']);
        $currentScheduled = Jadwal::where('asset_id', $asset->id)
            ->where('hari', $hari)
            ->count();

        if (($currentScheduled + $jumlah) > $asset->jumlah) {
            return back()
                ->withErrors(['jumlah' => 'Jumlah melebihi stok yang tersedia. (Total: ' . $asset->jumlah . ', Terjadwal: ' . $currentScheduled . ')'])
                ->withInput();
        }

        for ($i = 0; $i < $jumlah; $i++) {
            Jadwal::create([
                'asset_id' => $validated['asset_id'],
                'materi_id' => $validated['materi_id'],
                'user_id' => $validated['user_id'],
                'hari' => $hari,
                'jam_mulai' => $validated['jam_mulai'],
                'jam_selesai' => $validated['jam_selesai'],
                'keterangan' => $validated['keterangan'] ?? null,
            ]);
        }

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Jadwal $jadwal)
    {
        return view('jadwal.edit', [
            'jadwal' => $jadwal,
            'assets' => Asset::orderBy('nama_aset')->get(),
            'tentor' => User::where('role', 'tentor')->orderBy('name')->get(),
            'materi_list' => \App\Models\Materi::orderBy('nama')->get(),
            'tanggal' => $this->dateFromHari($jadwal->hari),
        ]);
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'materi_id' => ['required', 'exists:materi,id'],
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where('role', 'tentor'),
            ],
            'tanggal' => ['required', 'date'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $hari = $this->hariFromDate($validated['tanggal']);

        $jadwal->update([
            'asset_id' => $validated['asset_id'],
            'materi_id' => $validated['materi_id'],
            'user_id' => $validated['user_id'],
            'hari' => $hari,
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


    public function confirm(Request $request, Jadwal $jadwal)
    {
        $request->validate([
            'asset_item_id' => 'required|exists:asset_items,id',
        ]);

        $item = \App\Models\AssetItem::findOrFail($request->asset_item_id);

        if (!$item->is_available) {
            return back()->with('error', 'Alat pilih sudah tidak tersedia.');
        }

        // Update Jadwal
        $jadwal->update([
            'status' => 'Diterima',
            'asset_item_id' => $item->id
        ]);

        // Create Loan Record
        \App\Models\Loan::create([
            'user_id' => auth()->id(),
            'asset_id' => $jadwal->asset_id,
            'asset_item_id' => $item->id,
            'tanggal_pinjam' => now(),
            'status' => 'Dipinjam'
        ]);

        // Mark item as unavailable
        $item->update(['is_available' => false]);

        return back()->with('success', 'Alat berhasil diterima.');
    }
}
