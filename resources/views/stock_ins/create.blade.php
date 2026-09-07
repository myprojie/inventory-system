@extends('layouts.app')

@section('title', 'Input Stok Masuk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Input Transaksi Stok Masuk</h4>
        <p class="text-muted small mb-0">Catat penambahan atau penerimaan barang ke dalam stok gudang</p>
    </div>
    <a href="{{ route('stock-ins.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<form action="{{ route('stock-ins.store') }}" method="POST" id="stock-in-form">
    @csrf

    <div class="row g-4">
        <!-- Header Information -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold"><i class="fa-solid fa-file-invoice text-success me-2"></i>Informasi Transaksi</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="transaction_number" class="form-label small fw-semibold">Nomor Transaksi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control font-monospace @error('transaction_number') is-invalid @enderror" id="transaction_number" name="transaction_number" value="{{ old('transaction_number', $suggestedNumber) }}" required>
                        @error('transaction_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="transaction_date" class="form-label small fw-semibold">Tanggal Penerimaan <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('transaction_date') is-invalid @enderror" id="transaction_date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                        @error('transaction_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Petugas / User</label>
                        <input type="text" class="form-control bg-light" value="{{ Auth::user()->name }}" readonly disabled>
                    </div>

                    <div class="mb-0">
                        <label for="notes" class="form-label small fw-semibold">Catatan / Keterangan (Opsional)</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4" placeholder="Keterangan pengadaan barang...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Barang Items -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="fa-solid fa-boxes-packing text-primary me-2"></i>Daftar Barang Masuk</h6>
                    <button type="button" class="btn btn-sm btn-outline-success" id="add-row-btn">
                        <i class="fa-solid fa-plus me-1"></i> Tambah Baris
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="items-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 250px;">Barang <span class="text-danger">*</span></th>
                                    <th style="width: 120px;" class="text-center">Stok Saat Ini</th>
                                    <th style="width: 140px;">Jumlah Masuk <span class="text-danger">*</span></th>
                                    <th style="width: 50px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="items-container">
                                <!-- Row template rendered via JS / Blade -->
                                <tr class="item-row">
                                    <td>
                                        <select name="items[0][item_id]" class="form-select item-select" required>
                                            <option value="">-- Pilih Barang --</option>
                                            @foreach($items as $item)
                                                <option value="{{ $item->id }}" data-stock="{{ $item->stock }}" data-unit="{{ $item->unit }}">
                                                    {{ $item->name }} ({{ $item->category->name ?? '-' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-center current-stock-cell text-muted fw-bold">-</td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" name="items[0][quantity]" class="form-control quantity-input" min="1" value="1" required>
                                            <span class="input-group-text unit-label text-muted small">Pcs</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn" disabled>
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info py-2 small d-flex align-items-center gap-2 mt-3">
                        <i class="fa-solid fa-circle-info fs-5"></i>
                        <div>Stok barang akan secara otomatis bertambah dan pergerakan stok dicatat ke sistem.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-4">
                        <a href="{{ route('stock-ins.index') }}" class="btn btn-light border">Batal</a>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="fa-solid fa-check me-1"></i> Simpan Transaksi Masuk
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let rowIndex = 1;
    const container = document.getElementById('items-container');
    const addBtn = document.getElementById('add-row-btn');

    function updateRemoveButtons() {
        const rows = container.querySelectorAll('.item-row');
        rows.forEach(row => {
            const btn = row.querySelector('.remove-row-btn');
            btn.disabled = rows.length <= 1;
        });
    }

    function attachSelectListener(select) {
        select.addEventListener('change', function () {
            const row = this.closest('.item-row');
            const selected = this.options[this.selectedIndex];
            const stock = selected.getAttribute('data-stock') || '-';
            const unit = selected.getAttribute('data-unit') || 'Pcs';

            row.querySelector('.current-stock-cell').textContent = stock !== '-' ? `${stock} ${unit}` : '-';
            row.querySelector('.unit-label').textContent = unit;
        });
    }

    // Attach to initial row
    document.querySelectorAll('.item-select').forEach(attachSelectListener);

    addBtn.addEventListener('click', function () {
        const firstRow = container.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);

        // Update input names with new index
        const select = newRow.querySelector('.item-select');
        select.name = `items[${rowIndex}][item_id]`;
        select.value = '';

        const qty = newRow.querySelector('.quantity-input');
        qty.name = `items[${rowIndex}][quantity]`;
        qty.value = 1;

        newRow.querySelector('.current-stock-cell').textContent = '-';
        newRow.querySelector('.unit-label').textContent = 'Pcs';

        attachSelectListener(select);

        newRow.querySelector('.remove-row-btn').addEventListener('click', function () {
            newRow.remove();
            updateRemoveButtons();
        });

        container.appendChild(newRow);
        rowIndex++;
        updateRemoveButtons();
    });

    container.addEventListener('click', function (e) {
        if (e.target.closest('.remove-row-btn')) {
            const row = e.target.closest('.item-row');
            if (container.querySelectorAll('.item-row').length > 1) {
                row.remove();
                updateRemoveButtons();
            }
        }
    });

    updateRemoveButtons();
});
</script>
@endpush
