@extends('layouts.app')

@section('title', 'Detail Barang - ' . $item->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Detail Barang Inventaris</h4>
        <p class="text-muted small mb-0">Informasi spesifikasi dan riwayat mutasi barang</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
        @if(Auth::user()->role === 'admin')
        <a href="{{ route('items.edit', $item) }}" class="btn btn-primary">
            <i class="fa-solid fa-pen-to-square me-1"></i> Edit Barang
        </a>
        @endif
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Item Info Card -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold"><i class="fa-solid fa-box text-primary me-2"></i>Informasi Spesifikasi</h6>
            </div>
            <div class="card-body p-4">
                <div class="text-center mb-4 pb-3 border-bottom">
                    <div class="display-6 fw-bold {{ $item->stock <= 0 ? 'text-danger' : ($item->stock <= $item->minimum_stock ? 'text-warning' : 'text-success') }}">
                        {{ number_format($item->stock) }} <span class="fs-5 text-muted">{{ $item->unit }}</span>
                    </div>
                    <div class="mt-2">
                        <span class="badge {{ $item->stock_status_badge_class }} px-3 py-2">
                            {{ $item->stock_status_label }}
                        </span>
                        @if($item->status)
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 ms-1">Status: Aktif</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 ms-1">Status: Nonaktif</span>
                        @endif
                    </div>
                </div>

                <div class="list-group list-group-flush small">
                    <div class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted">Nama Barang:</span>
                        <span class="fw-bold text-dark">{{ $item->name }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted">Kategori:</span>
                        <span class="badge bg-light text-dark border">{{ $item->category->name ?? '-' }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted">Satuan Unit:</span>
                        <span class="fw-medium text-dark">{{ $item->unit }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted">Batas Minimum Stok:</span>
                        <span class="fw-medium text-dark">{{ number_format($item->minimum_stock) }} {{ $item->unit }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted">Lokasi Penyimpanan:</span>
                        <span class="fw-medium text-dark">{{ $item->location ?? '-' }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted">Terdaftar Pada:</span>
                        <span class="text-dark">{{ $item->created_at ? $item->created_at->format('d M Y H:i') : '-' }}</span>
                    </div>
                </div>

                @if($item->description)
                <div class="mt-3 p-3 bg-light rounded-3 small">
                    <strong class="text-muted d-block mb-1">Deskripsi:</strong>
                    <p class="mb-0 text-dark">{{ $item->description }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Movement History for this Item -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="fa-solid fa-clock-rotate-left text-warning me-2"></i>Riwayat Pergerakan Stok Barang</h6>
                <span class="badge bg-light text-dark border">Total: {{ $movements->total() }} Log</span>
            </div>
            <div class="card-body p-0">
                @if($movements->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Sebelum</th>
                                <th class="text-center">Sesudah</th>
                                <th>Petugas</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movements as $m)
                            <tr>
                                <td class="text-nowrap text-muted">{{ $m->created_at->format('d/m/y H:i') }}</td>
                                <td>
                                    @if($m->type === 'IN')
                                        <span class="badge bg-success">MASUK</span>
                                    @else
                                        <span class="badge bg-danger">KELUAR</span>
                                    @endif
                                </td>
                                <td class="text-center fw-bold {{ $m->type === 'IN' ? 'text-success' : 'text-danger' }}">
                                    {{ $m->type === 'IN' ? '+' : '-' }}{{ $m->quantity }}
                                </td>
                                <td class="text-center text-muted">{{ $m->stock_before }}</td>
                                <td class="text-center fw-bold">{{ $m->stock_after }}</td>
                                <td class="small">{{ $m->user->name ?? '-' }}</td>
                                <td class="small text-muted">{{ $m->notes ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top">
                    {{ $movements->links('pagination::bootstrap-5') }}
                </div>
                @else
                <div class="p-4 text-center text-muted">
                    <i class="fa-solid fa-arrows-spin fs-2 text-muted mb-2"></i>
                    <p class="mb-0 small">Belum ada riwayat pergerakan stok untuk barang ini.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
