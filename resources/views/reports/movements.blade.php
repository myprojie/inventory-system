@extends('layouts.app')

@section('title', 'Laporan Mutasi Stok')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 btn-print-hide">
    <div>
        <h4 class="fw-bold mb-1">Laporan Mutasi & Pergerakan Stok</h4>
        <p class="text-muted small mb-0">Laporan log lengkap perubahan dan riwayat transaksi stok inventaris internal kantor pos</p>
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
        <form method="GET" action="{{ route('reports.movements') }}" class="row g-2 align-items-center">
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

            <div class="col-md-3">
                <select name="item_id" class="form-select">
                    <option value="">-- Semua Barang --</option>
                    @foreach($items as $i)
                        <option value="{{ $i->id }}" {{ $itemId == $i->id ? 'selected' : '' }}>{{ $i->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="type" class="form-select">
                    <option value="">-- Semua Tipe --</option>
                    <option value="IN" {{ $type === 'IN' ? 'selected' : '' }}>Masuk (IN)</option>
                    <option value="OUT" {{ $type === 'OUT' ? 'selected' : '' }}>Keluar (OUT)</option>
                </select>
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-secondary">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
            </div>

            @if($startDate || $endDate || $itemId || $type)
            <div class="col-auto">
                <a href="{{ route('reports.movements') }}" class="btn btn-outline-secondary">
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
        <h4 class="fw-bold text-uppercase mb-1">LAPORAN MUTASI & PERGERAKAN STOK BARANG</h4>
        <h6 class="text-muted mb-1">SISTEM INVENTORY INTERNAL KANTOR POS</h6>
        <p class="small text-muted mb-0">
            Periode: <strong>{{ date('d M Y', strtotime($startDate)) }}</strong> s/d <strong>{{ date('d M Y', strtotime($endDate)) }}</strong> &bull; Dicetak: {{ date('d F Y, H:i') }} WIB
        </p>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0" style="font-size: 0.85rem;">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 45px;">No</th>
                    <th>Waktu</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Tipe</th>
                    <th style="width: 90px;">Jumlah</th>
                    <th style="width: 80px;">Sebelum</th>
                    <th style="width: 80px;">Sesudah</th>
                    <th>Petugas</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $index => $m)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-nowrap">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                    <td class="fw-semibold">{{ $m->item->name ?? 'Barang telah dihapus' }}</td>
                    <td>{{ $m->item->category->name ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $m->type === 'IN' ? 'bg-success' : 'bg-danger' }}">
                            {{ $m->type }}
                        </span>
                    </td>
                    <td class="text-center fw-bold {{ $m->type === 'IN' ? 'text-success' : 'text-danger' }}">
                        {{ $m->type === 'IN' ? '+' : '-' }}{{ number_format($m->quantity) }}
                    </td>
                    <td class="text-center text-muted">{{ number_format($m->stock_before) }}</td>
                    <td class="text-center fw-bold">{{ number_format($m->stock_after) }}</td>
                    <td>{{ $m->user->name ?? '-' }}</td>
                    <td class="small text-muted">{{ $m->notes ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-4 text-muted">Tidak ada pergerakan stok dalam periode ini.</td>
                </tr>
                @endforelse
            </tbody>
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
