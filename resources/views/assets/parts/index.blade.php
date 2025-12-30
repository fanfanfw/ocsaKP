@extends('layouts.app')

@section('content')
    <div class="card">
        <h2 style="margin-top:0;">Part Alat: {{ $asset->nama_aset }}</h2>
        <p style="color:var(--muted);">Kelola part untuk alat ini.</p>

        <form method="POST" action="{{ route('assets.parts.store', $asset) }}" style="margin-bottom:16px;">
            @csrf
            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                <div class="form-group">
                    <label>Nama Part</label>
                    <input type="text" name="nama_part" value="{{ old('nama_part') }}" required>
                </div>
                <div class="form-group">
                    <label>Kondisi</label>
                    <input type="text" name="kondisi" value="{{ old('kondisi') }}">
                </div>
                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" name="jumlah" min="1" value="{{ old('jumlah', 1) }}">
                </div>
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan">{{ old('keterangan') }}</textarea>
            </div>
            <div class="actions">
                <button class="btn" type="submit">Tambah Part</button>
                <a class="btn btn-outline" href="{{ route('assets.index') }}">Kembali</a>
            </div>
        </form>

        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Part</th>
                        <th>Kondisi</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parts as $part)
                        <tr>
                            <td>{{ $part->nama_part }}</td>
                            <td>{{ $part->kondisi ?? '-' }}</td>
                            <td>{{ $part->jumlah }}</td>
                            <td>{{ $part->keterangan ?? '-' }}</td>
                            <td>
                                <div class="actions">
                                    <a class="btn btn-outline" href="{{ route('assets.parts.edit', [$asset, $part]) }}">Edit</a>
                                    <form method="POST" action="{{ route('assets.parts.destroy', [$asset, $part]) }}" onsubmit="return confirm('Hapus part ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; color:var(--muted);">Belum ada part.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $parts->links() }}
        </div>
    </div>
@endsection
