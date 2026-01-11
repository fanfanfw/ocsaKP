@extends('layouts.app')

@section('content')
    <div class="card" style="max-width:720px;">
        <h2 style="margin-top:0;">Edit Materi</h2>
        <form method="POST" action="{{ route('materi.update', $materi) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Nama Materi</label>
                <input type="text" name="nama" value="{{ old('nama', $materi->nama) }}" required>
            </div>
            <div class="actions">
                <button class="btn" type="submit">Simpan</button>
                <a class="btn btn-outline" href="{{ route('materi.index') }}">Batal</a>
            </div>
        </form>
    </div>
@endsection