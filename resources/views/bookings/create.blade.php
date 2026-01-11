@extends('layouts.app')

@section('content')
    <div class="card" style="max-width:720px;">
        <h2 style="margin-top:0;">Ajukan Peminjaman Alat</h2>
        <p style="color:var(--muted); margin-top:-4px;">Pengajuan peminjaman di luar jadwal rutin.</p>

        <form method="POST" action="{{ route('bookings.store') }}">
            @csrf

            <input type="hidden" name="materi_id" value="{{ $materi->id }}">

            <div class="form-group">
                <label>Materi</label>
                <input type="text" value="{{ $materi->nama }}" disabled style="background:#f4f4f4;">
            </div>

            <div class="form-group">
                <label>Pilih Unit Alat</label>
                <select name="asset_item_id" required>
                    <option value="">-- Pilih Unit --</option>
                    @foreach ($assets as $asset)
                        <optgroup label="{{ $asset->nama_aset }}">
                            @forelse($asset->items as $item)
                                <option value="{{ $item->id }}" @selected(old('asset_item_id') == $item->id)>
                                    {{ $item->code }} ({{ $item->condition }})
                                </option>
                            @empty
                                <option disabled>Tidak ada unit tersedia</option>
                            @endforelse
                        </optgroup>
                    @endforeach
                </select>
                <small style="color:var(--muted);">Hanya unit yang tersedia yang ditampilkan.</small>
            </div>

            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal') }}" min="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group">
                    <label>Jam</label>
                    <input type="time" name="waktu" value="{{ old('waktu') }}" required>
                </div>
            </div>

            <div class="actions">
                <button class="btn" type="submit">Kirim Pengajuan</button>
                <a class="btn btn-outline" href="{{ route('materi.index') }}">Batal</a>
            </div>
        </form>
    </div>
@endsection
