@extends('layouts.app')

@section('content')
    <div class="card" style="max-width:720px;">
        <h2 style="margin-top:0;">Edit Materi</h2>
        <form method="POST" action="{{ route('materi.update', $materi) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Nama Materi</label>
                <input type="text" name="nama" value="{{ old('nama', $materi->nama) }}" required>
            </div>

            <div class="form-group">
                <label>Hubungkan dengan Alat</label>
                <div
                    style="display:flex; flex-wrap:wrap; gap:10px; padding:10px; border:1px solid var(--border); border-radius:10px; background:#fff;">
                    @php
                        $selectedAssets = old('asset_ids', $materi->assets->pluck('id')->toArray());
                    @endphp
                    @forelse($assets as $asset)
                        <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                            @php
                                $isLocked = in_array($asset->id, $lockedAssetIds ?? []);
                                $isChecked = in_array($asset->id, $selectedAssets);
                            @endphp

                            @if($isLocked)
                                <input type="hidden" name="asset_ids[]" value="{{ $asset->id }}">
                                <input type="checkbox" checked disabled title="Alat ini sedang dijadwalkan dengan materi ini">
                            @else
                                <input type="checkbox" name="asset_ids[]" value="{{ $asset->id }}" @checked($isChecked)>
                            @endif

                            {{ $asset->nama_aset }}
                            @if($isLocked)
                                <small style="color:var(--muted); font-size:0.8em;">(Terjadwal)</small>
                            @endif
                        </label>
                    @empty
                        <p style="margin:0; color:var(--muted);">Belum ada data alat.</p>
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