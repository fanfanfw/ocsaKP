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
                <label>Pilih Unit Alat</label>
                <select name="asset_item_id" required>
                    <option value="">-- Pilih Unit --</option>
                    @foreach ($assets as $asset)
                        <optgroup label="{{ $asset->nama_aset }}">
                            @forelse($asset->items as $item)
                                <option value="{{ $item->id }}" @selected(old('asset_item_id') == $item->id)>
                                    Kode: {{ $item->code }} (Kondisi: {{ $item->condition }})
                                </option>
                            @empty
                                <option disabled>Tidak ada unit tersedia</option>
                            @endforelse
                        </optgroup>
                    @endforeach
                </select>
                <small style="color:var(--muted);">Hanya unit yang tersedia yang ditampilkan.</small>
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

