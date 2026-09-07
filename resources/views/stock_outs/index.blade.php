@extends('layouts.app')

@section('title', 'Transaksi Stok Keluar')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Transaksi Stok Keluar</h4>
        <p class="text-muted small mb-0">Pencatatan pengeluaran atau distribusi barang inventaris internal kantor pos</p>
    </div>
    <div>
        <a href="{{ route('stock-outs.create') }}" class="btn btn-danger">
            <i class="fa-solid fa-minus me-1"></i> Input Stok Keluar
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('stock-outs.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari No Transaksi, Petugas, Catatan..." value="{{ $search }}">
                </div>
            </div>

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

            <div class="col-auto">
                <button type="submit" class="btn btn-secondary">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
            </div>

            @if($search || $startDate || $endDate)
            <div class="col-auto">
                <a href="{{ route('stock-outs.index') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-rotate-left me-1"></i> Reset
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body p-0">
        @if($stockOuts->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Petugas Pengeluar</th>
                        <th>Ringkasan Barang</th>
                        <th class="text-center">Total Item</th>
                        <th>Catatan / Tujuan</th>
                        <th style="width: 100px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockOuts as $index => $out)
                    <tr>
                        <td class="text-center text-muted">{{ $stockOuts->firstItem() + $index }}</td>
                        <td>
                            <span class="fw-bold text-danger font-monospace">{{ $out->transaction_number }}</span>
                        </td>
                        <td class="text-nowrap">{{ $out->transaction_date->format('d M Y') }}</td>
                        <td>
                            <i class="fa-regular fa-user text-muted me-1"></i>{{ $out->user->name ?? '-' }}
                        </td>
                        <td>
                            <div class="small">
                                @foreach($out->details->take(2) as $d)
                                    <div>&bull; {{ $d->item->name ?? '-' }} <span class="badge bg-danger-subtle text-danger">-{{ $d->quantity }} {{ $d->item->unit ?? '' }}</span></div>
                                @endforeach
                                @if($out->details->count() > 2)
                                    <span class="text-muted fst-italic">+{{ $out->details->count() - 2 }} barang lainnya</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-center fw-bold text-danger">
                            {{ $out->details->sum('quantity') }} unit
                        </td>
                        <td class="text-muted small">{{ $out->notes ?? '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('stock-outs.show', $out) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail Transaksi">
                                <i class="fa-regular fa-eye me-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Menampilkan {{ $stockOuts->firstItem() ?? 0 }} - {{ $stockOuts->lastItem() ?? 0 }} dari total {{ $stockOuts->total() }} transaksi
            </div>
            <div>
                {{ $stockOuts->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @else
        <div class="empty-state">
            <i class="fa-solid fa-arrow-up-from-bracket text-danger"></i>
            <h5 class="fw-bold text-dark">Belum Ada Transaksi Stok Keluar</h5>
            <p class="text-muted small mb-3">Belum ada data pengeluaran stok barang yang tercatat.</p>
            <a href="{{ route('stock-outs.create') }}" class="btn btn-danger btn-sm">
                <i class="fa-solid fa-minus me-1"></i> Input Stok Keluar Baru
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
