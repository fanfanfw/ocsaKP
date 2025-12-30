@extends('layouts.app')

@section('content')
    <div class="card" style="max-width:780px;">
        <h2 style="margin-top:0;">Tambah Perawatan</h2>
        <form method="POST" action="{{ route('maintenance.store') }}">
            @csrf
            <div class="form-group">
                <label>Aset</label>
                <select name="asset_id" required>
                    <option value="">Pilih Aset</option>
                    @foreach($assets as $asset)
                        <option value="{{ $asset->id }}" @selected(old('asset_id') == $asset->id)>{{ $asset->nama_aset }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" required>{{ old('deskripsi') }}</textarea>
            </div>
            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal') }}" required>
                </div>
                <div class="form-group">
                    <label>Part</label>
                    <input type="text" name="part" value="{{ old('part') }}">
                </div>
                <div class="form-group">
                    <label>Jenis Kerusakan</label>
                    <input type="text" name="jenis_kerusakan" value="{{ old('jenis_kerusakan') }}">
                </div>
                <div class="form-group">
                    <label>Tingkat</label>
                    <select name="tingkat">
                        <option value="">Pilih Tingkat</option>
                        @foreach(['Ringan','Sedang','Berat'] as $level)
                            <option value="{{ $level }}" @selected(old('tingkat') === $level)>{{ $level }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Tindakan</label>
                <textarea name="tindakan">{{ old('tindakan') }}</textarea>
            </div>
            <div class="form-group">
                <label>Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}">
            </div>
            <div class="actions">
                <button class="btn" type="submit">Simpan</button>
                <a class="btn btn-outline" href="{{ route('maintenance.index') }}">Batal</a>
            </div>
        </form>
    </div>
@endsection
