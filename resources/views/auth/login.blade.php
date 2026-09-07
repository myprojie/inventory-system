@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5 col-xl-4">
        <div class="text-center mb-4 text-white">
            <div class="d-inline-flex align-items-center justify-content-center bg-warning text-dark rounded-circle p-3 mb-2 shadow">
                <i class="fa-solid fa-boxes-stacked fs-2 text-dark"></i>
            </div>
            <h3 class="fw-bold mb-1">INVENTORY SYSTEM</h3>
            <p class="text-white-50 small mb-0">Kantor Pos Internal Inventory</p>
        </div>

        <div class="card login-card p-4 p-sm-4">
            <div class="mb-3 text-center">
                <h5 class="fw-bold text-dark mb-1">Masuk ke Akun</h5>
                <p class="text-muted small">Silakan masukkan email dan password Anda</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show small" role="alert">
                    <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold">Email Pengguna</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-regular fa-envelope text-muted"></i></span>
                        <input type="email" class="form-control border-start-0 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="admin@pos.co.id" required autofocus>
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label small fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                        <input type="password" class="form-control border-start-0 @error('password') is-invalid @enderror" id="password" name="password" placeholder="••••••••" required>
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label small text-muted" for="remember">Ingat Saya di Perangkat Ini</label>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-pos">
                        <i class="fa-solid fa-arrow-right-to-bracket me-2"></i> Masuk Sekarang
                    </button>
                </div>
            </form>

            <div class="border-top pt-3 text-center">
                <div class="text-muted small">
                    <strong>Demo Akun:</strong><br>
                    Admin: <code>admin@pos.co.id</code> / <code>password</code><br>
                    Petugas: <code>petugas@pos.co.id</code> / <code>password</code>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
