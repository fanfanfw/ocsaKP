@extends('layouts.app')

@section('content')
    <div class="card">
        <h2 style="margin-top:0;">Approval Peminjaman</h2>
        <p style="color:var(--muted); margin-top:-4px;">Kelola pengajuan peminjaman alat dari tentor.</p>

        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Pemohon</th>
                        <th>Materi & Alat</th>
                        <th>Jumlah</th>
                        <th>Jadwal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td>
                                <div>{{ $booking->user->name }}</div>
                                <small style="color:var(--muted);">{{ $booking->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <div><strong>{{ $booking->asset->nama_aset }}</strong></div>
                                <div style="font-size:13px; color:var(--primary);">
                                    Kode Unit: {{ $booking->assetItem->code ?? '-' }}
                                </div>
                                <small style="color:var(--muted);">Materi: {{ $booking->materi->nama }}</small>
                            </td>
                            <td>{{ $booking->jumlah }} Unit</td>
                            <td>
                                <div>{{ $booking->tanggal->format('d/m/Y') }}</div>
                                <small>{{ $booking->waktu }}</small>
                            </td>
                            <td>
                                @if($booking->status === 'pending')
                                    <span class="badge" style="background:#e07b39; color:#fff;">Pending</span>
                                @elseif($booking->status === 'approved')
                                    <span class="badge" style="background:#1b7f5a; color:#fff;">Disetujui</span>
                                @else
                                    <span class="badge" style="background:#d32f2f; color:#fff;">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                @if(auth()->user()->role === 'admin' && $booking->status === 'pending')
                                    <div class="actions">
                                        <button class="btn btn-sm btn-approve" data-id="{{ $booking->id }}"
                                            style="background:#1b7f5a; color:white;">Approve</button>
                                        <button class="btn btn-sm btn-danger btn-reject" data-id="{{ $booking->id }}"
                                            style="color:white;">Reject</button>

                                        <form id="form-approve-{{ $booking->id }}"
                                            action="{{ route('bookings.approve', $booking) }}" method="POST" style="display:none;">
                                            @csrf
                                        </form>
                                        <form id="form-reject-{{ $booking->id }}" action="{{ route('bookings.reject', $booking) }}"
                                            method="POST" style="display:none;">
                                            @csrf
                                        </form>
                                    </div>
                                @else
                                    <span style="color:var(--muted);">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; color:var(--muted);">Belum ada pengajuan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $bookings->links() }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Approve
            document.querySelectorAll('.btn-approve').forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    Swal.fire({
                        title: 'Setujui Pengajuan?',
                        text: "Data akan masuk ke Peminjaman (Loans) dan stok unit berkurang.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#1b7f5a',
                        cancelButtonColor: '#5f6b63',
                        confirmButtonText: 'Ya, Setujui',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`form-approve-${id}`).submit();
                        }
                    });
                });
            });

            // Reject
            document.querySelectorAll('.btn-reject').forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    Swal.fire({
                        title: 'Tolak Pengajuan?',
                        text: "Pengajuan akan ditandai sebagai Ditolak.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#c93a3a',
                        cancelButtonColor: '#5f6b63',
                        confirmButtonText: 'Ya, Tolak',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`form-reject-${id}`).submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection