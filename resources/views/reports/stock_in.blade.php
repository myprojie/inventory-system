@extends('layouts.app')

@section('title', 'Laporan Stok Masuk')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 btn-print-hide">
    <div>
        <h4 class="fw-bold mb-1">Laporan Rekapitulasi Stok Masuk</h4>
        <p class="text-muted small mb-0">Laporan data penerimaan dan pengadaan barang inventaris internal kantor pos</p>
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

<!-- Filters -->
<div class="card mb-4 btn-print-hide">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('reports.stock-in') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-light small">Dari</span>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
            </div>

            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-light small">Sampai</span>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
            </div>

            <div class="col-md-4">
                <select name="item_id" class="form-select">
                    <option value="">-- Semua Barang --</option>
                    @foreach($items as $i)
                        <option value="{{ $i->id }}" {{ $itemId == $i->id ? 'selected' : '' }}>{{ $i->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-secondary">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
            </div>

            @if($startDate || $endDate || $itemId)
            <div class="col-auto">
                <a href="{{ route('reports.stock-in') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-rotate-left me-1"></i> Reset
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

<!-- Document -->
<div class="card p-4">
    <div class="border-bottom pb-3 mb-4 text-center">
        <h4 class="fw-bold text-uppercase mb-1">LAPORAN PENERIMAAN STOK MASUK</h4>
        <h6 class="text-muted mb-1">SISTEM INVENTORY INTERNAL KANTOR POS</h6>
        <p class="small text-muted mb-0">
            Periode: <strong>{{ date('d M Y', strtotime($startDate)) }}</strong> s/d <strong>{{ date('d M Y', strtotime($endDate)) }}</strong> &bull; Dicetak: {{ date('d F Y, H:i') }} WIB
        </p>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0" style="font-size: 0.88rem;">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>No. Transaksi</th>
                    <th>Tanggal</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th style="width: 120px;">Jumlah Masuk</th>
                    <th>Petugas Penerima</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($details as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-monospace fw-bold text-success">{{ $detail->stockIn->transaction_number ?? '-' }}</td>
                    <td class="text-nowrap">{{ $detail->stockIn->transaction_date ? $detail->stockIn->transaction_date->format('d/m/Y') : '-' }}</td>
                    <td class="fw-semibold">{{ $detail->item->name ?? 'Barang telah dihapus' }}</td>
                    <td>{{ $detail->item->category->name ?? '-' }}</td>
                    <td class="text-center fw-bold text-success">
                        +{{ number_format($detail->quantity) }} {{ $detail->item->unit ?? '' }}
                    </td>
                    <td>{{ $detail->stockIn->user->name ?? '-' }}</td>
                    <td class="small text-muted">{{ $detail->stockIn->notes ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">Tidak ada transaksi stok masuk dalam periode ini.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="5" class="text-end">Total Jumlah Barang Masuk:</td>
                    <td class="text-center text-success">+{{ number_format($details->sum('quantity')) }} unit</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>

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
