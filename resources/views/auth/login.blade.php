<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#06b6d4">
    <title>Login Petugas — Apotek Sehat Sehati Punggur</title>

    <!-- Bootstrap 5.3 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts: Inter + Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

    {{-- Daftar gambar background (slideshow) --}}
    @php
        $bgImages = [
            asset('images/backgrounds/background 1.jpg'),
            asset('images/backgrounds/background 2.jpg'),
            asset('images/backgrounds/background 3.jpg'),
            asset('images/backgrounds/background 4.jpg'),
            asset('images/backgrounds/background 5.jpg'),
            asset('images/backgrounds/background 6.jpg'),
        ];
    @endphp

    <!-- Preload slide pertama agar first-paint cepat -->
    <link rel="preload" as="image" href="{{ $bgImages[0] }}">

    <style>
        /* ==========================================================
           VARIABEL TEMA — BIRU CYAN
           ========================================================== */
        :root {
            --cyan-200: #a5f3fc;
            --cyan-300: #67e8f9;
            --cyan-400: #22d3ee;
            --cyan-500: #06b6d4;
            --cyan-600: #0891b2;
            --blue-700: #0369a1;
            --blue-900: #0c4a6e;
            --ink:      #eafcff;
            --ink-dim:  rgba(234, 252, 255, 0.62);
            --glass:    rgba(255, 255, 255, 0.10);
            --glass-brd:rgba(255, 255, 255, 0.20);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body { height: 100%; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            overflow: hidden;
            background: #02121f; /* fallback saat gambar termuat */
            display: flex;
            align-items: stretch;
            justify-content: flex-end;   /* kartu login berada di sisi KANAN layar */
            color: var(--ink);
        }

        /* ==========================================================
           1. BACKGROUND DINAMIS — SLIDESHOW + BLUR + KEN BURNS
           ========================================================== */
        .bg-slide {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-size: cover;
            background-position: center;
            /* Blur + gelapkan gambar supaya kartu login menonjol */
            filter: blur(7px) brightness(0.52) saturate(1.15);
            transform: scale(1.12); /* cegah tepi transparan akibat blur */
            opacity: 0;
            transition: opacity 1.6s ease-in-out;
            will-change: opacity, transform;
        }
        .bg-slide.active { opacity: 1; }

        /* Efek Ken Burns: zoom pelan bergantian arah tiap slide */
        .bg-slide.active:nth-child(odd)  { animation: kenburnsA 26s ease-out forwards; }
        .bg-slide.active:nth-child(even) { animation: kenburnsB 26s ease-out forwards; }

        @keyframes kenburnsA {
            from { transform: scale(1.12) translate(0, 0); }
            to   { transform: scale(1.22) translate(-1.6%, -1.2%); }
        }
        @keyframes kenburnsB {
            from { transform: scale(1.22) translate(1.6%, 1.2%); }
            to   { transform: scale(1.12) translate(0, 0); }
        }

        /* Overlay gradien cyan-biru + vignette di atas gambar */
        .bg-overlay {
            position: fixed;
            inset: 0;
            z-index: 1;
            background:
                radial-gradient(ellipse at center, transparent 0%, rgba(1, 14, 27, 0.55) 100%),
                linear-gradient(120deg,
                    rgba(6, 182, 212, 0.32) 0%,
                    rgba(8, 145, 178, 0.40) 45%,
                    rgba(3, 62, 100, 0.58) 100%);
            pointer-events: none;
        }

        /* Tekstur noise halus agar kesan premium */
        .bg-noise {
            position: fixed;
            inset: 0;
            z-index: 1;
            opacity: 0.045;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
        }

        /* Bola cahaya cyan melayang (aksen dekoratif) */
        .orb {
            position: fixed;
            border-radius: 50%;
            z-index: 1;
            pointer-events: none;
            filter: blur(70px);
            mix-blend-mode: screen;
        }
        .orb-1 {
            width: 420px; height: 420px;
            top: -140px; left: -120px;
            background: radial-gradient(circle, rgba(34, 211, 238, 0.38), transparent 70%);
            animation: orbFloat 28s ease-in-out infinite alternate;
        }
        .orb-2 {
            width: 360px; height: 360px;
            bottom: -150px; left: 32%;
            background: radial-gradient(circle, rgba(103, 232, 249, 0.30), transparent 70%);
            animation: orbFloat 34s ease-in-out infinite alternate-reverse;
        }
        .orb-3 {
            width: 300px; height: 300px;
            top: 24%; right: -110px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.30), transparent 70%);
            animation: orbFloat 24s ease-in-out infinite alternate;
        }
        @keyframes orbFloat {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(60px, 40px) scale(1.15); }
        }

        /* ==========================================================
           2. PANEL KIRI — BRANDING
           ========================================================== */
        .left-panel {
            position: relative;
            z-index: 2;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 0;
            padding: clamp(40px, 6vw, 88px);
            color: #fff;
            max-width: 760px;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            width: fit-content;
            background: var(--glass);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-brd);
            border-radius: 999px;
            padding: 9px 20px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.4px;
            margin-bottom: 30px;
            box-shadow: 0 8px 24px rgba(2, 32, 56, 0.35);
        }
        .brand-badge i { color: var(--cyan-300); font-size: 15px; }

        .left-panel h1 {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(2.1rem, 4.4vw, 3.4rem);
            font-weight: 800;
            line-height: 1.14;
            letter-spacing: -0.5px;
            margin-bottom: 18px;
            text-shadow: 0 4px 18px rgba(0, 0, 0, 0.35);
        }
        /* Kata kunci dengan teks gradien cyan */
        .left-panel h1 .grad-text {
            background: linear-gradient(92deg, var(--cyan-200), var(--cyan-400) 55%, var(--cyan-300));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .left-panel .subtitle {
            font-size: clamp(0.95rem, 1.4vw, 1.08rem);
            font-weight: 300;
            color: var(--ink-dim);
            max-width: 460px;
            line-height: 1.75;
            margin-bottom: 36px;
        }

        /* Kartu statistik kaca */
        .stat-cards { display: flex; gap: 14px; flex-wrap: wrap; }
        .stat-card {
            background: var(--glass);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-brd);
            border-radius: 16px;
            padding: 16px 20px;
            min-width: 136px;
            transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            border-color: rgba(103, 232, 249, 0.55);
            box-shadow: 0 12px 32px rgba(6, 182, 212, 0.25);
        }
        .stat-card .stat-icon {
            font-size: 1.05rem;
            color: var(--cyan-300);
            margin-bottom: 8px;
            display: block;
        }
        .stat-card .stat-num {
            font-family: 'Poppins', sans-serif;
            font-size: 1.45rem;
            font-weight: 700;
            color: var(--cyan-300);
            line-height: 1;
        }
        .stat-card .stat-label {
            font-size: 10.5px;
            font-weight: 500;
            color: var(--ink-dim);
            margin-top: 5px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        /* Jam live + tanggal */
        .live-clock {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 40px;
            width: fit-content;
        }
        .live-clock .clock-ico {
            width: 40px; height: 40px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--glass);
            border: 1px solid var(--glass-brd);
            color: var(--cyan-300);
            font-size: 1.05rem;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        .live-clock .clock-time {
            font-family: 'Poppins', sans-serif;
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--ink);
            letter-spacing: 1px;
            font-variant-numeric: tabular-nums;
            line-height: 1.1;
        }
        .live-clock .clock-date {
            font-size: 12px;
            color: var(--ink-dim);
            font-weight: 400;
            letter-spacing: 0.3px;
            text-transform: capitalize;
        }

        /* Indikator slideshow (progress dots) */
        .slide-dots {
            display: flex;
            gap: 9px;
            margin-top: 34px;
        }
        .slide-dot {
            position: relative;
            width: 34px;
            height: 5px;
            border: none;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.18);
            overflow: hidden;
            cursor: pointer;
            padding: 0;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .slide-dot:hover { background: rgba(255, 255, 255, 0.30); transform: translateY(-1px); }
        .slide-dot .fill {
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(90deg, var(--cyan-400), var(--cyan-300));
            transform: scaleX(0);
            transform-origin: left center;
        }
        .slide-dot.active .fill { animation: dotFill var(--slide-duration, 7s) linear forwards; }
        @keyframes dotFill { from { transform: scaleX(0); } to { transform: scaleX(1); } }

        /* Animasi masuk bertahap panel kiri */
        .anim-up {
            opacity: 0;
            animation: slideUp 0.9s cubic-bezier(0.22, 0.9, 0.32, 1) forwards;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(26px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ==========================================================
           3. PANEL KANAN — KARTU LOGIN (GLASSMORPHISM)
           ========================================================== */
        .right-panel {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 470px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 36px 30px;
            /* Kaca tipis sepanjang sisi kanan layar */
            background: linear-gradient(200deg, rgba(255,255,255,0.09), rgba(255,255,255,0.03));
            backdrop-filter: blur(26px) saturate(170%);
            -webkit-backdrop-filter: blur(26px) saturate(170%);
            border-left: 1px solid rgba(255, 255, 255, 0.14);
            box-shadow: -24px 0 60px rgba(1, 20, 38, 0.35);
        }
        /* Garis aksen cyan bercahaya pada tepi kiri panel */
        .right-panel::before {
            content: '';
            position: absolute;
            left: -1px;
            top: 12%;
            bottom: 12%;
            width: 2px;
            border-radius: 2px;
            background: linear-gradient(180deg, transparent, var(--cyan-400), transparent);
            opacity: 0.8;
        }

        .login-card {
            width: 100%;
            max-width: 384px;
            position: relative;
            border-radius: 24px;
            padding: 36px 32px 28px;
            background: linear-gradient(160deg, rgba(255, 255, 255, 0.14), rgba(255, 255, 255, 0.05));
            border: 1px solid rgba(255, 255, 255, 0.22);
            backdrop-filter: blur(20px) saturate(150%);
            -webkit-backdrop-filter: blur(20px) saturate(150%);
            box-shadow: 0 28px 64px rgba(0, 18, 36, 0.5);
            overflow: hidden;
            opacity: 0;
            animation: cardIn 1s cubic-bezier(0.22, 0.9, 0.32, 1) 0.15s forwards;
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(30px) scale(0.98); filter: blur(6px); }
            to   { opacity: 1; transform: translateY(0) scale(1); filter: blur(0); }
        }
        /* Glow cyan tipis di garis atas kartu */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 12%; right: 12%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(103, 232, 249, 0.95), transparent);
        }
        /* Cahaya radial lembut di pojok kartu */
        .login-card::after {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(34, 211, 238, 0.22), transparent 70%);
            pointer-events: none;
        }

        /* Header kartu */
        .card-logo-wrap { text-align: center; margin-bottom: 26px; position: relative; }
        .logo-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--cyan-400), var(--blue-700));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            box-shadow: 0 10px 30px rgba(6, 182, 212, 0.45), inset 0 1px 1px rgba(255,255,255,0.35);
            position: relative;
        }
        .logo-circle i { font-size: 2rem; color: #fff; }
        /* Ring pulse di belakang logo */
        .logo-circle::before {
            content: '';
            position: absolute;
            inset: -7px;
            border-radius: 50%;
            border: 1.5px solid rgba(103, 232, 249, 0.55);
            animation: logoPulse 2.6s ease-out infinite;
        }
        @keyframes logoPulse {
            0%   { transform: scale(0.9); opacity: 0.9; }
            70%  { transform: scale(1.25); opacity: 0; }
            100% { transform: scale(1.25); opacity: 0; }
        }
        .card-logo-wrap h4 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.28rem;
            color: #fff;
            margin-bottom: 5px;
            letter-spacing: 0.2px;
        }
        .card-logo-wrap small {
            color: var(--ink-dim);
            font-size: 12.5px;
            letter-spacing: 0.2px;
        }

        /* Form inputs */
        .form-label {
            color: rgba(255, 255, 255, 0.82);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            margin-bottom: 7px;
        }
        .input-group-text {
            background: var(--glass) !important;
            border: 1px solid var(--glass-brd) !important;
            border-right: none !important;
            color: rgba(255, 255, 255, 0.65) !important;
            transition: border-color 0.25s ease;
        }
        .form-control {
            background: var(--glass) !important;
            border: 1px solid var(--glass-brd) !important;
            border-left: none !important;
            color: #fff !important;
            font-size: 14px;
            caret-color: var(--cyan-300);
            transition: background 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
        }
        .form-control::placeholder { color: rgba(255, 255, 255, 0.38) !important; }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.16) !important;
            border-color: rgba(103, 232, 249, 0.65) !important;
            box-shadow: 0 0 0 3.5px rgba(34, 211, 238, 0.18) !important;
            color: #fff !important;
        }
        .input-group:focus-within .input-group-text {
            border-color: rgba(103, 232, 249, 0.65) !important;
            color: var(--cyan-300) !important;
        }

        /* Tombol toggle password */
        #togglePassword {
            background: var(--glass) !important;
            border: 1px solid var(--glass-brd) !important;
            border-left: none !important;
            color: rgba(255, 255, 255, 0.65) !important;
            transition: all 0.2s ease;
        }
        #togglePassword:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            color: var(--cyan-300) !important;
        }

        /* Hint Caps Lock */
        .capslock-hint {
            display: none;
            font-size: 11.5px;
            color: #fcd34d;
            margin-top: 6px;
            align-items: center;
            gap: 6px;
        }
        .capslock-hint.show { display: flex; }

        /* Checkbox remember */
        .form-check-label { color: var(--ink-dim); font-size: 12.5px; }
        .form-check-input {
            background-color: rgba(255, 255, 255, 0.14);
            border-color: rgba(255, 255, 255, 0.32);
            cursor: pointer;
        }
        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(34, 211, 238, 0.18);
            border-color: rgba(103, 232, 249, 0.65);
        }
        .form-check-input:checked {
            background-color: var(--cyan-500);
            border-color: var(--cyan-500);
        }

        /* Tombol login utama — gradien cyan + shimmer */
        .btn-login {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, var(--cyan-400) 0%, var(--cyan-500) 45%, var(--blue-700) 100%);
            color: #fff;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 0.3px;
            border: none;
            border-radius: 12px;
            padding: 13px;
            width: 100%;
            box-shadow: 0 8px 22px rgba(6, 182, 212, 0.42), inset 0 1px 1px rgba(255, 255, 255, 0.35);
            transition: transform 0.25s ease, box-shadow 0.25s ease, filter 0.25s ease;
        }
        .btn-login::after {
            /* Kilau cahaya yang menyapu tombol */
            content: '';
            position: absolute;
            top: 0; left: -80%;
            width: 45%;
            height: 100%;
            background: linear-gradient(105deg, transparent, rgba(255, 255, 255, 0.38), transparent);
            transform: skewX(-22deg);
            animation: shimmer 3.4s ease-in-out infinite;
        }
        @keyframes shimmer {
            0%   { left: -80%; }
            55%, 100% { left: 135%; }
        }
        .btn-login:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 14px 34px rgba(6, 182, 212, 0.58), inset 0 1px 1px rgba(255, 255, 255, 0.35);
            filter: brightness(1.06);
            color: #fff;
        }
        .btn-login:active:not(:disabled) { transform: translateY(0); }
        .btn-login:disabled { opacity: 0.75; cursor: wait; }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 22px 0 16px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.18), transparent);
        }
        .divider span {
            color: rgba(255, 255, 255, 0.45);
            font-size: 11px;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        /* Tombol role demo */
        .btn-role {
            background: var(--glass);
            border: 1px solid var(--glass-brd);
            color: rgba(255, 255, 255, 0.85);
            font-size: 12px;
            font-weight: 600;
            border-radius: 10px;
            padding: 8px 14px;
            transition: all 0.22s ease;
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
        }
        .btn-role:hover {
            background: rgba(34, 211, 238, 0.18);
            border-color: rgba(103, 232, 249, 0.55);
            color: var(--cyan-300);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(6, 182, 212, 0.22);
        }

        /* Alert override (glass) */
        .alert { border-radius: 12px; font-size: 13px; backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); }
        .alert-success { background: rgba(16, 185, 129, 0.18); border-color: rgba(16, 185, 129, 0.35); color: #6ee7b7; }
        .alert-danger  { background: rgba(239, 68, 68, 0.18);  border-color: rgba(239, 68, 68, 0.35);  color: #fca5a5; }
        .alert-warning { background: rgba(245, 158, 11, 0.18); border-color: rgba(245, 158, 11, 0.35); color: #fcd34d; }
        .btn-close { filter: invert(1); }

        /* Footer kartu */
        .card-footnote {
            text-align: center;
            color: rgba(255, 255, 255, 0.35);
            font-size: 11px;
            margin-top: 18px;
        }
        .card-footnote code { color: var(--cyan-300); }

        /* ==========================================================
           RESPONSIF
           ========================================================== */
        @media (max-width: 992px) {
            body { justify-content: center; }
            .left-panel { display: none; }
            .right-panel {
                max-width: 100%;
                min-height: 100vh;
                border-left: none;
                background: rgba(1, 16, 30, 0.45);
                box-shadow: none;
            }
            .right-panel::before { display: none; }
        }

        /* Hormati preferensi pengguna yang mengurangi motion */
        @media (prefers-reduced-motion: reduce) {
            .bg-slide.active:nth-child(odd),
            .bg-slide.active:nth-child(even),
            .orb, .logo-circle::before, .btn-login::after {
                animation: none !important;
            }
            .anim-up, .login-card { animation-duration: 0.01s !important; }
            .slide-dot.active .fill { animation: none; transform: none; background: var(--cyan-300); }
        }
    </style>
</head>
<body>

    {{-- URL background untuk preload di JS --}}
    <script>const BG_URLS = {{ Js::from($bgImages) }};</script>

    <!-- =========================================================
         LAPISAN BACKGROUND SLIDESHOW (auto carousel + blur)
         ========================================================= -->
    @foreach ($bgImages as $i => $img)
        <div class="bg-slide {{ $i === 0 ? 'active' : '' }}" id="slide-{{ $i }}"
             style="background-image: url('{{ $img }}')"></div>
    @endforeach

    <!-- Overlay gradien cyan, noise, dan orbs dekoratif -->
    <div class="bg-overlay"></div>
    <div class="bg-noise"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- =========================================================
         PANEL KIRI — BRANDING & INFORMASI
         ========================================================= -->
    <div class="left-panel">

        <div class="brand-badge anim-up" style="animation-delay: 0.05s">
            <i class="bi bi-capsule-pill"></i>
            Apotek Sehat Sehati · Punggur
        </div>

        <h1 class="anim-up" style="animation-delay: 0.15s">
            Sistem Informasi<br>
            <span class="grad-text">Kefarmasian</span> &amp;<br>
            Point of Sale
        </h1>

        <p class="subtitle anim-up" style="animation-delay: 0.25s">
            Platform manajemen apotek modern yang terintegrasi — kelola stok obat,
            kasir digital, dan laporan penjualan dalam satu sistem yang aman.
        </p>

        <div class="stat-cards anim-up" style="animation-delay: 0.35s">
            <div class="stat-card">
                <i class="bi bi-database-check stat-icon"></i>
                <div class="stat-num">3NF</div>
                <div class="stat-label">Normalisasi DB</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-boxes stat-icon"></i>
                <div class="stat-num">FEFO</div>
                <div class="stat-label">Manajemen Stok</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-qr-code stat-icon"></i>
                <div class="stat-num">QRIS</div>
                <div class="stat-label">Pembayaran Digital</div>
            </div>
        </div>

        <!-- Jam & tanggal live -->
        <div class="live-clock anim-up" style="animation-delay: 0.45s">
            <span class="clock-ico"><i class="bi bi-clock"></i></span>
            <div>
                <div class="clock-time" id="liveClock">--:--:--</div>
                <div class="clock-date" id="liveDate">&nbsp;</div>
            </div>
        </div>

        <!-- Indikator slideshow background -->
        <div class="slide-dots anim-up" id="slideDots" style="animation-delay: 0.55s">
            @foreach ($bgImages as $i => $img)
                <button type="button" class="slide-dot {{ $i === 0 ? 'active' : '' }}" data-idx="{{ $i }}"
                        aria-label="Tampilkan background {{ $i + 1 }}">
                    <span class="fill"></span>
                </button>
            @endforeach
        </div>

    </div>

    <!-- =========================================================
         PANEL KANAN — FORM LOGIN (glassmorphism)
         ========================================================= -->
    <div class="right-panel">
        <div class="login-card">

            <!-- Logo & judul -->
            <div class="card-logo-wrap">
                <div class="logo-circle">
                    <i class="bi bi-heart-pulse-fill"></i>
                </div>
                <h4>Apotek Sehat Sehati</h4>
                <small>Sistem Informasi Pelayanan Kefarmasian &amp; POS</small>
            </div>

            <!-- Pesan sesi / error -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2 small mb-3" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif
            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show py-2 small mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('warning') }}
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show py-2 small mb-3" role="alert">
                    <i class="bi bi-x-circle-fill me-1"></i> {{ $errors->first() }}
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            <!-- Form login -->
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="bi bi-person-badge me-1"></i> Email Petugas
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" id="email" class="form-control"
                               placeholder="nama@apotek.com" value="{{ old('email') }}" required autofocus
                               autocomplete="username">
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock me-1"></i> Kata Sandi
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-key"></i></span>
                        <input type="password" name="password" id="password"
                               class="form-control" placeholder="••••••••" required autocomplete="current-password">
                        <button class="btn" type="button" id="togglePassword" aria-label="Tampilkan password">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    <!-- Peringatan Caps Lock aktif -->
                    <div class="capslock-hint" id="capsHint">
                        <i class="bi bi-exclamation-triangle-fill"></i> Caps Lock sedang aktif
                    </div>
                </div>

                <!-- Remember me & info keamanan -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Ingat sesi saya</label>
                    </div>
                    <small style="color:rgba(255,255,255,0.5); font-size:11.5px;">
                        <i class="bi bi-shield-check me-1" style="color:var(--cyan-300);"></i>Bcrypt
                    </small>
                </div>

                <!-- Tombol masuk -->
                <button type="submit" class="btn-login mb-1" id="btnLogin">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Sistem
                </button>
            </form>

            <!-- Divider -->
            <div class="divider"><span>Uji Coba Cepat</span></div>

            <!-- Tombol role demo -->
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <button type="button" class="btn-role" onclick="fillLogin('kasir@apotek.com', 'password123')">
                    <i class="bi bi-cart3 me-1"></i>Kasir
                </button>
                <button type="button" class="btn-role" onclick="fillLogin('apoteker@apotek.com', 'password123')">
                    <i class="bi bi-capsule me-1"></i>Apoteker
                </button>
                <button type="button" class="btn-role" onclick="fillLogin('admin@apotek.com', 'password123')">
                    <i class="bi bi-person-gear me-1"></i>Admin
                </button>
            </div>

            <div class="card-footnote">
                Password default: <code>password123</code>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        /* =========================================================
           1. BACKGROUND SLIDESHOW (auto carousel, halus)
           ========================================================= */
        const SLIDE_DURATION = 7000; // ms — samakan dengan --slide-duration CSS
        document.documentElement.style.setProperty('--slide-duration', SLIDE_DURATION + 'ms');

        const slideEls = document.querySelectorAll('.bg-slide');
        const dotEls  = document.querySelectorAll('.slide-dot');
        const totalSlides = slideEls.length;
        let currentSlide = 0;
        let slideTimer = null;

        function goToSlide(idx) {
            if (idx === currentSlide) return;
            slideEls[currentSlide].classList.remove('active');
            dotEls[currentSlide].classList.remove('active');
            currentSlide = idx;
            slideEls[currentSlide].classList.add('active'); // restart Ken Burns
            dotEls[currentSlide].classList.add('active');    // restart progress fill
            restartAutoPlay();
        }

        function nextSlide() {
            goToSlide((currentSlide + 1) % totalSlides);
        }

        function restartAutoPlay() {
            clearInterval(slideTimer);
            slideTimer = setInterval(nextSlide, SLIDE_DURATION);
        }
        restartAutoPlay();

        // Klik dot untuk memilih background secara manual
        dotEls.forEach(dot => {
            dot.addEventListener('click', () => goToSlide(parseInt(dot.dataset.idx)));
        });

        // Preload semua gambar agar pergantian slide tidak putus-putus
        BG_URLS.forEach(url => { const img = new Image(); img.src = url; });

        /* =========================================================
           2. JAM & TANGGAL LIVE (format Indonesia)
           ========================================================= */
        const clockEl = document.getElementById('liveClock');
        const dateEl  = document.getElementById('liveDate');

        function updateClock() {
            const now = new Date();
            clockEl.textContent = now.toLocaleTimeString('id-ID', { hour12: false });
            dateEl.textContent = now.toLocaleDateString('id-ID', {
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
            });
        }
        updateClock();
        setInterval(updateClock, 1000);

        /* =========================================================
           3. TOGGLE PASSWORD + DETEKSI CAPS LOCK
           ========================================================= */
        const passwordInput = document.getElementById('password');
        const capsHint = document.getElementById('capsHint');

        document.getElementById('togglePassword').addEventListener('click', function () {
            const eye = document.getElementById('eyeIcon');
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            eye.classList.toggle('bi-eye', !isHidden);
            eye.classList.toggle('bi-eye-slash', isHidden);
        });

        ['keyup', 'keydown'].forEach(evt => {
            passwordInput.addEventListener(evt, function (e) {
                capsHint.classList.toggle('show', e.getModifierState && e.getModifierState('CapsLock'));
            });
        });
        passwordInput.addEventListener('blur', () => capsHint.classList.remove('show'));

        /* =========================================================
           4. QUICK FILL AKUN DEMO
           ========================================================= */
        function fillLogin(email, password) {
            document.getElementById('email').value = email;
            passwordInput.value = password;
            const btn = document.getElementById('btnLogin');
            btn.style.transform = 'scale(0.97)';
            setTimeout(() => btn.style.transform = '', 180);
        }

        /* =========================================================
           5. LOADING STATE SAAT SUBMIT
           ========================================================= */
        document.getElementById('loginForm').addEventListener('submit', function () {
            const btn = document.getElementById('btnLogin');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memverifikasi...';
            btn.disabled = true;
        });
    </script>
</body>
</html>
