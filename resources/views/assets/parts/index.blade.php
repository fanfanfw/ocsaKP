@extends('layouts.app')

@section('content')
    <div class="card">
        <h2 style="margin-top:0;">Detail Alat: {{ $asset->nama_aset }}</h2>
        <p style="color:var(--muted);">Daftar unit alat yang belum dipinjam.</p>

        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode Unit</th>
                        <th>Kondisi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td><span class="badge">{{ $item->code }}</span></td>
                            <td>{{ $item->condition ?? '-' }}</td>
                            <td><span class="badge">Tersedia</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align:center; color:var(--muted);">Tidak ada unit tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $items->links() }}
        </div>
        <div class="actions" style="margin-top:12px;">
            <a class="btn btn-outline" href="{{ route('assets.index') }}">Kembali</a>
        </div>
    </div>
@endsection
