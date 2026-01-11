@extends('layouts.app')

@section('content')
    <div class="card">
        <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:12px;">
            <div>
                <h2 style="margin:0;">Kelola Materi</h2>
                <p style="color:var(--muted); margin:4px 0 0;">Daftar materi yang tersedia untuk kegiatan belajar mengajar.
                </p>
            </div>
            <a class="btn" href="{{ route('materi.create') }}">Tambah Materi</a>
        </div>

        <form method="GET" action="{{ route('materi.index') }}" style="margin:16px 0 12px;">
            <div class="actions">
                <input type="text" name="q" placeholder="Cari materi..." value="{{ request('q') }}">
                <button class="btn btn-outline" type="submit">Cari</button>
                @if(request('q'))
                    <a class="btn btn-outline" href="{{ route('materi.index') }}">Reset</a>
                @endif
            </div>
        </form>

        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Materi</th>
                        <th>Jumlah Alat Terkait</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materi as $item)
                        <tr>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->assets_count ?? $item->assets()->count() }}</td>
                            <td>
                                <div class="actions">
                                    <a class="btn btn-outline" href="{{ route('materi.edit', $item) }}">Edit</a>
                                    <button class="btn btn-danger delete-btn" data-id="{{ $item->id }}"
                                        data-name="{{ $item->nama }}">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align:center; color:var(--muted);">Belum ada data materi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $materi->links() }}
        </div>
    </div>

    <!-- SweetAlert2 for Delete Confirmation -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.delete-btn');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');

                    Swal.fire({
                        title: 'Hapus Materi?',
                        text: `Apakah Anda yakin ingin menghapus materi "${name}"?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#c93a3a',
                        cancelButtonColor: '#5f6b63',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/materi/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                }
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire(
                                            'Terhapus!',
                                            data.message,
                                            'success'
                                        ).then(() => {
                                            window.location.reload();
                                        });
                                    } else {
                                        Swal.fire(
                                            'Gagal!',
                                            data.message,
                                            'error'
                                        );
                                    }
                                })
                                .catch(error => {
                                    Swal.fire(
                                        'Error!',
                                        'Terjadi kesalahan saat menghapus data.',
                                        'error'
                                    );
                                });
                        }
                    });
                });
            });
        });
    </script>
@endsection