@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Manajemen Pengguna (User Management)</h4>
        <p class="text-muted small mb-0">Kelola akun staf, hak akses role (Admin, Petugas, Pimpinan), dan keamanan sistem</p>
    </div>
    <div>
        <a href="{{ route('users.create') }}" class="btn btn-pos-primary">
            <i class="fa-solid fa-user-plus me-1"></i> Tambah Pengguna
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau email pengguna..." value="{{ $search }}">
                </div>
            </div>

            <div class="col-md-4">
                <select name="role" class="form-select">
                    <option value="">-- Semua Role --</option>
                    <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin (Administrator Penuh)</option>
                    <option value="petugas" {{ $role === 'petugas' ? 'selected' : '' }}>Petugas (Operasional Stok)</option>
                    <option value="pimpinan" {{ $role === 'pimpinan' ? 'selected' : '' }}>Pimpinan (Monitoring & Laporan)</option>
                </select>
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-secondary">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
            </div>

            @if($search || $role)
            <div class="col-auto">
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
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
        @if($users->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th>Pengguna</th>
                        <th>Email</th>
                        <th>Hak Akses (Role)</th>
                        <th>Terdaftar Sejak</th>
                        <th style="width: 140px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $u)
                    <tr>
                        <td class="text-center text-muted">{{ $users->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center fw-bold text-dark" style="width: 34px; height: 34px; font-size: 0.8rem;">
                                    {{ strtoupper(substr($u->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $u->name }}</div>
                                    @if($u->id === Auth::id())
                                        <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">(Akun Anda)</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted">{{ $u->email }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $u->role === 'admin' ? 'bg-danger' : ($u->role === 'petugas' ? 'bg-primary' : 'bg-info') }} px-3 py-1">
                                <i class="fa-solid fa-shield-halved me-1"></i> {{ ucfirst($u->role) }}
                            </span>
                        </td>
                        <td class="text-muted small">
                            {{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('users.edit', $u) }}" class="btn btn-outline-primary" title="Edit Akun">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                                @if($u->id !== Auth::id())
                                <form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna {{ $u->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus Akun">
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
                Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari total {{ $users->total() }} pengguna
            </div>
            <div>
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @else
        <div class="empty-state">
            <i class="fa-solid fa-users-slash text-muted"></i>
            <h5 class="fw-bold text-dark">Pengguna Tidak Ditemukan</h5>
            <p class="text-muted small mb-3">Tidak ditemukan pengguna yang sesuai dengan filter.</p>
            <a href="{{ route('users.create') }}" class="btn btn-pos-primary btn-sm">
                <i class="fa-solid fa-user-plus me-1"></i> Tambah Pengguna Baru
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
