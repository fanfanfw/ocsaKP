@extends('layouts.app')

@section('content')
    <div class="card">
        <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:12px;">
            <div>
                <h2 style="margin:0;">Jadwal Penggunaan</h2>
                <p style="color:var(--muted); margin:4px 0 0;">Informasi jadwal alat berdasarkan hari.</p>
            </div>
            @if(auth()->user()->role === 'admin')
                <a class="btn" href="{{ route('jadwal.create') }}">Tambah Jadwal</a>
            @endif
        </div>

        <form method="GET" action="{{ route('jadwal.index') }}" style="margin:16px 0 12px;">
            <div class="actions">
                <select name="hari">
                    <option value="">Semua Hari</option>
                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $hari)
                        <option value="{{ $hari }}" @selected(request('hari') === $hari)>{{ $hari }}</option>
                    @endforeach
                </select>
                <button class="btn btn-outline" type="submit">Filter</button>
                @if(request('hari'))
                    <a class="btn btn-outline" href="{{ route('jadwal.index') }}">Reset</a>
                @endif
            </div>
        </form>

        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Alat</th>
                        <th>Tentor</th>
                        <th>Materi</th>
                        <th>Hari &amp; Tanggal</th>
                        <th>Jam</th>
                        <th>Keterangan</th>
                        @if(auth()->user()->role === 'admin')
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwal as $item)
                        <tr>
                            <td>{{ $item->asset?->nama_aset ?? '-' }}</td>
                            <td>{{ $item->user?->name ?? $item->user?->username ?? '-' }}</td>
                            <td>{{ $item->asset?->kategori ?? '-' }}</td>
                            <td>{{ $item->hari_tanggal ?? $item->hari }}</td>
                            <td>{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</td>
                            <td>{{ $item->keterangan ?? '-' }}</td>
                            @if(auth()->user()->role === 'admin')
                                <td>
                                    <div class="actions">
                                        <a class="btn btn-outline" href="{{ route('jadwal.edit', $item) }}">Edit</a>
                                        <form method="POST" action="{{ route('jadwal.destroy', $item) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'admin' ? 7 : 6 }}" style="text-align:center; color:var(--muted);">Belum ada jadwal.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $jadwal->links() }}
        </div>
    </div>
@endsection
