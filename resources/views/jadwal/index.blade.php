@extends('layouts.app')

@section('content')
    <div class="card">
        <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:12px;">
            <div>
                <h2 style="margin:0;">Jadwal Penggunaan</h2>
                <p style="color:var(--muted); margin:4px 0 0;">Informasi jadwal alat berdasarkan hari.</p>
            </div>
            @if(auth()->user()->role === 'admin')
                <a class="btn" href="{{ route('jadwal.create') }}">Tambah Jadwal</a>
            @endif
        </div>

        <form method="GET" action="{{ route('jadwal.index') }}" style="margin:16px 0 12px;">
            <div class="actions">
                <select name="hari">
                    <option value="">Semua Hari</option>
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                        <option value="{{ $hari }}" @selected(request('hari') === $hari)>{{ $hari }}</option>
                    @endforeach
                </select>
                <button class="btn btn-outline" type="submit">Filter</button>
                @if(request('hari'))
                    <a class="btn btn-outline" href="{{ route('jadwal.index') }}">Reset</a>
                @endif
            </div>
        </form>

        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Alat</th>
                        <th>Tentor</th>
                        <th>Materi</th>
                        <th>Hari &amp; Tanggal</th>
                        <th>Jam</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwal as $item)
                        <tr>
                            <td>{{ $item->asset?->nama_aset ?? '-' }}</td>
                            <td>{{ $item->user?->name ?? $item->user?->username ?? '-' }}</td>
                            <td>{{ $item->materi?->nama ?? '-' }}</td>
                            <td>{{ $item->hari_tanggal ?? $item->hari }}</td>
                            <td>{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</td>
                            <td>{{ $item->keterangan ?? '-' }}</td>
                            <td>
                                @if($item->status == 'Terjadwal')
                                    <span class="badge" style="background:#ffc107; color:#000;">Terjadwal</span>
                                @elseif($item->status == 'Diterima')
                                    <span class="badge" style="background:#28a745; color:#fff;">Diterima</span>
                                    <br> <small>{{ $item->assetItem?->code }}</small>
                                @else
                                    <span class="badge">{{ $item->status }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions">
                                    @if(auth()->user()->role === 'admin')
                                        <a class="btn btn-outline" href="{{ route('jadwal.edit', $item) }}">Edit</a>
                                        <form action="{{ route('jadwal.destroy', $item) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus jadwal ini?');" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger">Hapus</button>
                                        </form>
                                    @endif

                                    @if(auth()->user()->role === 'tentor')
                                        @if($item->user_id == auth()->id() && $item->status == 'Terjadwal')
                                            <button class="btn btn-primary terima-btn" data-id="{{ $item->id }}"
                                                data-asset-id="{{ $item->asset_id }}"
                                                data-asset-name="{{ $item->asset?->nama_aset }}">Terima Alat</button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center; color:var(--muted);">Belum ada jadwal.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $jadwal->links() }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const terimaButtons = document.querySelectorAll('.terima-btn');

            terimaButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const jadwalId = this.getAttribute('data-id');
                    const assetId = this.getAttribute('data-asset-id');
                    const assetName = this.getAttribute('data-asset-name');

                    // Fetch available items
                    Swal.fire({
                        title: 'Memuat data alat...',
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`/api/assets/${assetId}/items`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(response.status + ' ' + response.statusText);
                            }
                            return response.json();
                        })
                        .then(items => {
                            Swal.close();

                            const inputOptions = {};
                            if (items.length > 0) {
                                items.forEach(item => {
                                    inputOptions[item.id] = `${item.code} (${item.condition})`;
                                });

                                Swal.fire({
                                    title: `Terima Alat: ${assetName}`,
                                    text: 'Pilih kode unit yang diterima:',
                                    input: 'select',
                                    inputOptions: inputOptions,
                                    inputPlaceholder: 'Pilih unit',
                                    showCancelButton: true,
                                    confirmButtonText: 'Konfirmasi Terima',
                                    cancelButtonText: 'Batal',
                                    inputValidator: (value) => {
                                        if (!value) {
                                            return 'Anda harus memilih unit alat!';
                                        }
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Submit form
                                        const form = document.createElement('form');
                                        form.method = 'POST';
                                        form.action = `/jadwal/${jadwalId}/confirm`;

                                        const csrf = document.createElement('input');
                                        csrf.type = 'hidden';
                                        csrf.name = '_token';
                                        csrf.value = "{{ csrf_token() }}";

                                        const input = document.createElement('input');
                                        input.type = 'hidden';
                                        input.name = 'asset_item_id';
                                        input.value = result.value;

                                        form.appendChild(csrf);
                                        form.appendChild(input);
                                        document.body.appendChild(form);
                                        form.submit();
                                    }
                                });
                            } else {
                                Swal.fire('Tidak Tersedia', 'Tidak ada unit yang tersedia untuk alat ini.', 'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error', 'Gagal memuat data alat: ' + err.message, 'error');
                        });
                });
            });
        });
    </script>
@endsection