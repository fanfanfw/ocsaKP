@extends('layouts.app')

@section('content')
    <div class="card" style="max-width:720px;">
        <h2 style="margin-top:0;">Pengembalian Alat</h2>
        <p style="color:var(--muted);">Alat: {{ $loan->asset?->nama_aset ?? '-' }}</p>
        <form method="POST" action="{{ route('loans.processReturn', $loan) }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Bukti Kembali (opsional)</label>
                <input type="file" name="bukti_kembali" accept=".jpg,.jpeg,.png,.pdf">
            </div>
            <div class="actions">
                <button class="btn" type="submit">Proses Pengembalian</button>
                <a class="btn btn-outline" href="{{ route('loans.index') }}">Batal</a>
            </div>
        </form>
    </div>
@endsection
