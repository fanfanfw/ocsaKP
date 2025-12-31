@extends('layouts.app')

@section('content')
    <div class="card" style="max-width:720px;">
        <h2 style="margin-top:0;">Tambah Jadwal</h2>
        <form method="POST" action="{{ route('jadwal.store') }}">
            @csrf
            <div class="form-group">
                <label>Alat</label>
                <select name="asset_id" required>
                    <option value="">Pilih Alat</option>
                    @foreach($assets as $asset)
                        <option value="{{ $asset->id }}" @selected(old('asset_id') == $asset->id)>{{ $asset->nama_aset }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Tentor</label>
                <select name="user_id" required>
                    <option value="">Pilih Tentor</option>
                    @foreach($tentor as $user)
                        <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                            {{ $user->name ?? $user->username }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="tanggal" value="{{ old('tanggal') }}" required>
            </div>
            <div class="form-group">
                <label>Jam Mulai</label>
                <input type="text" name="jam_mulai" value="{{ old('jam_mulai') }}" required placeholder="HH:MM" pattern="^([01]\d|2[0-3]):[0-5]\d$" inputmode="numeric">
            </div>
            <div class="form-group">
                <label>Jam Selesai</label>
                <input type="text" name="jam_selesai" value="{{ old('jam_selesai') }}" required placeholder="HH:MM" pattern="^([01]\d|2[0-3]):[0-5]\d$" inputmode="numeric">
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <input type="text" name="keterangan" value="{{ old('keterangan') }}">
            </div>
            <div class="actions">
                <button class="btn" type="submit">Simpan</button>
                <a class="btn btn-outline" href="{{ route('jadwal.index') }}">Batal</a>
            </div>
        </form>
    </div>
@endsection
