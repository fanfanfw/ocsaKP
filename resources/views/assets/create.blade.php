@extends('layouts.app')

@section('content')
    <div class="card" style="max-width:720px;">
        <h2 style="margin-top:0;">Tambah Alat</h2>
        <form method="POST" action="{{ route('assets.store') }}">
            @csrf
            <div class="form-group">
                <label>Nama Alat</label>
                <input type="text" name="nama_aset" value="{{ old('nama_aset') }}" required>
            </div>

            <div class="form-group">
                <label>Jumlah</label>
                <input type="number" name="jumlah" min="1" value="{{ old('jumlah', 1) }}">
            </div>
            <div class="form-group">
                <label>Kode Unit (Prefix)</label>
                <input type="text" name="kode_unit_prefix" value="{{ old('kode_unit_prefix') }}" placeholder="Contoh: rbt" required>
                <small style="color:var(--muted);">Otomatis dibuat menjadi rbt-1, rbt-2, dst sesuai jumlah.</small>
            </div>
            <div class="actions">
                <button class="btn" type="submit">Simpan</button>
                <a class="btn btn-outline" href="{{ route('assets.index') }}">Batal</a>
            </div>
        </form>
    </div>
@endsection
