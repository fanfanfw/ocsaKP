@extends('layouts.app')

@section('content')
    <div class="card">
        <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:12px;">
            <div>
                <h2 style="margin:0;">Data Alat</h2>
                <p style="color:var(--muted); margin:4px 0 0;">Kelola inventaris alat ajar.</p>
            </div>
            @if(auth()->user()->role === 'admin')
                <a class="btn" href="{{ route('assets.create') }}">Tambah Alat</a>
            @endif
        </div>

        <form method="GET" action="{{ route('assets.index') }}" style="margin:16px 0 12px;">
            <div class="actions">
                <input type="text" name="q" placeholder="Cari nama/kategori/status..." value="{{ request('q') }}">
                <button class="btn btn-outline" type="submit">Cari</button>
                @if(request('q'))
                    <a class="btn btn-outline" href="{{ route('assets.index') }}">Reset</a>
                @endif
            </div>
        </form>

        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Alat</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                        @php
                            $activeLoans = $activeCounts[$asset->id] ?? 0;
                            $available = max($asset->jumlah - $activeLoans, 0);
                            $isScheduled = in_array($asset->id, $scheduledIds ?? [], true);
                        @endphp
                        <tr>
                            <td>{{ $asset->nama_aset }}</td>
                            <td>{{ $asset->kategori ?? '-' }}</td>
                            <td>
                                @if($isScheduled)
                                    <span class="badge">Terjadwal</span>
                                    @if($available > 0)
                                        <span class="badge">Tersedia</span>
                                    @endif
                                @elseif($available <= 0)
                                    <span class="badge">Digunakan</span>
                                @else
                                    <span class="badge">Tersedia</span>
                                @endif
                            </td>
                            <td>{{ $available }}</td>
                            <td>
                                @if(auth()->user()->role === 'admin')
                                    <div class="actions">
                                        <a class="btn btn-outline" href="{{ route('assets.parts.index', $asset) }}">Part</a>
                                        <a class="btn btn-outline" href="{{ route('assets.edit', $asset) }}">Edit</a>
                                        <form method="POST" action="{{ route('assets.destroy', $asset) }}" onsubmit="return confirm('Hapus alat ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                @else
                                    @if($available > 0)
                                        <a class="btn btn-outline" href="{{ route('loans.create', ['asset_id' => $asset->id]) }}">Gunakan Alat</a>
                                    @else
                                        <span style="color:var(--muted);">Tidak tersedia</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; color:var(--muted);">Belum ada data alat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $assets->links() }}
        </div>
    </div>
@endsection
