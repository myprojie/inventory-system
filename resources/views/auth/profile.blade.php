@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Profil Pengguna</h4>
        <p class="text-muted small mb-0">Kelola informasi data diri dan keamanan akun Anda</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card text-center p-4">
            <div class="rounded-circle bg-warning text-dark mx-auto d-flex align-items-center justify-content-center fw-bold fs-3 mb-3 shadow-sm" style="width: 80px; height: 80px;">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
            <p class="text-muted small mb-2">{{ $user->email }}</p>
            <div>
                <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : ($user->role === 'petugas' ? 'bg-primary' : 'bg-info') }} px-3 py-2">
                    <i class="fa-solid fa-shield-halved me-1"></i> Role: {{ ucfirst($user->role) }}
                </span>
            </div>
            <hr class="my-4">
            <div class="text-start small text-muted">
                <div class="d-flex justify-content-between mb-2">
                    <span>Terdaftar Pada:</span>
                    <span class="fw-medium text-dark">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Terakhir Update:</span>
                    <span class="fw-medium text-dark">{{ $user->updated_at ? $user->updated_at->format('d M Y H:i') : '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold"><i class="fa-regular fa-pen-to-square me-2 text-primary"></i>Perbarui Informasi Akun</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label small fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mt-4">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                <i class="fa-solid fa-lock me-2 text-warning"></i>Ubah Password (Opsional)
                            </h6>
                            <p class="text-muted small">Kosongkan jika Anda tidak bermaksud untuk mengganti password akun.</p>
                        </div>

                        <div class="col-12">
                            <label for="current_password" class="form-label small fw-semibold">Password Saat Ini</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" placeholder="Masukkan password saat ini jika ingin mengubah password">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label small fw-semibold">Password Baru</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Minimal 6 karakter">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label small fw-semibold">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Ketik ulang password baru">
                        </div>

                        <div class="col-12 text-end mt-4">
                            <button type="submit" class="btn btn-pos-primary px-4">
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
