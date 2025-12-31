@extends('layouts.app')

@section('content')
    <div class="card" style="max-width:720px;">
        <h2 style="margin-top:0;">Edit Jadwal</h2>
        <form method="POST" action="{{ route('jadwal.update', $jadwal) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Alat</label>
                <select name="asset_id" required>
                    @foreach($assets as $asset)
                        <option value="{{ $asset->id }}" @selected(old('asset_id', $jadwal->asset_id) == $asset->id)>{{ $asset->nama_aset }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Tentor</label>
                <select name="user_id" required>
                    @foreach($tentor as $user)
                        <option value="{{ $user->id }}" @selected(old('user_id', $jadwal->user_id) == $user->id)>
                            {{ $user->name ?? $user->username }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', $tanggal ?? '') }}" required>
            </div>
            <div class="form-group">
                <label>Jam Mulai</label>
                <input type="text" name="jam_mulai" value="{{ old('jam_mulai', substr($jadwal->jam_mulai, 0, 5)) }}" required placeholder="HH:MM" pattern="^([01]\d|2[0-3]):[0-5]\d$" inputmode="numeric">
            </div>
            <div class="form-group">
                <label>Jam Selesai</label>
                <input type="text" name="jam_selesai" value="{{ old('jam_selesai', substr($jadwal->jam_selesai, 0, 5)) }}" required placeholder="HH:MM" pattern="^([01]\d|2[0-3]):[0-5]\d$" inputmode="numeric">
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <input type="text" name="keterangan" value="{{ old('keterangan', $jadwal->keterangan) }}">
            </div>
            <div class="actions">
                <button class="btn" type="submit">Perbarui</button>
                <a class="btn btn-outline" href="{{ route('jadwal.index') }}">Batal</a>
            </div>
        </form>
    </div>
@endsection
