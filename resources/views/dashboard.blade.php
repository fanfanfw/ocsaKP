@extends('layouts.app')

@section('content')
    @if(auth()->user()->role === 'admin')
        <div class="card">
            <h2 style="margin-top:0;">Ringkasan</h2>
            <p style="color: var(--muted); margin-top: -4px;">Pantau kondisi inventaris dan aktivitas hari ini.</p>
            <div class="grid grid-4">
                <div class="card" style="border: 1px solid #dbe7e0;">
                    <div class="badge">Total Alat</div>
                    <h3 style="margin: 12px 0 0;">{{ $totalAssets }}</h3>
                </div>
                <div class="card" style="border: 1px solid #dbe7e0;">
                    <div class="badge">Alat Tersedia</div>
                    <h3 style="margin: 12px 0 0;">{{ $availableAssets }}</h3>
                </div>
                <div class="card" style="border: 1px solid #dbe7e0;">
                    <div class="badge">Alat Terjadwal</div>
                    <h3 style="margin: 12px 0 0;">{{ $scheduledAssets }}</h3>
                </div>
                <div class="card" style="border: 1px solid #dbe7e0;">
                    <div class="badge">Sedang Digunakan</div>
                    <h3 style="margin: 12px 0 0;">{{ $activeLoans }}</h3>
                </div>
                <div class="card" style="border: 1px solid #dbe7e0;">
                    <div class="badge">Perawatan Aktif</div>
                    <h3 style="margin: 12px 0 0;">{{ $maintenanceOpen }}</h3>
                </div>
            </div>
        </div>

        <div class="card" style="margin-top:20px;">
            <h3 style="margin-top:0;">Komposisi Status Alat</h3>
            <p style="color:var(--muted); margin-top:-4px;">Distribusi alat berdasarkan status saat ini.</p>
            <div style="max-width:420px; margin: 0 auto;">
                <canvas id="statusChart" height="260"></canvas>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            const statusData = {
                tersedia: {{ (int) ($statusCounts['Tersedia'] ?? 0) }},
                terjadwal: {{ (int) ($statusCounts['Terjadwal'] ?? 0) }},
                digunakan: {{ (int) ($statusCounts['Dipinjam'] ?? 0) }}
            };

            const labels = ['Tersedia', 'Terjadwal', 'Digunakan'];
            const values = [statusData.tersedia, statusData.terjadwal, statusData.digunakan];

            const ctx = document.getElementById('statusChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: ['#1b7f5a', '#f3c969', '#e07b39'],
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        </script>
    @else
        <div class="card">
            <h2 style="margin-top:0;">Daftar Alat</h2>
            <p style="color:var(--muted); margin-top:-4px;">Gunakan alat secara manual di luar jadwal.</p>
            <div style="overflow-x:auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Alat</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                            <tr>
                                <td>{{ $asset->nama_aset }}</td>
                                <td>{{ $asset->kategori ?? '-' }}</td>
                                @php
                                    $activeLoans = $activeCounts[$asset->id] ?? 0;
                                    $available = max($asset->jumlah - $activeLoans, 0);
                                    $isScheduled = in_array($asset->id, $scheduledIds ?? [], true);
                                @endphp
                                <td>
                                    @if($isScheduled)
                                        <span class="badge">Terjadwal</span>
                                        @if($available > 0)
                                            <span class="badge">Tersedia</span>
                                        @endif
                                    @elseif($available <= 0)
                                        <span class="badge">Digunakan</span>
                                    @else
                                        <span class="badge">Tersedia</span>
                                    @endif
                                </td>
                                <td>{{ $available }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center; color:var(--muted);">Belum ada data alat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
