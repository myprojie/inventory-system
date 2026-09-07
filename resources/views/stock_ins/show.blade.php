@extends('layouts.app')

@section('title', 'Detail Stok Masuk - ' . $stockIn->transaction_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Detail Transaksi Stok Masuk</h4>
        <p class="text-muted small mb-0">Rincian nomor transaksi: <strong class="font-monospace text-success">{{ $stockIn->transaction_number }}</strong></p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('stock-ins.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn btn-outline-dark btn-print-hide">
            <i class="fa-solid fa-print me-1"></i> Cetak Bukti
        </button>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold"><i class="fa-solid fa-circle-info text-success me-2"></i>Informasi Penerimaan</h6>
            </div>
            <div class="card-body p-4">
                <div class="list-group list-group-flush small">
                    <div class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted">No. Transaksi:</span>
                        <span class="fw-bold font-monospace text-success">{{ $stockIn->transaction_number }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted">Tanggal:</span>
                        <span class="fw-medium text-dark">{{ $stockIn->transaction_date->format('d F Y') }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted">Petugas:</span>
                        <span class="fw-medium text-dark">{{ $stockIn->user->name ?? '-' }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted">Waktu Pencatatan:</span>
                        <span class="text-dark">{{ $stockIn->created_at ? $stockIn->created_at->format('d M Y H:i') : '-' }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted">Total Jumlah Masuk:</span>
                        <span class="badge bg-success fs-6">{{ $stockIn->details->sum('quantity') }} unit</span>
                    </div>
                </div>

                @if($stockIn->notes)
                <div class="mt-3 p-3 bg-light rounded-3 small">
                    <strong class="text-muted d-block mb-1">Catatan:</strong>
                    <p class="mb-0 text-dark">{{ $stockIn->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold"><i class="fa-solid fa-list-check text-primary me-2"></i>Rincian Barang Diterima</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Satuan</th>
                                <th class="text-center" style="width: 150px;">Jumlah Masuk</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockIn->details as $index => $detail)
                            <tr>
                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $detail->item->name ?? 'Barang telah dihapus' }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $detail->item->category->name ?? '-' }}</span>
                                </td>
                                <td class="text-muted">{{ $detail->item->unit ?? '-' }}</td>
                                <td class="text-center fw-bold text-success fs-6">
                                    +{{ number_format($detail->quantity) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="4" class="text-end">Total Keseluruhan:</td>
                                <td class="text-center text-success fs-6">+{{ number_format($stockIn->details->sum('quantity')) }} unit</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
