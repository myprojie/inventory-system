@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Dashboard Inventory</h4>
        <p class="text-muted small mb-0">Ringkasan kondisi inventaris dan aktivitas pergerakan stok internal kantor pos</p>
    </div>
    @if(in_array(Auth::user()->role, ['admin', 'petugas']))
    <div class="d-flex gap-2">
        <a href="{{ route('stock-ins.create') }}" class="btn btn-sm btn-success">
            <i class="fa-solid fa-plus me-1"></i> Stok Masuk
        </a>
        <a href="{{ route('stock-outs.create') }}" class="btn btn-sm btn-danger">
            <i class="fa-solid fa-minus me-1"></i> Stok Keluar
        </a>
    </div>
    @endif
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-3">
        <div class="card h-100 border-0 shadow-sm bg-primary text-white p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-semibold text-white-50">Total Barang</span>
                <i class="fa-solid fa-boxes-stacked fs-4 opacity-75"></i>
            </div>
            <h3 class="fw-bold mb-0">{{ number_format($totalItems) }}</h3>
            <span class="small text-white-50 mt-1">Item terdaftar</span>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-3">
        <div class="card h-100 border-0 shadow-sm bg-info text-white p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-semibold text-white-50">Total Kategori</span>
                <i class="fa-solid fa-tags fs-4 opacity-75"></i>
            </div>
            <h3 class="fw-bold mb-0">{{ number_format($totalCategories) }}</h3>
            <span class="small text-white-50 mt-1">Kategori aktif</span>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-3">
        <div class="card h-100 border-0 shadow-sm bg-secondary text-white p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-semibold text-white-50">Total Stok Fisik</span>
                <i class="fa-solid fa-cubes fs-4 opacity-75"></i>
            </div>
            <h3 class="fw-bold mb-0">{{ number_format($totalStock) }}</h3>
            <span class="small text-white-50 mt-1">Unit seluruh barang</span>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-3">
        <div class="card h-100 border-0 shadow-sm bg-warning text-dark p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-semibold opacity-75">Stok Menipis</span>
                <i class="fa-solid fa-triangle-exclamation fs-4 opacity-75"></i>
            </div>
            <h3 class="fw-bold mb-0">{{ number_format($lowStockCount) }}</h3>
            <span class="small opacity-75 mt-1">&le; batas minimum</span>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-3">
        <div class="card h-100 border-0 shadow-sm bg-danger text-white p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-semibold text-white-50">Stok Habis</span>
                <i class="fa-solid fa-circle-xmark fs-4 opacity-75"></i>
            </div>
            <h3 class="fw-bold mb-0">{{ number_format($outOfStockCount) }}</h3>
            <span class="small text-white-50 mt-1">Stok = 0 unit</span>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-3">
        <div class="card h-100 border-0 shadow-sm bg-success text-white p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-semibold text-white-50">Total Masuk</span>
                <i class="fa-solid fa-arrow-down fs-4 opacity-75"></i>
            </div>
            <h3 class="fw-bold mb-0">{{ number_format($totalStockInQty) }}</h3>
            <span class="small text-white-50 mt-1">Unit diterima</span>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-3">
        <div class="card h-100 border-0 shadow-sm bg-dark text-white p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-semibold text-white-50">Total Keluar</span>
                <i class="fa-solid fa-arrow-up fs-4 opacity-75 text-danger"></i>
            </div>
            <h3 class="fw-bold mb-0">{{ number_format($totalStockOutQty) }}</h3>
            <span class="small text-white-50 mt-1">Unit didistribusikan</span>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-3">
        <div class="card h-100 border-0 shadow-sm bg-white p-3 d-flex flex-column justify-content-center">
            <span class="small text-muted fw-semibold">Pusat Laporan</span>
            <div class="mt-2">
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="fa-solid fa-file-export me-1"></i> Buka Laporan
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Chart & Low Stock Items Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fa-solid fa-chart-column me-2 text-primary"></i>Tren Transaksi Stok Tahun {{ date('Y') }}</span>
                <span class="badge bg-light text-dark border">Per Bulan</span>
            </div>
            <div class="card-body p-3">
                <canvas id="movementChart" style="max-height: 280px;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold text-danger"><i class="fa-solid fa-bell me-2"></i>Peringatan Stok Menipis</span>
                <a href="{{ route('items.index', ['stock_status' => 'menipis']) }}" class="small text-decoration-none">Lihat Semua &rarr;</a>
            </div>
            <div class="card-body p-0">
                @if($lowStockItems->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Barang</th>
                                <th>Kategori</th>
                                <th class="text-center">Stok</th>
                                <th class="text-center">Min</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockItems as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item->name }}</td>
                                <td class="text-muted">{{ $item->category->name ?? '-' }}</td>
                                <td class="text-center fw-bold {{ $item->stock == 0 ? 'text-danger' : 'text-warning' }}">{{ $item->stock }} {{ $item->unit }}</td>
                                <td class="text-center text-muted">{{ $item->minimum_stock }}</td>
                                <td>
                                    <span class="badge {{ $item->stock_status_badge_class }}">
                                        {{ $item->stock_status_label }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-4 text-center text-muted">
                    <i class="fa-solid fa-circle-check fs-2 text-success mb-2"></i>
                    <p class="mb-0 small">Semua stok barang berada dalam kondisi aman.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Stock Movements -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="fa-solid fa-clock-rotate-left me-2 text-warning"></i>Riwayat Pergerakan Stok Terkini</span>
        <a href="{{ route('stock-movements.index') }}" class="small text-decoration-none">Semua Pergerakan &rarr;</a>
    </div>
    <div class="card-body p-0">
        @if($recentMovements->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size: 0.85rem;">
                <thead class="table-light">
                    <tr>
                        <th>Waktu</th>
                        <th>Barang</th>
                        <th>Tipe</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-center">Stok Sebelum</th>
                        <th class="text-center">Stok Sesudah</th>
                        <th>Petugas</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentMovements as $movement)
                    <tr>
                        <td class="text-muted text-nowrap">{{ $movement->created_at->format('d M Y H:i') }}</td>
                        <td class="fw-semibold">{{ $movement->item->name ?? '-' }}</td>
                        <td>
                            @if($movement->type === 'IN')
                                <span class="badge bg-success"><i class="fa-solid fa-arrow-down me-1"></i> MASUK</span>
                            @else
                                <span class="badge bg-danger"><i class="fa-solid fa-arrow-up me-1"></i> KELUAR</span>
                            @endif
                        </td>
                        <td class="text-center fw-bold {{ $movement->type === 'IN' ? 'text-success' : 'text-danger' }}">
                            {{ $movement->type === 'IN' ? '+' : '-' }}{{ $movement->quantity }} {{ $movement->item->unit ?? '' }}
                        </td>
                        <td class="text-center text-muted">{{ $movement->stock_before }}</td>
                        <td class="text-center fw-bold">{{ $movement->stock_after }}</td>
                        <td>{{ $movement->user->name ?? '-' }}</td>
                        <td class="text-muted small">{{ $movement->notes ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-4 text-center text-muted">
            <i class="fa-solid fa-boxes-stacked fs-2 text-muted mb-2"></i>
            <p class="mb-0 small">Belum ada aktivitas pergerakan stok.</p>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('movementChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! $monthLabels !!},
                    datasets: [
                        {
                            label: 'Stok Masuk',
                            data: {!! $monthlyInData !!},
                            backgroundColor: 'rgba(34, 197, 94, 0.75)',
                            borderColor: 'rgb(34, 197, 94)',
                            borderWidth: 1,
                            borderRadius: 4
                        },
                        {
                            label: 'Stok Keluar',
                            data: {!! $monthlyOutData !!},
                            backgroundColor: 'rgba(239, 68, 68, 0.75)',
                            borderColor: 'rgb(239, 68, 68)',
                            borderWidth: 1,
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
