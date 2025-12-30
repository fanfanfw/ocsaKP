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
                <label>Kategori</label>
                <input type="text" name="kategori" value="{{ old('kategori', $asset->kategori) }}">
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
