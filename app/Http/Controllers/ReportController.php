<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Loan;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index()
    {
        $loans = Loan::with(['asset', 'user'])->orderByDesc('tanggal_pinjam')->paginate(15);

        return view('reports.index', [
            'loans' => $loans,
        ]);
    }

    public function exportAssets(): StreamedResponse
    {
        $filename = 'laporan-aset-' . now()->format('YmdHis') . '.csv';
        $headers = $this->csvHeaders($filename);

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'ID',
                'Nama Aset',
                'Kategori',
                'Status',
                'Tahun',
                'Harga',
                'Jumlah',
            ]);

            Asset::orderBy('nama_aset')->chunk(500, function ($assets) use ($handle) {
                foreach ($assets as $asset) {
                    fputcsv($handle, [
                        $asset->id,
                        $asset->nama_aset,
                        $asset->kategori,
                        $asset->status === 'Dipinjam' ? 'Digunakan' : $asset->status,
                        $asset->tahun,
                        $asset->harga,
                        $asset->jumlah,
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportLoans(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $filename = 'laporan-penggunaan-' . now()->format('YmdHis') . '.csv';
        $headers = $this->csvHeaders($filename);

        $callback = function () use ($validated) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'ID',
                'Aset',
                'Peminjam',
                'Tanggal Pinjam',
                'Tanggal Kembali',
                'Status',
            ]);

            $query = Loan::with(['asset', 'user'])->orderByDesc('tanggal_pinjam');

            if (!empty($validated['start_date'])) {
                $query->whereDate('tanggal_pinjam', '>=', $validated['start_date']);
            }

            if (!empty($validated['end_date'])) {
                $query->whereDate('tanggal_pinjam', '<=', $validated['end_date']);
            }

            $query->chunk(500, function ($loans) use ($handle) {
                foreach ($loans as $loan) {
                    fputcsv($handle, [
                        $loan->id,
                        $loan->asset?->nama_aset,
                        $loan->user?->name ?? $loan->user?->username,
                        optional($loan->tanggal_pinjam)->format('Y-m-d H:i'),
                        optional($loan->tanggal_kembali)->format('Y-m-d H:i'),
                        $loan->status === 'Dipinjam' ? 'Digunakan' : $loan->status,
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportMaintenance(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $filename = 'laporan-perawatan-' . now()->format('YmdHis') . '.csv';
        $headers = $this->csvHeaders($filename);

        $callback = function () use ($validated) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'ID',
                'Aset',
                'Tanggal',
                'Deskripsi',
                'Part',
                'Jenis Kerusakan',
                'Tingkat',
                'Tindakan',
                'Tanggal Selesai',
            ]);

            $query = Maintenance::with('asset')->orderByDesc('tanggal');

            if (!empty($validated['start_date'])) {
                $query->whereDate('tanggal', '>=', $validated['start_date']);
            }

            if (!empty($validated['end_date'])) {
                $query->whereDate('tanggal', '<=', $validated['end_date']);
            }

            $query->chunk(500, function ($items) use ($handle) {
                foreach ($items as $item) {
                    fputcsv($handle, [
                        $item->id,
                        $item->asset?->nama_aset,
                        optional($item->tanggal)->format('Y-m-d'),
                        $item->deskripsi,
                        $item->part,
                        $item->jenis_kerusakan,
                        $item->tingkat,
                        $item->tindakan,
                        optional($item->tanggal_selesai)->format('Y-m-d'),
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function csvHeaders(string $filename): array
    {
        return [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
    }
}
