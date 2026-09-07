@extends('layouts.app')

@section('title', 'Transaksi Stok Masuk')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Transaksi Stok Masuk</h4>
        <p class="text-muted small mb-0">Pencatatan penerimaan barang inventaris ke dalam gudang pos</p>
    </div>
    <div>
        <a href="{{ route('stock-ins.create') }}" class="btn btn-success">
            <i class="fa-solid fa-plus me-1"></i> Input Stok Masuk
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('stock-ins.index') }}" class="row g-2 align-items-center">
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
                <a href="{{ route('stock-ins.index') }}" class="btn btn-outline-secondary">
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
        @if($stockIns->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Petugas Penerima</th>
                        <th>Ringkasan Barang</th>
                        <th class="text-center">Total Item</th>
                        <th>Catatan</th>
                        <th style="width: 100px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockIns as $index => $in)
                    <tr>
                        <td class="text-center text-muted">{{ $stockIns->firstItem() + $index }}</td>
                        <td>
                            <span class="fw-bold text-success font-monospace">{{ $in->transaction_number }}</span>
                        </td>
                        <td class="text-nowrap">{{ $in->transaction_date->format('d M Y') }}</td>
                        <td>
                            <i class="fa-regular fa-user text-muted me-1"></i>{{ $in->user->name ?? '-' }}
                        </td>
                        <td>
                            <div class="small">
                                @foreach($in->details->take(2) as $d)
                                    <div>&bull; {{ $d->item->name ?? '-' }} <span class="badge bg-success-subtle text-success">+{{ $d->quantity }} {{ $d->item->unit ?? '' }}</span></div>
                                @endforeach
                                @if($in->details->count() > 2)
                                    <span class="text-muted fst-italic">+{{ $in->details->count() - 2 }} barang lainnya</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-center fw-bold">
                            {{ $in->details->sum('quantity') }} unit
                        </td>
                        <td class="text-muted small">{{ $in->notes ?? '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('stock-ins.show', $in) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail Transaksi">
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
                Menampilkan {{ $stockIns->firstItem() ?? 0 }} - {{ $stockIns->lastItem() ?? 0 }} dari total {{ $stockIns->total() }} transaksi
            </div>
            <div>
                {{ $stockIns->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @else
        <div class="empty-state">
            <i class="fa-solid fa-arrow-down-to-bracket text-success"></i>
            <h5 class="fw-bold text-dark">Belum Ada Transaksi Stok Masuk</h5>
            <p class="text-muted small mb-3">Belum ada data penerimaan stok barang yang tercatat.</p>
            <a href="{{ route('stock-ins.create') }}" class="btn btn-success btn-sm">
                <i class="fa-solid fa-plus me-1"></i> Input Stok Masuk Baru
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
