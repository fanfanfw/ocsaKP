@extends('layouts.app')

@section('content')
    <div class="card" style="max-width:720px;">
        <h2 style="margin-top:0;">Tambah Penggunaan Alat</h2>
        <form method="POST" action="{{ route('loans.store') }}">
            @csrf
            @if(auth()->user()->role === 'admin')
                <div class="form-group">
                <label>Pengguna (Tentor)</label>
                    <select name="user_id">
                        <option value="">Pilih Tentor</option>
                        @foreach($tentor as $user)
                            <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                                {{ $user->name ?? $user->username }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="form-group">
                <label>Alat</label>
                <select name="asset_id" required>
                    <option value="">Pilih Alat</option>
                    @foreach($assets as $asset)
                        @php
                            $activeLoans = $activeCounts[$asset->id] ?? 0;
                            $available = max($asset->jumlah - $activeLoans, 0);
                            $isScheduled = in_array($asset->id, $scheduledIds ?? [], true);
                            $disabled = $available <= 0;
                            $note = $isScheduled ? 'Terjadwal, Tersedia: ' . $available : 'Tersedia: ' . $available;
                        @endphp
                        <option value="{{ $asset->id }}" @selected(old('asset_id', $selectedAssetId) == $asset->id) @disabled($disabled)>
                            {{ $asset->nama_aset }} ({{ $note }})
                        </option>
                    @endforeach
                </select>
                <small style="color:var(--muted);">Alat yang habis otomatis dinonaktifkan.</small>
            </div>
            <div class="form-group">
                <label>Tanggal Digunakan</label>
                <input type="datetime-local" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', now()->format('Y-m-d\\TH:i')) }}">
            </div>
            <div class="actions">
                <button class="btn" type="submit">Simpan</button>
                <a class="btn btn-outline" href="{{ route('loans.index') }}">Batal</a>
            </div>
        </form>
    </div>
@endsection
