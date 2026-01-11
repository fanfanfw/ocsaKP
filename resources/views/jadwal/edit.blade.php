@extends('layouts.app')

@section('content')
    <div class="card" style="max-width:720px;">
        <h2 style="margin-top:0;">Edit Jadwal</h2>
        <form method="POST" action="{{ route('jadwal.update', $jadwal) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Materi</label>
                <select name="materi_id" id="materi_id" required>
                    <option value="">Pilih Materi</option>
                    @foreach($materi_list as $materi)
                        <option value="{{ $materi->id }}" @selected(old('materi_id', $jadwal->materi_id) == $materi->id)>
                            {{ $materi->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Alat</label>
                <select name="asset_id" id="asset_id" required>
                    <option value="">Loading...</option>
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
                <input type="text" name="jam_mulai" value="{{ old('jam_mulai', substr($jadwal->jam_mulai, 0, 5)) }}"
                    required placeholder="HH:MM" pattern="^([01]\d|2[0-3]):[0-5]\d$" inputmode="numeric">
            </div>
            <div class="form-group">
                <label>Jam Selesai</label>
                <input type="text" name="jam_selesai" value="{{ old('jam_selesai', substr($jadwal->jam_selesai, 0, 5)) }}"
                    required placeholder="HH:MM" pattern="^([01]\d|2[0-3]):[0-5]\d$" inputmode="numeric">
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const materiSelect = document.getElementById('materi_id');
            const assetSelect = document.getElementById('asset_id');

            // Function to load assets
            function loadAssets(materiId, selectedAssetId = null) {
                assetSelect.innerHTML = '<option value="">Loading...</option>';
                assetSelect.disabled = true;

                if (materiId) {
                    fetch(`/api/materi/${materiId}/assets`)
                        .then(response => response.json())
                        .then(data => {
                            assetSelect.innerHTML = '<option value="">Pilih Alat</option>';
                            if (data.length === 0) {
                                assetSelect.innerHTML = '<option value="">Tidak ada alat untuk materi ini</option>';
                            }
                            data.forEach(asset => {
                                const isSelected = selectedAssetId == asset.id ? 'selected' : '';
                                assetSelect.innerHTML += `<option value="${asset.id}" ${isSelected}>${asset.nama_aset}</option>`;
                            });
                            assetSelect.disabled = false;
                        })
                        .catch(err => {
                            console.error(err);
                            assetSelect.innerHTML = '<option value="">Gagal memuat alat</option>';
                        });
                } else {
                    assetSelect.innerHTML = '<option value="">Pilih Materi terlebih dahulu</option>';
                    assetSelect.disabled = true;
                }
            }

            materiSelect.addEventListener('change', function () {
                loadAssets(this.value);
            });

            // Initial load
            const currentMateriId = "{{ old('materi_id', $jadwal->materi_id) }}";
            const currentAssetId = "{{ old('asset_id', $jadwal->asset_id) }}";

            if (currentMateriId) {
                loadAssets(currentMateriId, currentAssetId);
            }
        });
    </script>
@endsection