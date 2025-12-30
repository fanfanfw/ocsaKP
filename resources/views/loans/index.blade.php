@extends('layouts.app')

@section('content')
    <div class="card">
        <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:12px;">
            <div>
                <h2 style="margin:0;">Penggunaan Alat</h2>
                <p style="color:var(--muted); margin:4px 0 0;">Data penggunaan dan pengembalian alat.</p>
            </div>
            <a class="btn" href="{{ route('loans.create') }}">Tambah Penggunaan</a>
        </div>

        <div style="overflow-x:auto; margin-top:12px;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Alat</th>
                        @if(auth()->user()->role === 'admin')
                            <th>Peminjam</th>
                        @endif
                        <th>Tanggal Digunakan</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                        <th>Bukti Kembali</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                        <tr>
                            <td>{{ $loan->asset?->nama_aset ?? '-' }}</td>
                            @if(auth()->user()->role === 'admin')
                                <td>{{ $loan->user?->name ?? $loan->user?->username ?? '-' }}</td>
                            @endif
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
                            <td colspan="{{ auth()->user()->role === 'admin' ? 7 : 6 }}" style="text-align:center; color:var(--muted);">Belum ada data penggunaan.</td>
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
