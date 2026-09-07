@extends('layouts.app')

@section('title', 'Master Data Kategori')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Master Data Kategori</h4>
        <p class="text-muted small mb-0">Kelola pengelompokan jenis barang inventaris internal kantor pos</p>
    </div>
    @if(Auth::user()->role === 'admin')
    <div>
        <a href="{{ route('categories.create') }}" class="btn btn-pos-primary">
            <i class="fa-solid fa-plus me-1"></i> Tambah Kategori
        </a>
    </div>
    @endif
</div>

<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('categories.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau deskripsi kategori..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary">
                    <i class="fa-solid fa-filter me-1"></i> Cari
                </button>
            </div>
            @if($search)
            <div class="col-auto">
                <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-rotate-left me-1"></i> Reset
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($categories->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;" class="text-center">No</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th class="text-center" style="width: 130px;">Jumlah Barang</th>
                        <th style="width: 160px;">Dibuat</th>
                        @if(Auth::user()->role === 'admin')
                        <th style="width: 150px;" class="text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $index => $category)
                    <tr>
                        <td class="text-center text-muted">{{ $categories->firstItem() + $index }}</td>
                        <td class="fw-bold text-dark">{{ $category->name }}</td>
                        <td class="text-muted">{{ $category->description ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">
                                <i class="fa-solid fa-box me-1 text-primary"></i>{{ $category->items_count }} item
                            </span>
                        </td>
                        <td class="text-muted small">{{ $category->created_at ? $category->created_at->format('d M Y H:i') : '-' }}</td>
                        @if(Auth::user()->role === 'admin')
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $category->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Menampilkan {{ $categories->firstItem() ?? 0 }} - {{ $categories->lastItem() ?? 0 }} dari total {{ $categories->total() }} kategori
            </div>
            <div>
                {{ $categories->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @else
        <div class="empty-state">
            <i class="fa-solid fa-tags"></i>
            <h5 class="fw-bold text-dark">Belum Ada Data Kategori</h5>
            <p class="text-muted small mb-3">Tidak ditemukan kategori yang sesuai dengan kriteria pencarian.</p>
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('categories.create') }}" class="btn btn-pos-primary btn-sm">
                <i class="fa-solid fa-plus me-1"></i> Tambah Data Kategori
            </a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
