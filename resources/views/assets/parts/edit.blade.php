@extends('layouts.app')

@section('content')
    <div class="card" style="max-width:720px;">
        <h2 style="margin-top:0;">Edit Part Alat</h2>
        <p style="color:var(--muted);">Alat: {{ $asset->nama_aset }}</p>
        <form method="POST" action="{{ route('assets.parts.update', [$asset, $part]) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Nama Part</label>
                <input type="text" name="nama_part" value="{{ old('nama_part', $part->nama_part) }}" required>
            </div>
            <div class="form-group">
                <label>Kode Unit</label>
                <input type="text" name="kode_unit" value="{{ old('kode_unit', $part->assetItem->code ?? '') }}" required>
            </div>
            <div class="form-group">
                <label>Kondisi</label>
                <input type="text" name="kondisi" value="{{ old('kondisi', $part->kondisi) }}">
            </div>
            <div class="form-group">
                <label>Jumlah</label>
                <input type="number" name="jumlah" min="1" value="{{ old('jumlah', $part->jumlah) }}">
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan">{{ old('keterangan', $part->keterangan) }}</textarea>
            </div>
            <div class="actions">
                <button class="btn" type="submit">Perbarui</button>
                <a class="btn btn-outline" href="{{ route('assets.parts.index', $asset) }}">Batal</a>
            </div>
        </form>
    </div>
@endsection
