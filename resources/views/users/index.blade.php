@extends('layouts.app')

@section('content')
    <div class="card">
        <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:12px;">
            <div>
                <h2 style="margin:0;">Pengguna</h2>
                <p style="color:var(--muted); margin:4px 0 0;">Kelola akun admin dan tentor.</p>
            </div>
            <a class="btn" href="{{ route('users.create') }}">Tambah Pengguna</a>
        </div>

        <div style="overflow-x:auto; margin-top:12px;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->name ?? '-' }}</td>
                            <td><span class="badge">{{ $user->role }}</span></td>
                            <td>{{ optional($user->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="actions">
                                    <a class="btn btn-outline" href="{{ route('users.edit', $user) }}">Edit</a>
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Hapus pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; color:var(--muted);">Belum ada data pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $users->links() }}
        </div>
    </div>
@endsection
