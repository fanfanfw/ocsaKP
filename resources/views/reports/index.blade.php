@extends('layouts.app')

@section('content')
    <div class="card">
        <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:12px;">
            <div>
                <h2 style="margin-top:0;">Laporan &amp; Penggunaan Alat</h2>
                <p style="color:var(--muted);">Ekspor data CSV dan pantau penggunaan alat.</p>
            </div>
            <a class="btn" href="{{ route('loans.create') }}">Tambah Penggunaan</a>
        </div>

        <div style="overflow-x:auto; margin-top:12px;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Alat</th>
                        <th>Pengguna</th>
                        <th>Tanggal Digunakan</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                        <th>Bukti Kembali</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                        <tr>
                            <td>{{ $loan->asset?->nama_aset ?? '-' }}</td>
                            <td>{{ $loan->user?->name ?? $loan->user?->username ?? '-' }}</td>
                            <td>{{ optional($loan->tanggal_pinjam)->format('d/m/Y H:i') }}</td>
                            <td>{{ $loan->tanggal_kembali ? $loan->tanggal_kembali->format('d/m/Y H:i') : '-' }}</td>
                            <td><span class="badge">{{ $loan->status === 'Dipinjam' ? 'Digunakan' : $loan->status }}</span></td>
                            <td>
                                @if($loan->bukti_kembali)
                                    <a class="btn btn-outline" href="{{ asset('storage/bukti_kembali/' . $loan->bukti_kembali) }}" target="_blank">Lihat</a>
                                @else
                                    <span style="color:var(--muted);">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; color:var(--muted);">Belum ada data penggunaan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $loans->links() }}
        </div>
    </div>

    <div class="card" style="margin-top:20px;">
        <h3 style="margin-top:0;">Ekspor Laporan CSV</h3>
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
            <div class="card" style="border:1px solid var(--border);">
                <h4 style="margin-top:0;">Laporan Alat</h4>
                <p style="color:var(--muted);">Berisi daftar alat lengkap.</p>
                <a class="btn" href="{{ route('reports.assets') }}">Unduh CSV</a>
            </div>

            <div class="card" style="border:1px solid var(--border);">
                <h4 style="margin-top:0;">Laporan Penggunaan</h4>
                <form method="GET" action="{{ route('reports.loans') }}">
                    <div class="form-group">
                        <label>Mulai</label>
                        <input type="date" name="start_date">
                    </div>
                    <div class="form-group">
                        <label>Sampai</label>
                        <input type="date" name="end_date">
                    </div>
                    <button class="btn" type="submit">Unduh CSV</button>
                </form>
            </div>

            <div class="card" style="border:1px solid var(--border);">
                <h4 style="margin-top:0;">Laporan Perawatan</h4>
                <form method="GET" action="{{ route('reports.maintenance') }}">
                    <div class="form-group">
                        <label>Mulai</label>
                        <input type="date" name="start_date">
                    </div>
                    <div class="form-group">
                        <label>Sampai</label>
                        <input type="date" name="end_date">
                    </div>
                    <button class="btn" type="submit">Unduh CSV</button>
                </form>
            </div>
        </div>
    </div>
@endsection
