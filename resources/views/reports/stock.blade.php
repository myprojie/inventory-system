@extends('layouts.app')

@section('title', 'Laporan Stok Barang')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 btn-print-hide">
    <div>
        <h4 class="fw-bold mb-1">Laporan Posisi Stok Barang</h4>
        <p class="text-muted small mb-0">Laporan sisa persediaan fisik seluruh barang inventaris internal kantor pos</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Pusat Laporan
        </a>
        <button onclick="window.print()" class="btn btn-dark">
            <i class="fa-solid fa-print me-1"></i> Cetak Laporan
        </button>
    </div>
</div>

<!-- Filter Card (Hidden when printing) -->
<div class="card mb-4 btn-print-hide">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('reports.stock') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <select name="category_id" class="form-select">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <select name="stock_status" class="form-select">
                    <option value="">-- Semua Status Stok --</option>
                    <option value="aman" {{ $stockStatus === 'aman' ? 'selected' : '' }}>Stok Aman</option>
                    <option value="menipis" {{ $stockStatus === 'menipis' ? 'selected' : '' }}>Stok Menipis (&le; Min)</option>
                    <option value="habis" {{ $stockStatus === 'habis' ? 'selected' : '' }}>Stok Habis (= 0)</option>
                </select>
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-secondary">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
            </div>

            @if($categoryId || $stockStatus)
            <div class="col-auto">
                <a href="{{ route('reports.stock') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-rotate-left me-1"></i> Reset
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

<!-- Printable Document -->
<div class="card p-4">
    <!-- Header for Print -->
    <div class="border-bottom pb-3 mb-4 text-center">
        <h4 class="fw-bold text-uppercase mb-1">LAPORAN REKAPITULASI STOK BARANG</h4>
        <h6 class="text-muted mb-1">SISTEM INVENTORY INTERNAL KANTOR POS</h6>
        <p class="small text-muted mb-0">Dicetak pada: {{ date('d F Y, H:i') }} WIB &bull; Oleh: {{ Auth::user()->name }}</p>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0" style="font-size: 0.88rem;">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Satuan</th>
                    <th style="width: 110px;">Stok Fisik</th>
                    <th style="width: 100px;">Batas Min.</th>
                    <th>Lokasi Rak</th>
                    <th>Kondisi Stok</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $item->name }}</td>
                    <td>{{ $item->category->name ?? '-' }}</td>
                    <td class="text-center">{{ $item->unit }}</td>
                    <td class="text-center fw-bold {{ $item->stock <= 0 ? 'text-danger' : ($item->stock <= $item->minimum_stock ? 'text-warning' : 'text-success') }}">
                        {{ number_format($item->stock) }}
                    </td>
                    <td class="text-center text-muted">{{ number_format($item->minimum_stock) }}</td>
                    <td>{{ $item->location ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $item->stock_status_badge_class }}">
                            {{ $item->stock_status_label }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">Tidak ada data barang yang memenuhi filter.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="4" class="text-end">Total Seluruh Unit Stok Fisik:</td>
                    <td class="text-center">{{ number_format($items->sum('stock')) }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Signature Footer for Print -->
    <div class="row mt-5 pt-4 d-none d-print-flex">
        <div class="col-6 text-center">
            <p class="mb-5">Petugas Inventaris,</p>
            <p class="fw-bold mb-0 text-decoration-underline">{{ Auth::user()->name }}</p>
            <p class="small text-muted">{{ ucfirst(Auth::user()->role) }}</p>
        </div>
        <div class="col-6 text-center">
            <p class="mb-5">Pimpinan Kantor Pos,</p>
            <p class="fw-bold mb-0 text-decoration-underline">( ........................................ )</p>
            <p class="small text-muted">Kepala Kantor</p>
        </div>
    </div>
</div>
@endsection
