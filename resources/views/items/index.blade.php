@extends('layouts.app')

@section('title', 'Master Data Barang')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Master Data Barang</h4>
        <p class="text-muted small mb-0">Kelola daftar seluruh barang dan persediaan inventaris internal pos</p>
    </div>
    @if(Auth::user()->role === 'admin')
    <div>
        <a href="{{ route('items.create') }}" class="btn btn-pos-primary">
            <i class="fa-solid fa-plus me-1"></i> Tambah Barang
        </a>
    </div>
    @endif
</div>

<!-- Filters & Search -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('items.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, satuan, lokasi..." value="{{ $search }}">
                </div>
            </div>

            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <select name="stock_status" class="form-select">
                    <option value="">-- Semua Status Stok --</option>
                    <option value="aman" {{ $stockStatus === 'aman' ? 'selected' : '' }}>Stok Aman (> Min)</option>
                    <option value="menipis" {{ $stockStatus === 'menipis' ? 'selected' : '' }}>Stok Menipis (&le; Min)</option>
                    <option value="habis" {{ $stockStatus === 'habis' ? 'selected' : '' }}>Stok Habis (= 0)</option>
                </select>
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-secondary">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
            </div>

            @if($search || $categoryId || $stockStatus !== null)
            <div class="col-auto">
                <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-rotate-left me-1"></i> Reset
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

<!-- Items Table -->
<div class="card">
    <div class="card-body p-0">
        @if($items->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th class="text-center">Stok</th>
                        <th class="text-center">Min. Stok</th>
                        <th>Lokasi</th>
                        <th>Kondisi Stok</th>
                        <th>Status</th>
                        <th style="width: 140px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $index => $item)
                    <tr>
                        <td class="text-center text-muted">{{ $items->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->name }}</div>
                            <div class="text-muted small">Satuan: {{ $item->unit }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $item->category->name ?? '-' }}</span>
                        </td>
                        <td class="text-center fw-bold {{ $item->stock <= 0 ? 'text-danger' : ($item->stock <= $item->minimum_stock ? 'text-warning' : 'text-success') }}">
                            {{ number_format($item->stock) }} {{ $item->unit }}
                        </td>
                        <td class="text-center text-muted">
                            {{ number_format($item->minimum_stock) }} {{ $item->unit }}
                        </td>
                        <td>
                            @if($item->location)
                                <span class="badge bg-secondary-subtle text-secondary border">
                                    <i class="fa-solid fa-location-dot me-1"></i>{{ $item->location }}
                                </span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $item->stock_status_badge_class }}">
                                {{ $item->stock_status_label }}
                            </span>
                        </td>
                        <td>
                            @if($item->status)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('items.show', $item) }}" class="btn btn-outline-info" title="Detail & Riwayat">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                                @if(Auth::user()->role === 'admin')
                                <a href="{{ route('items.edit', $item) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang {{ $item->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Menampilkan {{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }} dari total {{ $items->total() }} barang
            </div>
            <div>
                {{ $items->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @else
        <div class="empty-state">
            <i class="fa-solid fa-boxes-stacked"></i>
            <h5 class="fw-bold text-dark">Belum Ada Data Barang</h5>
            <p class="text-muted small mb-3">Tidak ditemukan barang yang sesuai dengan filter atau kata kunci pencarian.</p>
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('items.create') }}" class="btn btn-pos-primary btn-sm">
                <i class="fa-solid fa-plus me-1"></i> Tambah Data Barang
            </a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
