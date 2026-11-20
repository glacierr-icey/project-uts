<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0891b2">
    <title>@yield('title', 'Belanja Obat Online') - {{ config('pharmacy.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">

    <style>
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
            --emerald:  #10b981;
            --amber:    #f59e0b;
            --rose:     #f43f5e;
            --portal-bg: #f4fbfc;
            --glass-bg: rgba(255, 255, 255, 0.82);
            --glass-brd: rgba(255, 255, 255, 0.9);
            --shadow-soft: 0 10px 30px rgba(8, 145, 178, 0.08), 0 2px 6px rgba(8, 145, 178, 0.04);
            --shadow-hover: 0 18px 44px rgba(8, 145, 178, 0.16), 0 4px 12px rgba(8, 145, 178, 0.06);
        }

        * { box-sizing: border-box; }

        body.portal-body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            color: var(--ink-700);
            background-color: var(--portal-bg);
            background-image:
                radial-gradient(900px 540px at 92% -6%, rgba(103, 232, 249, 0.28), transparent 62%),
                radial-gradient(780px 520px at -6% 12%, rgba(165, 243, 252, 0.30), transparent 58%),
                radial-gradient(720px 460px at 54% 110%, rgba(204, 251, 241, 0.36), transparent 62%),
                linear-gradient(180deg, #f8fdff 0%, #f5fcfd 55%, #f2fbf8 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        ::selection { background: rgba(6, 182, 212, 0.22); color: var(--blue-900); }

        .portal-topbar {
            background: linear-gradient(90deg, var(--blue-900), var(--cyan-700) 55%, var(--cyan-600));
            color: rgba(255, 255, 255, 0.92);
            font-size: 11.5px;
            letter-spacing: 0.2px;
            padding: 7px 0;
        }
        .portal-topbar a { color: #fff; text-decoration: none; font-weight: 600; }
        .portal-topbar i { opacity: .9; }

        .portal-navbar {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(18px) saturate(170%);
            -webkit-backdrop-filter: blur(18px) saturate(170%);
            border-bottom: 1px solid rgba(6, 182, 212, 0.14);
            box-shadow: 0 6px 24px rgba(8, 145, 178, 0.07);
        }
        .portal-brand { display: inline-flex; align-items: center; gap: 11px; text-decoration: none; }
        .portal-brand-mark {
            width: 44px; height: 44px; border-radius: 14px;
            background: linear-gradient(135deg, var(--cyan-400), var(--blue-700));
            color: #fff; display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.35rem;
            box-shadow: 0 8px 20px rgba(6, 182, 212, 0.35), inset 0 1px 1px rgba(255, 255, 255, .4);
            flex-shrink: 0;
        }
        .portal-brand-text { line-height: 1.12; }
        .portal-brand-name {
            display: block; font-family: 'Poppins', sans-serif; font-weight: 700;
            font-size: 15px; color: var(--ink-900);
        }
        .portal-brand-sub {
            display: block; font-size: 9.5px; font-weight: 700; letter-spacing: 1.3px;
            color: var(--cyan-700); text-transform: uppercase; margin-top: 2px;
        }
        .portal-navbar .nav-link {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 8px 15px; border-radius: 999px;
            font-weight: 600; font-size: 13.5px; color: var(--ink-500);
            transition: all .18s ease;
        }
        .portal-navbar .nav-link:hover { color: var(--cyan-700); background: rgba(6, 182, 212, 0.08); }
        .portal-navbar .nav-link.active {
            color: #fff; background: linear-gradient(135deg, var(--cyan-400), var(--cyan-600));
            box-shadow: 0 6px 16px rgba(6, 182, 212, 0.32);
        }
        .btn-portal {
            background: linear-gradient(135deg, var(--cyan-400) 0%, var(--cyan-500) 45%, var(--cyan-600) 100%);
            color: #fff; border: none; font-weight: 700; border-radius: 13px;
            padding: 11px 20px;
            box-shadow: 0 8px 20px rgba(6, 182, 212, 0.30), inset 0 1px 1px rgba(255, 255, 255, .35);
            transition: all .2s ease;
        }
        .btn-portal:hover, .btn-portal:focus {
            color: #fff; transform: translateY(-1px); filter: brightness(1.04);
            box-shadow: 0 12px 26px rgba(6, 182, 212, 0.4);
        }
        .btn-portal:disabled { opacity: .55; transform: none; box-shadow: none; color: #fff; }

        /* Tombol konfirmasi WhatsApp dibuat lebih menonjol & area klik besar. */
        .btn-wa-portal {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: #fff; border: none; font-weight: 800; border-radius: 13px;
            padding: 14px 20px; font-size: 13px; line-height: 1.2;
            box-shadow: 0 10px 22px rgba(22, 163, 74, .28), inset 0 1px 1px rgba(255, 255, 255, .28);
            transition: all .2s ease;
        }
        .btn-wa-portal:hover, .btn-wa-portal:focus {
            color: #fff; transform: translateY(-1px); filter: brightness(1.04);
            box-shadow: 0 14px 28px rgba(22, 163, 74, .38);
        }

        .btn-portal-outline {
            border: 1.5px solid rgba(6, 182, 212, 0.4);
            background: rgba(255, 255, 255, 0.78);
            color: var(--cyan-700); font-weight: 700; border-radius: 13px;
            padding: 10px 18px; transition: all .2s ease;
        }
        .btn-portal-outline:hover { background: #fff; border-color: var(--cyan-500); color: var(--cyan-700); transform: translateY(-1px); }
        .btn-cart {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(6, 182, 212, 0.10);
            border: 1px solid rgba(6, 182, 212, 0.26);
            color: var(--cyan-700); font-weight: 700; font-size: 13px;
            border-radius: 999px; padding: 8px 12px 8px 14px;
            transition: all .18s ease; position: relative;
        }
        .btn-cart:hover { background: rgba(6, 182, 212, 0.16); color: var(--cyan-700); }
        .cart-badge {
            min-width: 21px; height: 21px; padding: 0 6px;
            border-radius: 999px; background: linear-gradient(135deg, var(--cyan-500), var(--blue-700));
            color: #fff; font-size: 10.5px; font-weight: 800;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .cart-badge.is-empty { background: rgba(100, 116, 139, 0.35); }
        .btn-login {
            display: inline-flex; align-items: center; gap: 7px;
            background: #fff; border: 1px solid rgba(148, 163, 184, 0.36);
            color: var(--ink-500); font-weight: 700; font-size: 13px;
            border-radius: 11px; padding: 8px 13px; transition: all .18s ease;
        }
        .btn-login:hover { color: var(--cyan-700); border-color: rgba(6, 182, 212, 0.5); background: var(--cyan-50); }

        .portal-main { flex: 1 0 auto; padding-bottom: 3rem; }
        .section-eyebrow {
            display: inline-flex; align-items: center; gap: 7px;
            font-size: 11px; font-weight: 800; letter-spacing: 1.2px; text-transform: uppercase;
            color: var(--cyan-700);
        }
        .section-title {
            font-family: 'Poppins', sans-serif; font-weight: 700; color: var(--ink-900);
            font-size: clamp(1.35rem, 2.2vw, 1.85rem); margin: 6px 0 6px;
        }
        .section-sub { color: var(--ink-500); font-size: 13.5px; max-width: 640px; margin-bottom: 0; }

        .portal-card {
            background: var(--glass-bg);
            backdrop-filter: blur(18px) saturate(150%);
            -webkit-backdrop-filter: blur(18px) saturate(150%);
            border: 1px solid var(--glass-brd);
            border-radius: 20px;
            box-shadow: var(--shadow-soft);
        }
        .soft-chip {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(148, 163, 184, 0.22);
            color: var(--ink-700); border-radius: 999px;
            padding: 6px 12px; font-size: 11.5px; font-weight: 600; white-space: nowrap;
        }
        .badge-soft {
            display: inline-flex; align-items: center; gap: 5px;
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.10), rgba(8, 145, 178, 0.16));
            color: var(--cyan-700); border: 1px solid rgba(6, 182, 212, 0.26);
            border-radius: 999px; padding: 4px 10px; font-size: 10.5px; font-weight: 700;
            letter-spacing: .2px; white-space: nowrap;
        }
        .badge-success-soft { background: rgba(16, 185, 129, .12); color: #047857; border: 1px solid rgba(16, 185, 129, .28); }
        .badge-warning-soft { background: rgba(245, 158, 11, .14); color: #b45309; border: 1px solid rgba(245, 158, 11, .30); }
        .badge-danger-soft { background: rgba(244, 63, 94, .10); color: #be123c; border: 1px solid rgba(244, 63, 94, .28); }

        .form-control, .form-select {
            border-radius: 12px; border: 1.5px solid rgba(148, 163, 184, 0.30);
            padding: 10px 13px; font-size: 13.5px; background-color: rgba(255, 255, 255, 0.88);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--cyan-500);
            box-shadow: 0 0 0 3.5px rgba(6, 182, 212, 0.14);
        }
        .form-label { font-size: 12.5px; font-weight: 700; color: var(--ink-700); margin-bottom: 6px; }
        .form-text { font-size: 11px; }

        /* ================= PRODUK ================= */
        .product-card {
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 18px;
            padding: 16px;
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 16px rgba(8, 145, 178, 0.05);
            transition: all .2s ease;
            position: relative;
            overflow: hidden;
        }
        .product-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); border-color: rgba(6, 182, 212, 0.35); }
        .product-icon {
            width: 48px; height: 48px; border-radius: 14px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.45rem; margin-bottom: 12px;
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.12), rgba(8, 145, 178, 0.18));
            color: var(--cyan-700);
        }
        .product-cat { font-size: 10px; font-weight: 800; letter-spacing: .8px; text-transform: uppercase; color: var(--cyan-700); }
        .product-name { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 14.5px; color: var(--ink-900); margin: 4px 0 2px; line-height: 1.3; }
        .product-unit { font-size: 11.5px; color: var(--ink-400); }
        .product-price { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 1.16rem; color: var(--cyan-700); }
        .product-stock { font-size: 11px; font-weight: 600; color: var(--ink-500); }
        .stock-dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        .stock-safe { background: var(--emerald); }
        .stock-low { background: var(--amber); }
        .stock-empty { background: var(--rose); }
        .btn-add-cart {
            background: linear-gradient(135deg, var(--cyan-500), var(--cyan-700));
            color: #fff; border: none; border-radius: 11px;
            font-size: 12.5px; font-weight: 700; padding: 9px 12px;
            transition: all .18s ease; width: 100%;
        }
        .btn-add-cart:hover { color: #fff; filter: brightness(1.06); box-shadow: 0 8px 18px rgba(6, 182, 212, .28); }
        .btn-add-cart:disabled { background: rgba(148, 163, 184, 0.35); color: #fff; cursor: not-allowed; box-shadow: none; }

        /* ================= CART OFFCANVAS ================= */
        .cart-offcanvas { width: min(420px, 96vw) !important; }
        .cart-offcanvas .offcanvas-header { border-bottom: 1px solid rgba(148, 163, 184, 0.18); }
        .cart-offcanvas .offcanvas-title { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1rem; color: var(--ink-900); }
        .cart-item {
            display: flex; gap: 11px; align-items: flex-start;
            padding: 12px 0; border-bottom: 1px dashed rgba(148, 163, 184, 0.26);
        }
        .cart-item-icon {
            width: 40px; height: 40px; border-radius: 11px; flex-shrink: 0;
            display: inline-flex; align-items: center; justify-content: center;
            background: rgba(6, 182, 212, 0.10); color: var(--cyan-700); font-size: 1rem;
        }
        .cart-item-name { font-weight: 700; font-size: 12.5px; color: var(--ink-900); line-height: 1.3; }
        .cart-item-meta { font-size: 10.5px; color: var(--ink-400); }
        .cart-item-price { font-weight: 700; font-size: 12px; color: var(--cyan-700); }
        .qty-control { display: inline-flex; align-items: center; gap: 2px; border: 1px solid rgba(148, 163, 184, .28); border-radius: 9px; overflow: hidden; }
        .qty-btn { border: none; background: rgba(6, 182, 212, .08); color: var(--cyan-700); width: 27px; height: 27px; font-size: 12px; font-weight: 800; display: inline-flex; align-items: center; justify-content: center; }
        .qty-btn:hover { background: rgba(6, 182, 212, .18); }
        .qty-value { min-width: 28px; text-align: center; font-size: 12px; font-weight: 700; }
        .cart-remove { border: none; background: transparent; color: var(--ink-400); font-size: 13px; padding: 2px 4px; }
        .cart-remove:hover { color: var(--rose); }
        .cart-footer { border-top: 1px solid rgba(148, 163, 184, 0.18); padding-top: 14px; margin-top: auto; }
        .cart-total-label { font-size: 12.5px; color: var(--ink-500); font-weight: 600; }
        .cart-total-value { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 1.3rem; color: var(--cyan-700); }
        .cart-empty { text-align: center; padding: 42px 12px; color: var(--ink-400); }
        .cart-empty i { font-size: 2.6rem; opacity: .5; }

        /* ================= TOAST ================= */
        .portal-toast {
            position: fixed; z-index: 2000; right: 18px; bottom: 18px;
            display: flex; align-items: center; gap: 10px;
            background: #fff; border: 1px solid rgba(6, 182, 212, 0.25);
            border-radius: 14px; padding: 12px 16px;
            box-shadow: 0 16px 40px rgba(8, 145, 178, 0.18);
            font-size: 13px; font-weight: 600; color: var(--ink-700);
            opacity: 0; transform: translateY(12px); transition: all .25s ease;
            max-width: 360px;
        }
        .portal-toast.show { opacity: 1; transform: translateY(0); }
        .portal-toast .toast-icon { color: var(--emerald); font-size: 1.1rem; }
        .portal-toast.is-warning { border-color: rgba(245, 158, 11, .3); }
        .portal-toast.is-warning .toast-icon { color: var(--amber); }

        /* ================= FOOTER ================= */
        .portal-footer {
            margin-top: auto;
            background: linear-gradient(160deg, #072f3f 0%, #0b4e5e 52%, #0e7490 100%);
            color: rgba(255, 255, 255, 0.76);
            padding: 46px 0 0;
            font-size: 12.5px;
        }
        .portal-footer h6 { color: #fff; font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 13px; letter-spacing: .3px; margin-bottom: 14px; }
        .portal-footer a { color: rgba(255, 255, 255, 0.72); text-decoration: none; transition: color .15s ease; }
        .portal-footer a:hover { color: #fff; }
        .footer-brand { display: inline-flex; align-items: center; gap: 11px; margin-bottom: 12px; }
        .footer-brand .portal-brand-name { color: #fff; }
        .footer-brand .portal-brand-sub { color: var(--cyan-200); }
        .footer-list { list-style: none; padding: 0; margin: 0; display: grid; gap: 9px; }
        .footer-list li { display: flex; gap: 9px; align-items: flex-start; }
        .footer-list i { color: var(--cyan-300); margin-top: 2px; }
        .footer-bottom { border-top: 1px solid rgba(255, 255, 255, 0.13); margin-top: 32px; padding: 16px 0; font-size: 11.5px; color: rgba(255, 255, 255, 0.55); }
        .footer-pay { display: inline-flex; align-items: center; gap: 6px; padding: 5px 9px; border-radius: 8px; background: rgba(255, 255, 255, 0.09); font-size: 10.5px; font-weight: 700; color: #fff; }

        .alert-portal { border-radius: 14px; font-size: 13px; border: none; }
        .alert-portal.alert-success { background: rgba(16, 185, 129, 0.12); color: #047857; }
        .alert-portal.alert-danger { background: rgba(244, 63, 94, 0.10); color: #be123c; }

        @media (max-width: 991.98px) {
            .portal-navbar .navbar-collapse { padding-top: 12px; }
            .portal-navbar .nav-link { width: 100%; }
        }
        @media (max-width: 575.98px) {
            .portal-topbar { font-size: 10.5px; }
            .portal-brand-name { font-size: 13.5px; }
            .portal-brand-sub { font-size: 8px; letter-spacing: .8px; }
        }
    </style>
    @stack('styles')
</head>
<body class="portal-body">
    <div class="portal-topbar">
        <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span><i class="bi bi-truck me-1"></i>Bisa ambil di toko atau dikirim ke alamat Anda</span>
            <span class="d-none d-md-inline">
                <i class="bi bi-clock me-1"></i>Buka setiap hari &middot;
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', config('pharmacy.phone')) }}"><i class="bi bi-telephone me-1"></i>{{ config('pharmacy.phone') }}</a>
            </span>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg portal-navbar sticky-top py-2">
        <div class="container">
            <a class="portal-brand" href="{{ route('portal.index') }}">
                <span class="portal-brand-mark"><i class="bi bi-capsule"></i></span>
                <span class="portal-brand-text">
                    <span class="portal-brand-name">{{ config('pharmacy.name') }}</span>
                    <span class="portal-brand-sub">Belanja Obat Online &middot; Punggur</span>
                </span>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#portalNavbar"
                    aria-controls="portalNavbar" aria-expanded="false" aria-label="Buka menu">
                <i class="bi bi-list fs-2 text-secondary"></i>
            </button>

            <div class="collapse navbar-collapse" id="portalNavbar">
                <ul class="navbar-nav mx-auto my-2 my-lg-0 gap-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('portal.index') || request()->routeIs('home') ? 'active' : '' }}" href="{{ route('portal.index') }}">
                            <i class="bi bi-house-door"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('portal.index') }}#katalog">
                            <i class="bi bi-grid"></i> Katalog
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('portal.about') ? 'active' : '' }}" href="{{ route('portal.about') }}">
                            <i class="bi bi-shop"></i> Tentang Toko
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('portal.address') ? 'active' : '' }}" href="{{ route('portal.address') }}">
                            <i class="bi bi-geo-alt"></i> Alamat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('portal.track*') ? 'active' : '' }}" href="{{ route('portal.track') }}">
                            <i class="bi bi-search"></i> Lacak Pesanan
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2 flex-wrap pb-2 pb-lg-0">
                    <button type="button" class="btn-cart" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas" aria-controls="cartOffcanvas">
                        <i class="bi bi-bag"></i>
                        <span class="d-none d-sm-inline">Keranjang</span>
                        <span class="cart-badge is-empty" data-cart-badge>0</span>
                    </button>
                    <a href="{{ route('login') }}" class="btn-login">
                        <i class="bi bi-person-lock"></i>
                        <span class="d-none d-md-inline">Login Petugas</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="portal-main">
        <div class="container pt-4">
            @if (session('success'))
                <div class="alert alert-portal alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-portal alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-portal alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Periksa kembali data Anda:</strong>
                    <ul class="mb-0 mt-2 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <footer class="portal-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-brand">
                        <span class="portal-brand-mark"><i class="bi bi-capsule"></i></span>
                        <span class="portal-brand-text">
                            <span class="portal-brand-name">{{ config('pharmacy.name') }}</span>
                            <span class="portal-brand-sub">Sehat Sehati &middot; Punggur</span>
                        </span>
                    </div>
                    <p class="mb-3" style="max-width: 340px;">
                        {{ config('pharmacy.tagline') }}
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="footer-pay"><i class="bi bi-cash-coin"></i> COD</span>
                        <span class="footer-pay"><i class="bi bi-bank2"></i> Transfer</span>
                        <span class="footer-pay"><i class="bi bi-qr-code"></i> QRIS</span>
                    </div>
                </div>

                <div class="col-6 col-lg-2">
                    <h6>Jelajahi</h6>
                    <ul class="footer-list">
                        <li><a href="{{ route('portal.index') }}">Beranda</a></li>
                        <li><a href="{{ route('portal.index') }}#katalog">Katalog Obat</a></li>
                        <li><a href="{{ route('portal.about') }}">Tentang Toko</a></li>
                        <li><a href="{{ route('portal.address') }}">Alamat Toko</a></li>
                        <li><a href="{{ route('portal.track') }}">Lacak Pesanan</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-3">
                    <h6>Hubungi Kami</h6>
                    <ul class="footer-list">
                        <li><i class="bi bi-geo-alt-fill"></i><span>{{ config('pharmacy.address') }}</span></li>
                        <li><i class="bi bi-telephone-fill"></i><a href="tel:{{ preg_replace('/[^0-9+]/', '', config('pharmacy.phone')) }}">{{ config('pharmacy.phone') }}</a></li>
                        <li><i class="bi bi-envelope-fill"></i><a href="mailto:{{ config('pharmacy.email') }}">{{ config('pharmacy.email') }}</a></li>
                    </ul>
                </div>

                <div class="col-lg-3">
                    <h6>Jam Operasional</h6>
                    <ul class="footer-list">
                        @foreach (config('pharmacy.hours') as $hour)
                            <li><i class="bi bi-clock-fill"></i><span>{{ $hour['day'] }} <strong class="text-white">{{ $hour['time'] }}</strong></span></li>
                        @endforeach
                    </ul>
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light mt-3 rounded-3">
                        <i class="bi bi-person-lock me-1"></i>Login Petugas
                    </a>
                </div>
            </div>

            <div class="footer-bottom d-flex flex-wrap justify-content-between gap-2">
                <span>&copy; {{ date('Y') }} {{ config('pharmacy.legal_name') }}. Semua hak dilindungi.</span>
                <span>Portal pelanggan &middot; Sistem Informasi Apotek</span>
            </div>
        </div>
    </footer>

    {{-- ================= KERANJANG (OFFCANVAS) ================= --}}
    <div class="offcanvas offcanvas-end cart-offcanvas" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel">
        <div class="offcanvas-header">
            <div>
                <h5 class="offcanvas-title" id="cartOffcanvasLabel"><i class="bi bi-bag-heart me-2 text-info"></i>Keranjang Belanja</h5>
                <small class="text-muted" style="font-size: 11px;">Periksa obat sebelum checkout</small>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column">
            <div id="cartItems" class="flex-grow-1"></div>
            <div class="cart-footer">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="cart-total-label">Total Belanja</span>
                    <span class="cart-total-value" id="cartTotal">Rp 0</span>
                </div>
                <small class="text-muted d-block mb-2" style="font-size: 10.5px;">Belum termasuk ongkir (dikonfirmasi petugas bila diperlukan).</small>
                <a href="{{ route('portal.checkout') }}" class="btn btn-portal w-100" id="cartCheckoutBtn">
                    <i class="bi bi-bag-check me-1"></i>Lanjut ke Checkout
                </a>
                <button type="button" class="btn btn-link btn-sm text-muted w-100 mt-1 text-decoration-none" id="cartClearBtn">
                    <i class="bi bi-trash3 me-1"></i>Kosongkan keranjang
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            const STORAGE_KEY = 'apotek_portal_cart_v1';

            function formatRupiah(value) {
                return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
            }

            function escapeHtml(text) {
                return String(text ?? '').replace(/[&<>"']/g, function (char) {
                    return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char];
                });
            }

            function getCart() {
                try {
                    const raw = localStorage.getItem(STORAGE_KEY);
                    const parsed = raw ? JSON.parse(raw) : [];
                    return Array.isArray(parsed) ? parsed : [];
                } catch (error) {
                    return [];
                }
            }

            function saveCart(cart) {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
                renderCart();
                document.dispatchEvent(new CustomEvent('portal-cart:updated', { detail: cart }));
            }

            function normalizeProduct(raw) {
                return {
                    id: Number(raw.id),
                    name: String(raw.name || 'Obat'),
                    price: Number(raw.price || 0),
                    unit: String(raw.unit || 'pcs'),
                    stock: Number(raw.stock || 0),
                    category: String(raw.category || 'Umum'),
                };
            }

            function add(product, quantity) {
                const clean = normalizeProduct(product);
                const qty = Math.max(1, Number(quantity || 1));
                const cart = getCart();
                const existing = cart.find((item) => Number(item.id) === clean.id);

                if (existing) {
                    const maxStock = Math.max(Number(existing.stock || clean.stock || 0), 1);
                    existing.quantity = Math.min(Number(existing.quantity || 0) + qty, maxStock);
                    existing.price = clean.price;
                    existing.stock = clean.stock;
                    existing.name = clean.name;
                    existing.unit = clean.unit;
                    existing.category = clean.category;
                } else {
                    cart.push({ ...clean, quantity: Math.min(qty, Math.max(clean.stock, 1)) });
                }

                saveCart(cart);
            }

            function updateQty(id, quantity) {
                const cart = getCart();
                const item = cart.find((row) => Number(row.id) === Number(id));
                if (!item) return;

                const maxStock = Math.max(Number(item.stock || 0), 1);
                item.quantity = Math.max(1, Math.min(Number(quantity || 1), maxStock));
                saveCart(cart);
            }

            function remove(id) {
                saveCart(getCart().filter((item) => Number(item.id) !== Number(id)));
            }

            function clear() {
                saveCart([]);
            }

            function updateBadge() {
                const totalItems = getCart().reduce((sum, item) => sum + Number(item.quantity || 0), 0);
                document.querySelectorAll('[data-cart-badge]').forEach((badge) => {
                    badge.textContent = totalItems;
                    badge.classList.toggle('is-empty', totalItems === 0);
                });
            }

            function renderCart() {
                const wrapper = document.getElementById('cartItems');
                const totalElement = document.getElementById('cartTotal');
                const checkoutButton = document.getElementById('cartCheckoutBtn');
                const cart = getCart();

                if (!wrapper) return;

                if (cart.length === 0) {
                    wrapper.innerHTML = `
                        <div class="cart-empty">
                            <i class="bi bi-bag-x d-block mb-2"></i>
                            <div class="fw-semibold" style="color: var(--ink-500);">Keranjang masih kosong</div>
                            <small>Tambahkan obat dari katalog untuk mulai berbelanja.</small>
                        </div>`;
                } else {
                    wrapper.innerHTML = cart.map((item) => {
                        const subtotal = Number(item.price || 0) * Number(item.quantity || 0);
                        return `
                            <div class="cart-item">
                                <span class="cart-item-icon"><i class="bi bi-capsule"></i></span>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="cart-item-name">${escapeHtml(item.name)}</div>
                                    <div class="cart-item-meta">${escapeHtml(item.category)} &middot; ${formatRupiah(item.price)} / ${escapeHtml(item.unit)}</div>
                                    <div class="d-flex align-items-center justify-content-between mt-2 gap-2">
                                        <div class="qty-control">
                                            <button type="button" class="qty-btn" data-cart-dec="${item.id}" aria-label="Kurangi">-</button>
                                            <span class="qty-value">${item.quantity}</span>
                                            <button type="button" class="qty-btn" data-cart-inc="${item.id}" aria-label="Tambah">+</button>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="cart-item-price">${formatRupiah(subtotal)}</span>
                                            <button type="button" class="cart-remove" data-cart-remove="${item.id}" aria-label="Hapus">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                    }).join('');
                }

                const total = cart.reduce((sum, item) => sum + (Number(item.price || 0) * Number(item.quantity || 0)), 0);
                if (totalElement) totalElement.textContent = formatRupiah(total);
                if (checkoutButton) checkoutButton.classList.toggle('disabled', cart.length === 0);

                updateBadge();
            }

            function showToast(message, type) {
                const toast = document.createElement('div');
                toast.className = 'portal-toast' + (type === 'warning' ? ' is-warning' : '');
                toast.innerHTML = `<i class="bi ${type === 'warning' ? 'bi-exclamation-circle-fill' : 'bi-check-circle-fill'} toast-icon"></i><span>${escapeHtml(message)}</span>`;
                document.body.appendChild(toast);

                requestAnimationFrame(() => toast.classList.add('show'));
                window.setTimeout(() => {
                    toast.classList.remove('show');
                    window.setTimeout(() => toast.remove(), 280);
                }, 2300);
            }

            window.PortalCart = { getCart, add, updateQty, remove, clear, renderCart, formatRupiah, showToast };

            document.addEventListener('DOMContentLoaded', function () {
                renderCart();

                document.addEventListener('click', function (event) {
                    const addButton = event.target.closest('[data-add-to-cart]');
                    if (addButton) {
                        event.preventDefault();
                        add({
                            id: addButton.dataset.id,
                            name: addButton.dataset.name,
                            price: addButton.dataset.price,
                            unit: addButton.dataset.unit,
                            stock: addButton.dataset.stock,
                            category: addButton.dataset.category,
                        }, 1);
                        showToast(addButton.dataset.name + ' ditambahkan ke keranjang.');
                        return;
                    }

                    const increaseButton = event.target.closest('[data-cart-inc]');
                    if (increaseButton) {
                        const id = increaseButton.dataset.cartInc;
                        const item = getCart().find((row) => Number(row.id) === Number(id));
                        if (item) {
                            if (Number(item.quantity) >= Number(item.stock || 0)) {
                                showToast('Jumlah sudah mencapai batas stok tersedia.', 'warning');
                                return;
                            }
                            updateQty(id, Number(item.quantity) + 1);
                        }
                        return;
                    }

                    const decreaseButton = event.target.closest('[data-cart-dec]');
                    if (decreaseButton) {
                        const id = decreaseButton.dataset.cartDec;
                        const item = getCart().find((row) => Number(row.id) === Number(id));
                        if (item) updateQty(id, Number(item.quantity) - 1);
                        return;
                    }

                    const removeButton = event.target.closest('[data-cart-remove]');
                    if (removeButton) {
                        remove(removeButton.dataset.cartRemove);
                        showToast('Item dihapus dari keranjang.', 'warning');
                        return;
                    }

                    if (event.target.closest('#cartClearBtn')) {
                        if (getCart().length > 0 && window.confirm('Kosongkan seluruh keranjang belanja?')) {
                            clear();
                            showToast('Keranjang dikosongkan.', 'warning');
                        }
                        return;
                    }

                    const checkoutButton = event.target.closest('#cartCheckoutBtn');
                    if (checkoutButton && getCart().length === 0) {
                        event.preventDefault();
                        showToast('Keranjang masih kosong. Pilih obat terlebih dahulu.', 'warning');
                    }
                });

                window.addEventListener('storage', function (event) {
                    if (event.key === STORAGE_KEY) renderCart();
                });

                document.addEventListener('portal-cart:updated', renderCart);
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
