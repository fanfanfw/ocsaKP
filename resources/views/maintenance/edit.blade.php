@extends('layouts.app')

@section('content')
    <div class="card" style="max-width:780px;">
        <h2 style="margin-top:0;">Edit Perawatan</h2>
        <form method="POST" action="{{ route('maintenance.update', $maintenance) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Aset</label>
                <select name="asset_id" required>
                    @foreach($assets as $asset)
                        <option value="{{ $asset->id }}" @selected(old('asset_id', $maintenance->asset_id) == $asset->id)>{{ $asset->nama_aset }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" required>{{ old('deskripsi', $maintenance->deskripsi) }}</textarea>
            </div>
            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', optional($maintenance->tanggal)->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label>Kode Unit</label>
                    <select name="asset_item_id" id="asset_item_id" required disabled>
                        <option value="">Pilih Aset terlebih dahulu</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jenis Kerusakan</label>
                    <input type="text" name="jenis_kerusakan" value="{{ old('jenis_kerusakan', $maintenance->jenis_kerusakan) }}">
                </div>
                <div class="form-group">
                    <label>Tingkat</label>
                    <select name="tingkat">
                        <option value="">Pilih Tingkat</option>
                        @foreach(['Ringan','Sedang','Berat'] as $level)
                            <option value="{{ $level }}" @selected(old('tingkat', $maintenance->tingkat) === $level)>{{ $level }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Tindakan</label>
                <textarea name="tindakan">{{ old('tindakan', $maintenance->tindakan) }}</textarea>
            </div>
            <div class="form-group">
                <label>Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', optional($maintenance->tanggal_selesai)->format('Y-m-d')) }}">
            </div>
            <div class="actions">
                <button class="btn" type="submit">Perbarui</button>
                <a class="btn btn-outline" href="{{ route('maintenance.index') }}">Batal</a>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const assetSelect = document.querySelector('select[name="asset_id"]');
            const itemSelect = document.getElementById('asset_item_id');
            const selectedAssetId = "{{ old('asset_id', $maintenance->asset_id) }}";
            const selectedItemId = "{{ old('asset_item_id', $maintenance->asset_item_id) }}";

            function resetItems(message) {
                itemSelect.innerHTML = `<option value="">${message}</option>`;
                itemSelect.disabled = true;
            }

            function loadItems(assetId, selectedId) {
                resetItems('Loading...');
                if (!assetId) {
                    resetItems('Pilih Aset terlebih dahulu');
                    return;
                }

                fetch(`/api/assets/${assetId}/items?all=1`)
                    .then(response => response.json())
                    .then(items => {
                        itemSelect.innerHTML = '<option value="">Pilih Kode Unit</option>';
                        if (!items || items.length === 0) {
                            itemSelect.innerHTML = '<option value="">Tidak ada unit</option>';
                            itemSelect.disabled = true;
                            return;
                        }
                        items.forEach(item => {
                            const isSelected = String(selectedId) === String(item.id) ? 'selected' : '';
                            const suffix = item.is_available ? '' : ' (Tidak tersedia)';
                            itemSelect.innerHTML += `<option value="${item.id}" ${isSelected}>${item.code}${suffix}</option>`;
                        });
                        itemSelect.disabled = false;
                    })
                    .catch(() => resetItems('Gagal memuat unit'));
            }

            assetSelect.addEventListener('change', function() {
                loadItems(this.value, null);
            });

            assetSelect.value = selectedAssetId;
            loadItems(selectedAssetId, selectedItemId);
        });
    </script>
@endsection
