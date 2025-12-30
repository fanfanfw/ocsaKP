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
                <label>Kategori</label>
                <input type="text" name="kategori" value="{{ old('kategori') }}">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    @foreach(['Tersedia','Terjadwal','Dipinjam'] as $status)
                        <option value="{{ $status }}" @selected(old('status', 'Tersedia') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Tahun</label>
                <input type="number" name="tahun" value="{{ old('tahun') }}">
            </div>
            <div class="form-group">
                <label>Harga</label>
                <input type="number" name="harga" value="{{ old('harga') }}">
            </div>
            <div class="form-group">
                <label>Jumlah</label>
                <input type="number" name="jumlah" min="1" value="{{ old('jumlah', 1) }}">
            </div>
            <div class="actions">
                <button class="btn" type="submit">Simpan</button>
                <a class="btn btn-outline" href="{{ route('assets.index') }}">Batal</a>
            </div>
        </form>
    </div>
@endsection
