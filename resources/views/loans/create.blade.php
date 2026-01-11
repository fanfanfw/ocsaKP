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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            materiSelect.addEventListener('change', function() {
                loadAssets(this.value);
            });

            // Initial load
            @php
                // Re-calculate or pass variable from view
                // We computed $initialMateriId inside the loop block which is scoped? No, blade @php is fine.
                // But let's just use the JS value from the select element if possible, or re-echo.
            @endphp
            const currentMateriId = "{{ $initialMateriId }}";
            const currentAssetId = "{{ old('asset_id', $selectedAssetId) }}";
            
            if (currentMateriId) {
                loadAssets(currentMateriId, currentAssetId);
            }
        });
    </script>
@endsection
