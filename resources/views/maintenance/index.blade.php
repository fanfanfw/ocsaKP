@extends('layouts.app')

@section('content')
    <div class="card">
        <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:12px;">
            <div>
                <h2 style="margin:0;">Perawatan Aset</h2>
                <p style="color:var(--muted); margin:4px 0 0;">Catatan kerusakan dan perbaikan aset.</p>
            </div>
            @if(auth()->user()->role === 'admin')
                <a class="btn" href="{{ route('maintenance.create') }}">Tambah Perawatan</a>
            @endif
        </div>

        <div style="overflow-x:auto; margin-top:12px;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Aset</th>
                        <th>Tanggal</th>
                        <th>Deskripsi</th>
                        <th>Part</th>
                        <th>Tingkat</th>
                        <th>Tanggal Selesai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>{{ $item->asset?->nama_aset ?? '-' }}</td>
                            <td>{{ optional($item->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ $item->deskripsi }}</td>
                            <td>{{ $item->part ?? '-' }}</td>
                            <td>{{ $item->tingkat ?? '-' }}</td>
                            <td>{{ $item->tanggal_selesai ? $item->tanggal_selesai->format('d/m/Y') : '-' }}</td>
                            <td>
                                @if(auth()->user()->role === 'admin')
                                    <div class="actions">
                                        <a class="btn btn-outline" href="{{ route('maintenance.edit', $item) }}">Edit</a>
                                        <form method="POST" action="{{ route('maintenance.destroy', $item) }}" onsubmit="return confirm('Hapus data perawatan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                @else
                                    <span style="color:var(--muted);">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; color:var(--muted);">Belum ada data perawatan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $items->links() }}
        </div>
    </div>
@endsection
