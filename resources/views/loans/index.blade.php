@extends('layouts.app')

@section('content')
    <div class="card">
        <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:12px;">
            <div>
                <h2 style="margin:0;">Pengembalian Alat</h2>
                <p style="color:var(--muted); margin:4px 0 0;">Data pengembalian alat yang digunakan.</p>
            </div>
        </div>

        <div style="overflow-x:auto; margin-top:12px;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Alat</th>
                        <th>Tanggal Digunakan</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                        <tr>
                            <td>{{ $loan->asset?->nama_aset ?? '-' }}</td>
                            <td>{{ optional($loan->tanggal_pinjam)->format('d/m/Y H:i') }}</td>
                            <td>{{ $loan->tanggal_kembali ? $loan->tanggal_kembali->format('d/m/Y H:i') : '-' }}</td>
                            <td><span class="badge">{{ $loan->status === 'Dipinjam' ? 'Digunakan' : $loan->status }}</span></td>
                            <td>
                                @if($loan->status === 'Dipinjam')
                                    <a class="btn btn-outline" href="{{ route('loans.return', $loan) }}">Pengembalian</a>
                                @else
                                    <span style="color:var(--muted);">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; color:var(--muted);">Belum ada data pengembalian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $loans->links() }}
        </div>
    </div>
@endsection
