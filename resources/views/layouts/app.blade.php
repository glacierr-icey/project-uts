<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#06b6d4">
    <title>@yield('title', 'Sistem Informasi Apotek') - Apotek Sehat Sehati Punggur</title>

    <!-- Bootstrap 5.3 CSS & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts: Inter + Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ==========================================================
           DESIGN TOKENS — BIRU CYAN (selaras halaman login)
           ========================================================== */
        :root {
            --cyan-50:  #ecfeff;
            --cyan-100: #cffafe;
            --cyan-200: #a5f3fc;
            --cyan-300: #67e8f9;
            --cyan-400: #22d3ee;
            --cyan-500: #06b6d4;
            --cyan-600: #0891b2;
            --cyan-700: #0e7490;
            --blue-700: #0369a1;
            --blue-900: #0c4a6e;
            --ink-900:  #0f172a;
            --ink-700:  #334155;
            --ink-500:  #64748b;
            --ink-400:  #94a3b8;
            --glass-bg:   rgba(255, 255, 255, 0.72);
            --glass-brd:  rgba(255, 255, 255, 0.85);
            --glass-shadow: 0 4px 24px rgba(8, 145, 178, 0.08), 0 1px 2px rgba(8, 145, 178, 0.06);
        }

        /* ==========================================================
           BODY + BACKGROUND AESTETIK (ORB BLUR / AURORA)
           ========================================================== */
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            color: var(--ink-700);
            background-color: #f2fafb;
            background-image:
                radial-gradient(1000px 620px at 88% -12%, rgba(103, 232, 249, 0.30), transparent 60%),
                radial-gradient(900px 600px at -8% 18%, rgba(165, 243, 252, 0.34), transparent 55%),
                radial-gradient(820px 520px at 52% 118%, rgba(204, 251, 241, 0.42), transparent 62%),
                linear-gradient(180deg, #f4fbff 0%, #f7fcfd 52%, #f1faf8 100%);
            background-attachment: fixed;
        }

        /* Bola cahaya cyan melayang di belakang konten */
        .bg-decor {
            position: fixed;
            inset: 0;
            z-index: -1;
            overflow: hidden;
            pointer-events: none;
        }
        .bg-decor .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            will-change: transform;
        }
        .orb-a {
            width: 460px; height: 460px;
            top: -150px; right: -110px;
            background: radial-gradient(circle, rgba(34, 211, 238, 0.34), transparent 70%);
            animation: orbFloatA 26s ease-in-out infinite alternate;
        }
        .orb-b {
            width: 420px; height: 420px;
            bottom: -170px; left: -130px;
            background: radial-gradient(circle, rgba(103, 232, 249, 0.28), transparent 70%);
            animation: orbFloatB 32s ease-in-out infinite alternate;
        }
        .orb-c {
            width: 300px; height: 300px;
            top: 42%; left: 56%;
            background: radial-gradient(circle, rgba(45, 212, 191, 0.20), transparent 70%);
            animation: orbFloatA 22s ease-in-out infinite alternate-reverse;
        }
        @keyframes orbFloatA { from { transform: translate(0, 0) scale(1); } to { transform: translate(-42px, 48px) scale(1.12); } }
        @keyframes orbFloatB { from { transform: translate(0, 0) scale(1); } to { transform: translate(48px, -42px) scale(1.1); } }

        ::selection { background: rgba(6, 182, 212, 0.25); color: var(--blue-900); }

        @media (prefers-reduced-motion: reduce) {
            .bg-decor .orb { animation: none !important; }
        }
        @media print { .bg-decor { display: none !important; } }

        /* ==========================================================
           GLASSMORPHISM CARDS
           ========================================================== */
        .card, .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(18px) saturate(150%);
            -webkit-backdrop-filter: blur(18px) saturate(150%);
            border: 1px solid var(--glass-brd);
            border-radius: 20px;
            box-shadow: var(--glass-shadow), inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }

        /* ==========================================================
           NAVBAR GLASS + AKSEN CYAN
           ========================================================== */
        .navbar-glass {
            background: rgba(255, 255, 255, 0.74);
            backdrop-filter: blur(18px) saturate(170%);
            -webkit-backdrop-filter: blur(18px) saturate(170%);
            border-bottom: 1px solid rgba(6, 182, 212, 0.16);
            box-shadow: 0 4px 24px rgba(8, 145, 178, 0.07);
        }
        .brand-logo {
            width: 42px; height: 42px;
            border-radius: 13px;
            background: linear-gradient(135deg, var(--cyan-400), var(--blue-700));
            color: #fff;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.25rem;
            box-shadow: 0 8px 20px rgba(6, 182, 212, 0.40), inset 0 1px 1px rgba(255, 255, 255, 0.4);
        }
        .brand-text .brand-name {
            display: block;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 14.5px;
            color: var(--ink-900);
            line-height: 1.15;
        }
        .brand-text .brand-sub {
            display: block;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: var(--cyan-700);
        }
        .navbar-glass .nav-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 15px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 13.5px;
            color: var(--ink-500);
            transition: all 0.2s ease;
        }
        .navbar-glass .nav-link:hover {
            color: var(--cyan-700);
            background: rgba(6, 182, 212, 0.09);
        }
        .navbar-glass .nav-link.active {
            color: #fff;
            background: linear-gradient(135deg, var(--cyan-400), var(--cyan-600));
            box-shadow: 0 6px 16px rgba(6, 182, 212, 0.35);
        }

        /* Zona user di kanan navbar */
        .user-avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--cyan-400), var(--cyan-600));
            color: #fff;
            font-weight: 700;
            font-size: 15px;
            display: inline-flex; align-items: center; justify-content: center;
            box-shadow: 0 6px 16px rgba(6, 182, 212, 0.35), inset 0 1px 1px rgba(255, 255, 255, 0.45);
        }
        .role-badge {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 999px;
            display: inline-block;
        }
        .role-admin    { background: rgba(244, 63, 94, 0.12); color: #be123c; border: 1px solid rgba(244, 63, 94, 0.28); }
        .role-apoteker { background: rgba(6, 182, 212, 0.13); color: var(--cyan-700); border: 1px solid rgba(6, 182, 212, 0.32); }
        .role-kasir    { background: rgba(245, 158, 11, 0.13); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.30); }

        .btn-logout {
            display: inline-flex; align-items: center; gap: 6px;
            border: 1px solid rgba(148, 163, 184, 0.35);
            background: rgba(255, 255, 255, 0.6);
            color: var(--ink-500);
            border-radius: 10px;
            padding: 8px 13px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-logout:hover {
            color: #e11d48;
            border-color: rgba(225, 29, 72, 0.35);
            background: rgba(225, 29, 72, 0.06);
        }

        /* ==========================================================
           TOMBOL CYAN
           ========================================================== */
        .btn-cyan, .btn-pharmacy {
            background: linear-gradient(135deg, var(--cyan-400) 0%, var(--cyan-500) 45%, var(--cyan-600) 100%);
            color: #fff;
            border: none;
            font-weight: 600;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(6, 182, 212, 0.32), inset 0 1px 1px rgba(255, 255, 255, 0.35);
            transition: all 0.2s ease;
        }
        .btn-cyan:hover, .btn-pharmacy:hover {
            color: #fff;
            transform: translateY(-1px);
            filter: brightness(1.04);
            box-shadow: 0 10px 24px rgba(6, 182, 212, 0.42), inset 0 1px 1px rgba(255, 255, 255, 0.35);
        }
        .btn-cyan:disabled, .btn-pharmacy:disabled {
            opacity: 0.55;
            transform: none;
            box-shadow: none;
            color: #fff;
        }
        .btn-soft {
            background: rgba(255, 255, 255, 0.75);
            border: 1px solid rgba(148, 163, 184, 0.3);
            color: var(--ink-500);
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        .btn-soft:hover {
            color: var(--cyan-700);
            border-color: rgba(6, 182, 212, 0.45);
            background: #fff;
        }

        /* ==========================================================
           BADGE / CHIP / PILL
           ========================================================== */
        .badge-cat {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.10), rgba(8, 145, 178, 0.15));
            color: var(--cyan-700);
            border: 1px solid rgba(6, 182, 212, 0.26);
            border-radius: 999px;
            padding: 0.32em 0.8em;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.2px;
            white-space: nowrap;
        }
        .soft-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(255, 255, 255, 0.78);
            border: 1px solid rgba(148, 163, 184, 0.24);
            color: var(--ink-700);
            border-radius: 999px;
            padding: 6px 13px;
            font-size: 11.5px;
            font-weight: 600;
            white-space: nowrap;
        }
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-radius: 999px;
            padding: 0.35em 0.85em;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.2px;
            white-space: nowrap;
        }
        .pill-success { background: rgba(16, 185, 129, 0.12); color: #047857; border: 1px solid rgba(16, 185, 129, 0.28); }
        .pill-warning { background: rgba(245, 158, 11, 0.14); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.30); }
        .pill-danger  { background: rgba(239, 68, 68, 0.10); color: #b91c1c; border: 1px solid rgba(239, 68, 68, 0.28); }
        .pill-info    { background: rgba(6, 182, 212, 0.12); color: var(--cyan-700); border: 1px solid rgba(6, 182, 212, 0.30); }
        .pill-neutral { background: rgba(100, 116, 139, 0.10); color: var(--ink-500); border: 1px solid rgba(100, 116, 139, 0.24); }

        /* Kompatibilitas class lama (dipakai halaman Laporan) */
        .text-teal { color: var(--cyan-700) !important; }
        .badge-fefo {
            background: linear-gradient(135deg, #fef9c3, #fde68a);
            color: #854d0e;
            border: 1px solid #fcd34d;
            border-radius: 8px;
            font-weight: 600;
            font-size: 11px;
            padding: 0.3em 0.65em;
        }

        /* ==========================================================
           PAGE HEADER
           ========================================================== */
        .page-head {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .page-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.32rem;
            color: var(--ink-900);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 13px;
            line-height: 1.2;
        }
        .page-title .title-icon {
            flex-shrink: 0;
            width: 46px; height: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--cyan-400), var(--cyan-600));
            color: #fff;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
            box-shadow: 0 8px 20px rgba(6, 182, 212, 0.35), inset 0 1px 1px rgba(255, 255, 255, 0.4);
        }
        .page-sub {
            font-size: 12.5px;
            color: var(--ink-500);
            margin: 5px 0 0;
            max-width: 640px;
        }

        /* ==========================================================
           TABEL MODERN (GLASS + STICKY HEADER)
           ========================================================== */
        .table-modern { --bs-table-bg: transparent; margin-bottom: 0; }
        .table-modern > :not(caption) > * > * { border-bottom-color: rgba(148, 163, 184, 0.16); }
        .table-modern thead th {
            position: sticky;
            top: 0;
            z-index: 5;
            background: rgba(236, 254, 255, 0.94);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: var(--cyan-700);
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            border-bottom: 1px solid rgba(6, 182, 212, 0.20) !important;
            padding: 12px 14px;
            white-space: nowrap;
        }
        .table-modern tbody td { padding: 12px 14px; vertical-align: middle; }
        .table-modern tbody tr { transition: background 0.15s ease; }
        .table-modern tbody tr:hover { background: rgba(6, 182, 212, 0.05); }

        /* Scrollbar halus untuk area tabel yang scrollable */
        .scroll-area::-webkit-scrollbar { width: 8px; height: 8px; }
        .scroll-area::-webkit-scrollbar-thumb { background: rgba(8, 145, 178, 0.25); border-radius: 999px; }
        .scroll-area::-webkit-scrollbar-thumb:hover { background: rgba(8, 145, 178, 0.45); }
        .scroll-area::-webkit-scrollbar-track { background: transparent; }

        /* ==========================================================
           ALERT GLASS
           ========================================================== */
        main .alert {
            border-radius: 14px;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        main .alert-success { background: rgba(16, 185, 129, 0.12); border-color: rgba(16, 185, 129, 0.32); color: #047857; }
        main .alert-danger  { background: rgba(239, 68, 68, 0.10); border-color: rgba(239, 68, 68, 0.30); color: #b91c1c; }

        /* Form control aksen cyan */
        .form-control:focus, .form-select:focus {
            border-color: var(--cyan-500);
            box-shadow: 0 0 0 3.5px rgba(6, 182, 212, 0.15);
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Lapisan background estetik: bola cahaya cyan dengan blur -->
    <div class="bg-decor" aria-hidden="true">
        <div class="orb orb-a"></div>
        <div class="orb orb-b"></div>
        <div class="orb orb-c"></div>
    </div>

    <!-- Top Navigation Bar (Glassmorphism + Aksen Cyan) -->
    <nav class="navbar navbar-expand-lg navbar-glass sticky-top py-2 mb-4">
        <div class="container-fluid px-3 px-xl-4">
            <a class="navbar-brand d-flex align-items-center gap-2 py-0" href="{{ url('/') }}">
                <span class="brand-logo"><i class="bi bi-heart-pulse-fill"></i></span>
                <span class="brand-text">
                    <span class="brand-name">Apotek Sehat Sehati</span>
                    <span class="brand-sub">PUNGGUR · SISTEM KEFARMASIAN</span>
                </span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Buka menu navigasi">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                @auth
                    <ul class="navbar-nav mx-lg-auto my-3 my-lg-0 gap-1 gap-lg-2">
                        @if (in_array(Auth::user()->role, ['kasir', 'admin']))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}" href="{{ route('pos.index') }}">
                                    <i class="bi bi-cart3"></i> Kasir / POS
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                                    <i class="bi bi-bag-check"></i> Pesanan Online
                                    @php $pendingOnlineCount = \App\Models\SalesTransaction::where('order_channel', 'online')->where('payment_status', 'pending')->count(); @endphp
                                    @if ($pendingOnlineCount > 0)
                                        <span class="badge rounded-pill bg-warning text-dark" style="font-size: 9.5px;">{{ $pendingOnlineCount }}</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                        @if (in_array(Auth::user()->role, ['apoteker', 'admin']))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                    <i class="bi bi-boxes"></i> Stok Obat &amp; Batch
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.sales') }}">
                                    <i class="bi bi-bar-chart-line"></i> Laporan Penjualan
                                </a>
                            </li>
                        @endif
                    </ul>

                    <div class="d-flex align-items-center gap-2 gap-xl-3 user-zone">
                        <span class="user-avatar" title="{{ Auth::user()->name }}">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        <div class="d-none d-md-block lh-sm">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-semibold text-dark" style="font-size: 13.5px;">{{ Auth::user()->name }}</span>
                                @if (Auth::user()->role === 'admin')
                                    <span class="role-badge role-admin">Owner &amp; Admin</span>
                                @elseif (Auth::user()->role === 'apoteker')
                                    <span class="role-badge role-apoteker">Apoteker</span>
                                @else
                                    <span class="role-badge role-kasir">Kasir</span>
                                @endif
                            </div>
                            <small class="text-muted" style="font-size: 11.5px;">{{ Auth::user()->email }}</small>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn-logout" title="Keluar dari sistem">
                                <i class="bi bi-box-arrow-right"></i> <span class="d-none d-sm-inline">Logout</span>
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="container-fluid px-3 px-xl-4 pb-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Terdapat kesalahan input:
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Scripts: Bootstrap 5.3 & SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>
