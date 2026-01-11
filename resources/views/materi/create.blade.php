@extends('layouts.app')

@section('content')
    <div class="card" style="max-width:720px;">
        <h2 style="margin-top:0;">Tambah Materi</h2>
        <form method="POST" action="{{ route('materi.store') }}">
            @csrf
            <div class="form-group">
                <label>Nama Materi</label>
                <input type="text" name="nama" value="{{ old('nama') }}" required>
            </div>

            <div class="form-group">
                <label>Hubungkan dengan Alat</label>
                <div
                    style="display:flex; flex-wrap:wrap; gap:10px; padding:10px; border:1px solid var(--border); border-radius:10px; background:#fff;">
                    @forelse($assets as $asset)
                        <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                            <input type="checkbox" name="asset_ids[]" value="{{ $asset->id }}"
                                @checked(is_array(old('asset_ids')) && in_array($asset->id, old('asset_ids')))>
                            {{ $asset->nama_aset }}
                        </label>
                    @empty
                        <p style="margin:0; color:var(--muted);">Belum ada data alat. <a
                                href="{{ route('assets.create') }}">Tambah Alat</a> dulu.</p>
                    @endforelse
                </div>
                <small style="color:var(--muted);">Centang alat yang dapat digunakan untuk materi ini.</small>
            </div>
            <div class="actions">
                <button class="btn" type="submit">Simpan</button>
                <a class="btn btn-outline" href="{{ route('materi.index') }}">Batal</a>
            </div>
        </form>
    </div>
@endsection