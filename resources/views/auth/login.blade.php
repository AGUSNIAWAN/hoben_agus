<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HokBen Daily Activity</title>
    <link rel="icon" type="image/png" href="{{ asset('hokben-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --hokben-red: #D9222A;
            --hokben-red-dark: #b8161d;
            --hokben-yellow: #FFC400;
            --hokben-yellow-dark: #F59E0B;
            --hokben-amber: #E68A00;
            --text-dark: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --border-light: #e2e8f0;
            --bg-light: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            min-height: 100vh;
            background-color: #f8fafc;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        .login-split-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* ================= LEFT HERO SIDE (STOREFRONT ILLUSTRATION) ================= */
        .login-hero-side {
            flex: 1.15;
            position: relative;
            background: #0f172a;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 44px;
            overflow: hidden;
        }

        .hero-bg-wrapper {
            position: absolute;
            inset: 0;
            z-index: 1;
        }

        .hero-bg-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center bottom;
            transform: scale(1.02);
            transition: transform 10s ease;
        }

        .login-hero-side:hover .hero-bg-wrapper img {
            transform: scale(1.06);
        }

        /* Subtle vignette & color grading overlay */
        .hero-gradient-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                180deg,
                rgba(15, 23, 42, 0.38) 0%,
                rgba(15, 23, 42, 0.05) 45%,
                rgba(15, 23, 42, 0.78) 100%
            );
            z-index: 2;
        }

        /* Floating Badge Top */
        .hero-top-badge {
            position: relative;
            z-index: 5;
            align-self: flex-start;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(18, 24, 38, 0.78);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
            border-radius: 9999px;
            color: #ffffff;
            font-size: 0.86rem;
            font-weight: 600;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
            animation: floatSlow 5s ease-in-out infinite;
        }

        .hero-top-badge .pulsing-dot {
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.35);
            animation: pulseDot 2s infinite;
        }

        .hero-top-badge svg {
            color: var(--hokben-yellow);
        }

        /* Floating Card Bottom */
        .hero-bottom-card {
            position: relative;
            z-index: 5;
            background: rgba(18, 24, 38, 0.86);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 20px;
            padding: 24px 26px;
            color: #ffffff;
            max-width: 480px;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.6),
                        0 0 30px rgba(245, 158, 11, 0.15);
            animation: floatCard 6s ease-in-out infinite alternate;
        }

        .hero-bottom-card .card-head {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 12px;
        }

        .hero-bottom-card .card-head .icon-circle {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.25) 0%, rgba(217, 34, 42, 0.25) 100%);
            border: 1px solid rgba(245, 158, 11, 0.4);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--hokben-yellow);
        }

        .hero-bottom-card .card-head h4 {
            font-size: 1.05rem;
            font-weight: 700;
            color: #ffffff;
            margin: 0 0 2px 0;
        }

        .hero-bottom-card .card-head p {
            font-size: 0.82rem;
            color: #cbd5e1;
            margin: 0;
            line-height: 1.4;
        }

        .hero-stats-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .hero-stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 600;
            color: #f1f5f9;
        }

        .hero-stat-pill svg {
            color: var(--hokben-yellow);
        }

        /* ================= RIGHT FORM SIDE ================= */
        .login-form-side {
            flex: 0.95;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
            position: relative;
        }

        .form-content-box {
            width: 100%;
            max-width: 440px;
            margin: 0 auto;
        }

        /* Mascot Header */
        .mascot-header-wrap {
            text-align: center;
            margin-bottom: 24px;
        }

        .mascot-header-wrap .mascot-img {
            height: 92px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 4px 14px rgba(0, 0, 0, 0.08));
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .mascot-header-wrap .mascot-img:hover {
            transform: scale(1.08) rotate(-1.5deg);
        }

        /* Greetings */
        .greeting-wrap {
            text-align: center;
            margin-bottom: 32px;
        }

        .greeting-wrap h1 {
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
            letter-spacing: -0.025em;
        }

        .greeting-wrap p {
            font-size: 0.92rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        /* Alert Notifications */
        .alert-box {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
            line-height: 1.4;
            animation: fadeIn 0.3s ease;
        }

        .alert-box.alert-success {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .alert-box.alert-error {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        /* Form Inputs */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-body);
            margin-bottom: 8px;
        }

        .input-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-box .field-icon {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            pointer-events: none;
            transition: color 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .input-box input {
            width: 100%;
            height: 48px;
            padding: 10px 16px 10px 42px;
            border: 1.5px solid var(--border-light);
            border-radius: 10px;
            font-size: 0.94rem;
            color: #0f172a;
            background-color: #ffffff;
            transition: all 0.25s ease;
            outline: none;
        }

        /* Default email styling with subtle amber accent match */
        .input-box input#email {
            border-color: #cbd5e1;
        }

        .input-box input::placeholder {
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .input-box input:focus {
            border-color: var(--hokben-yellow-dark) !important;
            box-shadow: 0 0 0 3.5px rgba(245, 158, 11, 0.16) !important;
            background-color: #ffffff;
        }

        .input-box input:focus ~ .field-icon,
        .input-box input:focus + .field-icon {
            color: var(--hokben-yellow-dark);
        }

        .btn-toggle-password {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .btn-toggle-password:hover {
            color: #334155;
        }

        .field-error-text {
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 6px;
            font-weight: 500;
        }

        /* Checkbox & Forgot Password Row */
        .form-row-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 0.88rem;
        }

        .custom-checkbox {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
            color: var(--text-muted);
            font-weight: 500;
        }

        .custom-checkbox input[type="checkbox"] {
            width: 17px;
            height: 17px;
            accent-color: var(--hokben-yellow-dark);
            cursor: pointer;
        }

        .forgot-link {
            color: var(--hokben-yellow-dark);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .forgot-link:hover {
            color: var(--hokben-amber);
            text-decoration: underline;
        }

        /* Login Button */
        .btn-login-submit {
            width: 100%;
            height: 48px;
            background: linear-gradient(180deg, #F59E0B 0%, #E68A00 100%);
            border: none;
            border-radius: 10px;
            color: #ffffff;
            font-size: 1.02rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 10px 22px -6px rgba(245, 158, 11, 0.48);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-login-submit:hover {
            background: linear-gradient(180deg, #d97706 0%, #b45309 100%);
            box-shadow: 0 14px 26px -6px rgba(245, 158, 11, 0.6);
            transform: translateY(-2px);
        }

        .btn-login-submit:active {
            transform: translateY(1px);
        }

        /* Quick Demo Credentials Access */
        .demo-quick-box {
            margin-top: 30px;
            padding-top: 22px;
            border-top: 1px dashed var(--border-light);
            text-align: center;
        }

        .demo-quick-box p.demo-title {
            font-size: 0.76rem;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 12px;
        }

        .demo-pills-row {
            display: flex;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .demo-pill-btn {
            background: #f8fafc;
            border: 1px solid var(--border-light);
            color: #475569;
            padding: 7px 13px;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .demo-pill-btn:hover {
            background: #fffbeb;
            border-color: #fde68a;
            color: #b45309;
            transform: translateY(-1px);
        }

        /* Footer */
        .login-footer-copy {
            margin-top: 28px;
            text-align: center;
            font-size: 0.78rem;
            color: #94a3b8;
        }

        /* Forgot Password Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(8px);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-popup {
            background: #ffffff;
            border-radius: 20px;
            max-width: 440px;
            width: 100%;
            padding: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            text-align: center;
            animation: modalPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .modal-popup .popup-icon {
            width: 58px;
            height: 58px;
            background: #fffbeb;
            color: #f59e0b;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .modal-popup h3 {
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 10px;
        }

        .modal-popup p {
            font-size: 0.9rem;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 22px;
        }

        .btn-modal-close {
            width: 100%;
            padding: 12px;
            background: #f1f5f9;
            color: #334155;
            border: none;
            border-radius: 12px;
            font-size: 0.92rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-modal-close:hover {
            background: #e2e8f0;
        }

        /* Keyframes */
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        @keyframes floatCard {
            0% { transform: translateY(0); }
            100% { transform: translateY(-10px); }
        }

        @keyframes pulseDot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.3); opacity: 0.7; }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes modalPop {
            from { opacity: 0; transform: scale(0.92); }
            to { opacity: 1; transform: scale(1); }
        }

        /* ================= RESPONSIVE DESIGN ================= */
        @media (max-width: 992px) {
            .login-split-container {
                flex-direction: column;
            }

            .login-hero-side {
                flex: none;
                height: 280px;
                padding: 24px;
            }

            .hero-bottom-card {
                display: none;
            }

            .hero-top-badge {
                font-size: 0.78rem;
                padding: 7px 14px;
            }

            .login-form-side {
                flex: 1;
                padding: 40px 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-split-container">
        
        <!-- Left Hero Side (Visual Banner Ilustrasi Store HokBen) -->
        <div class="login-hero-side">
            <div class="hero-bg-wrapper">
                <img src="{{ asset('hokben-store-illust.jpg') }}" alt="HokBen Storefront Drive Thru" onerror="this.style.display='none'">
            </div>
            <div class="hero-gradient-overlay"></div>

            <!-- Top Floating Live Badge -->
            <div class="hero-top-badge">
                <span class="pulsing-dot"></span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/></svg>
                <span>OpsTrack Portal • Area 19 & Regional 5</span>
            </div>

            <!-- Bottom Floating Feature Showcase Card -->
            <div class="hero-bottom-card">
                <div class="card-head">
                    <div class="icon-circle">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/><path d="M14 9h5v5"/></svg>
                    </div>
                    <div>
                        <h4>Daily Activity & Monitoring Store</h4>
                        <p>Pemantauan checklist harian, aktual sales 31 hari, & fasilitas KFM.</p>
                    </div>
                </div>
                <div class="hero-stats-row">
                    <div class="hero-stat-pill">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>100% SOP Terpantau</span>
                    </div>
                    <div class="hero-stat-pill">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        <span>Real-time Sync</span>
                    </div>
                    <div class="hero-stat-pill">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                        <span>Halal & Higienis</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Form Side (Login Form Bersih & Menarik Sesuai Referensi) -->
        <div class="login-form-side">
            <div class="form-content-box">
                
                <!-- Mascot / Brand Logo (Taro & Hanako Otentik Transparan) -->
                <div class="mascot-header-wrap">
                    <img src="{{ asset('hokben-logo-clean.svg') }}" alt="HokBen Official Logo" class="mascot-img" onerror="this.src='{{ asset('hokben-logo.svg') }}'">
                </div>

                <!-- Greeting Header -->
                <div class="greeting-wrap">
                    <h1>Selamat Datang</h1>
                    <p>Masuk untuk melanjutkan ke aplikasi Daily Activity HokBen.</p>
                </div>

                <!-- Session & Validation Alert Messages -->
                @if(session('success'))
                    <div class="alert-box alert-success">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert-box alert-error">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Auth Form -->
                <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                    @csrf

                    <!-- Email Input -->
                    <div class="form-group">
                        <label for="email">Email atau Username</label>
                        <div class="input-box">
                            <span class="field-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            </span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="contoh@email.com" required autofocus autocomplete="username">
                        </div>
                        @error('email')
                            <p class="field-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-box">
                            <span class="field-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </span>
                            <input type="password" id="password" name="password" placeholder="Masukkan password" required autocomplete="current-password">
                            <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibility()" aria-label="Toggle password visibility">
                                <span id="toggleIcon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                </span>
                            </button>
                        </div>
                        @error('password')
                            <p class="field-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="form-row-actions">
                        <label class="custom-checkbox">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span class="label-text">Ingat saya</span>
                        </label>
                        <a href="#" class="forgot-link" onclick="openForgotPasswordModal(event)">Lupa password?</a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-login-submit">
                        <span>Login</span>
                    </button>
                </form>

                <!-- Quick Demo Access Pills -->
                <div class="demo-quick-box">
                    <p class="demo-title">Pilih Akun Demo untuk Masuk Cepat</p>
                    <div class="demo-pills-row">
                        <button type="button" class="demo-pill-btn" onclick="fillCredentials('admin@hokben.co.id', 'password123')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7zm3 16h14"/></svg>
                            <span>Leader</span>
                        </button>
                        <button type="button" class="demo-pill-btn" onclick="fillCredentials('ahmad@hokben.co.id', 'password123')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/><line x1="6" x2="18" y1="17" y2="17"/></svg>
                            <span>Kitchen</span>
                        </button>
                        <button type="button" class="demo-pill-btn" onclick="fillCredentials('siti@hokben.co.id', 'password123')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
                            <span>Front Counter</span>
                        </button>
                        <button type="button" class="demo-pill-btn" onclick="fillCredentials('rian@hokben.co.id', 'password123')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1-2-1Z"/><path d="M14 8H8"/><path d="M16 12H8"/><path d="M13 16H8"/></svg>
                            <span>Cashier</span>
                        </button>
                    </div>
                </div>

                <!-- Footer Copyright -->
                <div class="login-footer-copy">
                    <p>© {{ date('Y') }} PT Eka Bogainti (HokBen) • OpsTrack Area 19</p>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Lupa Password -->
    <div class="modal-overlay" id="forgotModal">
        <div class="modal-popup">
            <div class="popup-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21 2-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0 3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
            </div>
            <h3>Pemulihan Kata Sandi</h3>
            <p>Untuk menjaga keamanan operasional outlet, reset akun dikelola langsung oleh Store Leader atau Tim IT Support HokBen. Silakan hubungi Leader outlet Anda.</p>
            <button type="button" class="btn-modal-close" onclick="closeForgotPasswordModal()">Tutup</button>
        </div>
    </div>

    <script>
        // Toggle password visibility with crisp SVG icon change
        let passwordShown = false;
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            passwordShown = !passwordShown;
            
            if (passwordShown) {
                passwordInput.type = 'text';
                toggleIcon.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>`;
            } else {
                passwordInput.type = 'password';
                toggleIcon.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>`;
            }
        }

        // Quick fill credentials with smooth flash feedback
        function fillCredentials(email, password) {
            const emailInput = document.getElementById('email');
            const passInput = document.getElementById('password');
            
            emailInput.value = email;
            passInput.value = password;

            // Flash animation feedback
            emailInput.style.borderColor = 'var(--hokben-yellow-dark)';
            passInput.style.borderColor = 'var(--hokben-yellow-dark)';
            emailInput.style.boxShadow = '0 0 0 4px rgba(245, 158, 11, 0.25)';
            passInput.style.boxShadow = '0 0 0 4px rgba(245, 158, 11, 0.25)';
            
            setTimeout(() => {
                emailInput.style.borderColor = '';
                passInput.style.borderColor = '';
                emailInput.style.boxShadow = '';
                passInput.style.boxShadow = '';
            }, 600);
        }

        // Forgot password modal
        function openForgotPasswordModal(e) {
            if (e) e.preventDefault();
            document.getElementById('forgotModal').classList.add('active');
        }

        function closeForgotPasswordModal() {
            document.getElementById('forgotModal').classList.remove('active');
        }

        window.addEventListener('click', function(e) {
            const modal = document.getElementById('forgotModal');
            if (e.target === modal) {
                closeForgotPasswordModal();
            }
        });
    </script>
</body>
</html>
