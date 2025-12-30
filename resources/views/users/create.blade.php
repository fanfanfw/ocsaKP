@extends('layouts.app')

@section('content')
    <div class="card" style="max-width:720px;">
        <h2 style="margin-top:0;">Tambah Pengguna</h2>
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="{{ old('username') }}" required>
            </div>
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="name" value="{{ old('name') }}">
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" required>
                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                    <option value="tentor" @selected(old('role') === 'tentor')>Tentor</option>
                </select>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="actions">
                <button class="btn" type="submit">Simpan</button>
                <a class="btn btn-outline" href="{{ route('users.index') }}">Batal</a>
            </div>
        </form>
    </div>
@endsection
