@extends('layouts.app')

@section('title', 'Riwayat Pergerakan Stok')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Riwayat Pergerakan Stok (Stock Movements)</h4>
        <p class="text-muted small mb-0">Audit log seluruh mutasi stok masuk dan keluar inventaris internal kantor pos</p>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('stock-movements.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari keterangan / barang..." value="{{ $search }}">
                </div>
            </div>

            <div class="col-md-2">
                <select name="type" class="form-select">
                    <option value="">-- Semua Tipe --</option>
                    <option value="IN" {{ $type === 'IN' ? 'selected' : '' }}>Stok Masuk (IN)</option>
                    <option value="OUT" {{ $type === 'OUT' ? 'selected' : '' }}>Stok Keluar (OUT)</option>
                </select>
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
                <input type="date" name="start_date" class="form-control" title="Dari Tanggal" value="{{ $startDate }}">
            </div>

            <div class="col-md-2">
                <input type="date" name="end_date" class="form-control" title="Sampai Tanggal" value="{{ $endDate }}">
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-secondary">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
            </div>

            @if($search || $type || $itemId || $startDate || $endDate || $userId)
            <div class="col-auto">
                <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-secondary">
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
        @if($movements->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th>Waktu & Tanggal</th>
                        <th>Barang</th>
                        <th>Kategori</th>
                        <th class="text-center">Tipe</th>
                        <th class="text-center">Jumlah Mutasi</th>
                        <th class="text-center">Stok Sebelum</th>
                        <th class="text-center">Stok Sesudah</th>
                        <th>Petugas</th>
                        <th>Keterangan / Referensi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movements as $index => $m)
                    <tr>
                        <td class="text-center text-muted">{{ $movements->firstItem() + $index }}</td>
                        <td class="text-nowrap text-muted small">
                            <div>{{ $m->created_at->format('d M Y') }}</div>
                            <div class="text-muted">{{ $m->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="fw-bold text-dark">{{ $m->item->name ?? 'Barang telah dihapus' }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $m->item->category->name ?? '-' }}</span>
                        </td>
                        <td class="text-center">
                            @if($m->type === 'IN')
                                <span class="badge bg-success"><i class="fa-solid fa-arrow-down me-1"></i> MASUK</span>
                            @else
                                <span class="badge bg-danger"><i class="fa-solid fa-arrow-up me-1"></i> KELUAR</span>
                            @endif
                        </td>
                        <td class="text-center fw-bold {{ $m->type === 'IN' ? 'text-success' : 'text-danger' }} fs-6">
                            {{ $m->type === 'IN' ? '+' : '-' }}{{ number_format($m->quantity) }} {{ $m->item->unit ?? '' }}
                        </td>
                        <td class="text-center text-muted">{{ number_format($m->stock_before) }}</td>
                        <td class="text-center fw-bold text-dark">{{ number_format($m->stock_after) }}</td>
                        <td>
                            <span class="small"><i class="fa-regular fa-user text-muted me-1"></i>{{ $m->user->name ?? '-' }}</span>
                        </td>
                        <td class="small text-muted">
                            <div>{{ $m->notes ?? '-' }}</div>
                            @if($m->reference_type)
                                <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.7rem;">Ref: {{ $m->reference_type }} #{{ $m->reference_id }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Menampilkan {{ $movements->firstItem() ?? 0 }} - {{ $movements->lastItem() ?? 0 }} dari total {{ $movements->total() }} log pergerakan
            </div>
            <div>
                {{ $movements->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @else
        <div class="empty-state">
            <i class="fa-solid fa-arrows-spin text-warning"></i>
            <h5 class="fw-bold text-dark">Belum Ada Riwayat Pergerakan Stok</h5>
            <p class="text-muted small mb-0">Pergerakan stok akan tercatat otomatis saat transaksi stok masuk atau keluar dilakukan.</p>
        </div>
        @endif
    </div>
</div>
@endsection
