@extends('layouts.app')

@section('title', 'Pusat Laporan Inventory')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Pusat Laporan Inventory</h4>
        <p class="text-muted small mb-0">Cetak dan tinjau seluruh laporan rekapitulasi data inventaris kantor pos</p>
    </div>
</div>

<div class="row g-4">
    <!-- Card Laporan Stok -->
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 p-3 text-center d-flex flex-column justify-content-between shadow-sm">
            <div>
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto d-flex align-items-center justify-content-center p-3 mb-3" style="width: 64px; height: 64px;">
                    <i class="fa-solid fa-boxes-stacked fs-3"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Laporan Stok Barang</h5>
                <p class="text-muted small mb-3">Rekapitulasi sisa stok fisik, lokasi rak, dan status persediaan barang.</p>
            </div>
            <a href="{{ route('reports.stock') }}" class="btn btn-outline-primary btn-sm w-100">
                <i class="fa-solid fa-file-lines me-1"></i> Buka Laporan
            </a>
        </div>
    </div>

    <!-- Card Laporan Stok Masuk -->
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 p-3 text-center d-flex flex-column justify-content-between shadow-sm">
            <div>
                <div class="rounded-circle bg-success bg-opacity-10 text-success mx-auto d-flex align-items-center justify-content-center p-3 mb-3" style="width: 64px; height: 64px;">
                    <i class="fa-solid fa-arrow-down-to-bracket fs-3"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Laporan Stok Masuk</h5>
                <p class="text-muted small mb-3">Rekap seluruh penerimaan dan pengadaan barang dalam rentang periode.</p>
            </div>
            <a href="{{ route('reports.stock-in') }}" class="btn btn-outline-success btn-sm w-100">
                <i class="fa-solid fa-file-lines me-1"></i> Buka Laporan
            </a>
        </div>
    </div>

    <!-- Card Laporan Stok Keluar -->
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 p-3 text-center d-flex flex-column justify-content-between shadow-sm">
            <div>
                <div class="rounded-circle bg-danger bg-opacity-10 text-danger mx-auto d-flex align-items-center justify-content-center p-3 mb-3" style="width: 64px; height: 64px;">
                    <i class="fa-solid fa-arrow-up-from-bracket fs-3"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Laporan Stok Keluar</h5>
                <p class="text-muted small mb-3">Rekap distribusi dan pengeluaran barang inventaris per periode.</p>
            </div>
            <a href="{{ route('reports.stock-out') }}" class="btn btn-outline-danger btn-sm w-100">
                <i class="fa-solid fa-file-lines me-1"></i> Buka Laporan
            </a>
        </div>
    </div>

    <!-- Card Laporan Mutasi -->
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 p-3 text-center d-flex flex-column justify-content-between shadow-sm">
            <div>
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning mx-auto d-flex align-items-center justify-content-center p-3 mb-3" style="width: 64px; height: 64px;">
                    <i class="fa-solid fa-arrows-spin fs-3 text-dark"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Laporan Mutasi Stok</h5>
                <p class="text-muted small mb-3">Audit lengkap pergerakan stok (stok awal, mutasi, stok akhir).</p>
            </div>
            <a href="{{ route('reports.movements') }}" class="btn btn-outline-warning text-dark btn-sm w-100">
                <i class="fa-solid fa-file-lines me-1"></i> Buka Laporan
            </a>
        </div>
    </div>
</div>
@endsection
