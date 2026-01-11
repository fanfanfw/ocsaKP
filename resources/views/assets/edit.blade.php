@extends('layouts.app')

@section('content')
    <div class="card" style="max-width:720px;">
        <h2 style="margin-top:0;">Edit Alat</h2>
        <form method="POST" action="{{ route('assets.update', $asset) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Nama Alat</label>
                <input type="text" name="nama_aset" value="{{ old('nama_aset', $asset->nama_aset) }}" required>
            </div>
            <div class="form-group">
                <label>Materi Terkait</label>
                <div
                    style="display:flex; flex-wrap:wrap; gap:10px; padding:10px; border:1px solid var(--border); border-radius:10px; background:#fff;">
                    @php
                        $selectedMateri = old('materi_ids', $asset->materi->pluck('id')->toArray());
                    @endphp
                    @forelse($materi_list as $materi)
                        <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                            @php
                                $isLocked = in_array($materi->id, $lockedMateriIds ?? []);
                                $isChecked = in_array($materi->id, $selectedMateri);
                            @endphp

                            @if($isLocked)
                                <input type="hidden" name="materi_ids[]" value="{{ $materi->id }}">
                                <input type="checkbox" checked disabled title="Materi ini sedang digunakan dalam jadwal">
                            @else
                                <input type="checkbox" name="materi_ids[]" value="{{ $materi->id }}" @checked($isChecked)>
                            @endif

                            {{ $materi->nama }}
                            @if($isLocked)
                                <small style="color:var(--muted); font-size:0.8em;">(Terjadwal)</small>
                            @endif
                        </label>
                    @empty
                        <p style="margin:0; color:var(--muted);">Belum ada data materi.</p>
                    @endforelse
                </div>
            </div>
            <div class="form-group">
                <label>Jumlah</label>
                <input type="number" name="jumlah" min="1" value="{{ old('jumlah', $asset->jumlah) }}">
            </div>
            <div class="actions">
                <button class="btn" type="submit">Perbarui</button>
                <a class="btn btn-outline" href="{{ route('assets.index') }}">Batal</a>
            </div>
        </form>
    </div>
@endsection