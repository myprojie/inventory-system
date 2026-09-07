<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Inventory System Kantor Pos</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --pos-primary: #ff6600;
            --pos-primary-hover: #e65c00;
            --pos-dark: #0f172a;
            --pos-sidebar: #1e293b;
            --pos-sidebar-hover: #334155;
            --pos-sidebar-active: #ff6600;
            --pos-bg: #f8fafc;
            --pos-card-bg: #ffffff;
            --pos-border: #e2e8f0;
            --pos-text: #334155;
            --pos-text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--pos-bg);
            color: var(--pos-text);
            min-height: 100vh;
        }

        /* Sidebar Styling */
        #sidebar-wrapper {
            min-height: 100vh;
            width: 260px;
            background-color: var(--pos-sidebar);
            transition: margin 0.25s ease-out;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.15rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-brand i {
            color: var(--pos-primary);
            font-size: 1.5rem;
        }

        .sidebar-heading {
            padding: 1rem 1.5rem 0.35rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0.5rem 0.75rem;
            margin: 0;
        }

        .sidebar-menu li {
            margin-bottom: 0.2rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 0.9rem;
            color: #cbd5e1;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }

        .sidebar-link:hover {
            color: #ffffff;
            background-color: var(--pos-sidebar-hover);
        }

        .sidebar-link.active {
            color: #ffffff;
            background-color: var(--pos-sidebar-active);
            font-weight: 600;
        }

        .sidebar-link i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
        }

        /* Page Content */
        #page-content-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin 0.25s ease-out;
        }

        /* Top Navbar */
        .top-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid var(--pos-border);
            padding: 0.85rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 990;
        }

        .btn-pos-primary {
            background-color: var(--pos-primary);
            border-color: var(--pos-primary);
            color: #ffffff;
            font-weight: 500;
        }

        .btn-pos-primary:hover, .btn-pos-primary:focus {
            background-color: var(--pos-primary-hover);
            border-color: var(--pos-primary-hover);
            color: #ffffff;
        }

        .card {
            border: 1px solid var(--pos-border);
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            background-color: #ffffff;
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid var(--pos-border);
            padding: 1rem 1.25rem;
            font-weight: 600;
            border-top-left-radius: 0.75rem !important;
            border-top-right-radius: 0.75rem !important;
        }

        .table > :not(caption) > * > * {
            padding: 0.75rem 1rem;
            vertical-align: middle;
        }

        .table thead th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 600;
            font-size: 0.825rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border-bottom: 1px solid var(--pos-border);
        }

        .badge {
            font-weight: 600;
            padding: 0.4em 0.65em;
            font-size: 0.78rem;
        }

        .empty-state {
            padding: 3rem 1.5rem;
            text-align: center;
        }

        .empty-state i {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
        }

        /* Mobile responsiveness */
        @media (max-width: 991.98px) {
            #sidebar-wrapper {
                margin-left: -260px;
            }
            #sidebar-wrapper.show {
                margin-left: 0;
            }
            #page-content-wrapper {
                margin-left: 0;
            }
        }

        /* Print Media Query */
        @media print {
            #sidebar-wrapper, .top-navbar, .btn-print-hide, .pagination-container, .action-buttons {
                display: none !important;
            }
            #page-content-wrapper {
                margin-left: 0 !important;
                padding: 0 !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            body {
                background-color: #ffffff !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <aside id="sidebar-wrapper">
            <a href="{{ route('dashboard') }}" class="sidebar-brand">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Inventory Pos</span>
            </a>

            <div class="px-3 py-2 text-white border-bottom border-secondary border-opacity-25 d-flex align-items-center gap-2">
                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem;">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                </div>
                <div class="overflow-hidden">
                    <div class="fw-semibold text-truncate" style="font-size: 0.88rem;">{{ Auth::user()->name ?? 'User' }}</div>
                    <span class="badge {{ Auth::user()->role === 'admin' ? 'bg-danger' : (Auth::user()->role === 'petugas' ? 'bg-primary' : 'bg-info') }}" style="font-size: 0.7rem;">
                        {{ ucfirst(Auth::user()->role ?? 'petugas') }}
                    </span>
                </div>
            </div>

            <ul class="sidebar-menu mt-2">
                <li>
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-heading">Master Data</li>
                <li>
                    <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-tags"></i>
                        <span>Kategori</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('items.index') }}" class="sidebar-link {{ request()->routeIs('items.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-box-open"></i>
                        <span>Barang</span>
                    </a>
                </li>

                <li class="sidebar-heading">Transaksi</li>
                @if(in_array(Auth::user()->role, ['admin', 'petugas']))
                <li>
                    <a href="{{ route('stock-ins.index') }}" class="sidebar-link {{ request()->routeIs('stock-ins.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-arrow-down-to-bracket text-success"></i>
                        <span>Stok Masuk</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('stock-outs.index') }}" class="sidebar-link {{ request()->routeIs('stock-outs.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-arrow-up-from-bracket text-danger"></i>
                        <span>Stok Keluar</span>
                    </a>
                </li>
                @endif
                <li>
                    <a href="{{ route('stock-movements.index') }}" class="sidebar-link {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-arrows-spin text-warning"></i>
                        <span>Pergerakan Stok</span>
                    </a>
                </li>

                <li class="sidebar-heading">Laporan</li>
                <li>
                    <a href="{{ route('reports.stock') }}" class="sidebar-link {{ request()->routeIs('reports.stock') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-invoice"></i>
                        <span>Laporan Stok</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.index') || request()->routeIs('reports.stock-in') || request()->routeIs('reports.stock-out') || request()->routeIs('reports.movements') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Pusat Laporan</span>
                    </a>
                </li>

                @if(Auth::user()->role === 'admin')
                <li class="sidebar-heading">Administrasi</li>
                <li>
                    <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users-gear"></i>
                        <span>User Management</span>
                    </a>
                </li>
                @endif

                <li class="sidebar-heading">Akun</li>
                <li>
                    <a href="{{ route('profile') }}" class="sidebar-link {{ request()->routeIs('profile') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-circle"></i>
                        <span>Profil Saya</span>
                    </a>
                </li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" id="logout-form" class="d-none">
                        @csrf
                    </form>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="sidebar-link text-danger">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <!-- Top Navbar -->
            <nav class="top-navbar d-flex justify-content-between align-items-center">
                <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebar-toggle">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <div class="d-none d-md-flex align-items-center gap-2 text-muted" style="font-size: 0.88rem;">
                    <i class="fa-regular fa-building text-warning"></i>
                    <span>Sistem Inventory Internal Kantor Pos</span>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2 border" type="button" data-bs-toggle="dropdown">
                            <i class="fa-regular fa-user-circle text-primary"></i>
                            <span class="fw-medium">{{ Auth::user()->name ?? 'Akun' }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><h6 class="dropdown-header">{{ Auth::user()->email ?? '' }}</h6></li>
                            <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fa-regular fa-id-badge me-2"></i>Edit Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Keluar
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Main Content Container -->
            <main class="container-fluid p-3 p-md-4 flex-grow-1">
                <!-- Flash Alerts -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
                        <i class="fa-solid fa-circle-check fs-5"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
                        <i class="fa-solid fa-circle-exclamation fs-5"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
                        <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                        <div>{{ session('warning') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fa-solid fa-circle-xmark fs-5"></i>
                            <strong>Terdapat beberapa kesalahan:</strong>
                        </div>
                        <ul class="mb-0 ps-4">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-top py-3 px-4 text-center text-muted" style="font-size: 0.8rem;">
                &copy; {{ date('Y') }} Sistem Inventory Internal Kantor Pos &bull; Dikembangkan dengan Laravel 13 & Bootstrap 5
            </footer>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebar-toggle')?.addEventListener('click', function () {
            document.getElementById('sidebar-wrapper').classList.toggle('show');
        });
    </script>
    @stack('scripts')
</body>
</html>
