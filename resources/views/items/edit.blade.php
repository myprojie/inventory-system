@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Edit Data Barang</h4>
        <p class="text-muted small mb-0">Perbarui informasi barang inventaris: <strong>{{ $item->name }}</strong></p>
    </div>
    <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Form Edit Barang</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('items.update', $item) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label small fw-semibold">Kategori Barang <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $item->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="name" class="form-label small fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $item->name) }}" maxlength="150" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="unit" class="form-label small fw-semibold">Satuan Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit" value="{{ old('unit', $item->unit) }}" maxlength="30" required>
                            @error('unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Stok Saat Ini (Fisik)</label>
                            <input type="text" class="form-control bg-light fw-bold" value="{{ $item->stock }} {{ $item->unit }}" readonly disabled>
                            <div class="form-text small text-muted">Perubahan stok dilakukan via menu Stok Masuk/Keluar.</div>
                        </div>

                        <div class="col-md-4">
                            <label for="minimum_stock" class="form-label small fw-semibold">Batas Minimum Stok <span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control @error('minimum_stock') is-invalid @enderror" id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', $item->minimum_stock) }}" required>
                            @error('minimum_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="location" class="form-label small fw-semibold">Lokasi Penyimpanan / Rak (Opsional)</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $item->location) }}" maxlength="100">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label small fw-semibold">Status Ketersediaan <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="1" {{ old('status', $item->status ? '1' : '0') == '1' ? 'selected' : '' }}>Aktif (Tersedia untuk transaksi)</option>
                                <option value="0" {{ old('status', $item->status ? '1' : '0') === '0' ? 'selected' : '' }}>Nonaktif (Tidak digunakan lagi)</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label small fw-semibold">Deskripsi / Spesifikasi Barang (Opsional)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $item->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2 border-top pt-3 mt-4">
                            <a href="{{ route('items.index') }}" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-pos-primary">
                                <i class="fa-solid fa-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
