<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HokBen OpsTrack Portal</title>
    <link rel="icon" type="image/png" href="{{ asset('hokben-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 10% 15%, rgba(217, 34, 42, 0.22) 0%, transparent 45%),
                        radial-gradient(circle at 90% 85%, rgba(255, 196, 0, 0.15) 0%, transparent 45%),
                        #0c0f17;
            font-family: 'Inter', sans-serif;
            color: #f8fafc;
            position: relative;
            overflow: hidden;
        }

        /* Subtle glowing background accents */
        .bg-glow-1 {
            position: absolute;
            top: -150px;
            left: -150px;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(217, 34, 42, 0.25) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(50px);
            pointer-events: none;
        }

        .bg-glow-2 {
            position: absolute;
            bottom: -150px;
            right: -150px;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(255, 196, 0, 0.18) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(50px);
            pointer-events: none;
        }

        .login-wrapper {
            width: 100%;
            max-width: 450px;
            padding: 24px;
            box-sizing: border-box;
            position: relative;
            z-index: 10;
        }

        .login-card {
            background: rgba(18, 24, 38, 0.78);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 42px 34px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7),
                        0 0 40px -10px rgba(217, 34, 42, 0.25);
        }

        .brand-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .brand-logo-container {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            transition: transform 0.3s ease;
        }

        .brand-logo-container:hover {
            transform: scale(1.05);
        }

        .brand-logo-container img {
            height: 72px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 6px 14px rgba(217, 34, 42, 0.5));
        }

        .brand-header h1 {
            font-size: 24px;
            font-weight: 800;
            margin: 0 0 6px 0;
            color: #ffffff;
            letter-spacing: -0.5px;
        }

        .brand-header h1 span {
            color: #FFC400;
        }

        .brand-header p {
            font-size: 13.5px;
            color: #94a3b8;
            margin: 0;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #cbd5e1;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i.input-icon {
            position: absolute;
            left: 14px;
            font-size: 18px;
            color: #64748b;
            pointer-events: none;
            transition: color 0.2s;
        }

        .input-wrapper input {
            width: 100%;
            background: rgba(12, 15, 23, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            padding: 13px 14px 13px 42px;
            font-size: 14px;
            color: #ffffff;
            box-sizing: border-box;
            transition: all 0.25s ease;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: #D9222A;
            background: rgba(12, 15, 23, 0.95);
            box-shadow: 0 0 0 3px rgba(217, 34, 42, 0.25);
        }

        .input-wrapper input:focus + .input-icon,
        .input-wrapper input:focus ~ .input-icon {
            color: #FFC400;
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            padding: 0;
            font-size: 18px;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: #f8fafc;
        }

        .field-error {
            font-size: 12px;
            color: #f87171;
            margin-top: 6px;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 13px;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #94a3b8;
            cursor: pointer;
            user-select: none;
        }

        .checkbox-container input {
            cursor: pointer;
            accent-color: #D9222A;
            width: 16px;
            height: 16px;
        }

        .btn-submit {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #D9222A 0%, #b8161d 100%);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 10px 22px -5px rgba(217, 34, 42, 0.5);
            transition: all 0.25s ease;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #e62831 0%, #c4181f 100%);
            box-shadow: 0 12px 28px -5px rgba(217, 34, 42, 0.65), 0 0 15px rgba(255, 196, 0, 0.2);
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(1px);
        }

        .demo-credentials {
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
            line-height: 1.6;
        }

        .demo-credentials code {
            background: rgba(12, 15, 23, 0.85);
            padding: 2px 7px;
            border-radius: 5px;
            color: #FFC400;
            border: 1px solid rgba(255, 255, 255, 0.08);
            font-family: monospace;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="bg-glow-1"></div>
    <div class="bg-glow-2"></div>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="brand-header">
                <div class="brand-logo-container">
                    <img src="{{ asset('hokben-logo.svg') }}" alt="HokBen Logo" onerror="this.src='{{ asset('hokben-icon.png') }}'">
                </div>
                <h1>OpsTrack <span>Portal</span></h1>
                <p>Monitoring Operasional & Kepatuhan Store</p>
            </div>

            @if(session('success'))
                <div class="alert-success">
                    <i class="ti ti-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any() && !$errors->has('email') && !$errors->has('password'))
                <div class="alert-error">
                    <i class="ti ti-alert-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <div class="input-wrapper">
                        <i class="ti ti-mail input-icon"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="admin@hokben.co.id" required autofocus autocomplete="username">
                    </div>
                    @error('email')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <i class="ti ti-lock input-icon"></i>
                        <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility()" aria-label="Toggle password">
                            <i class="ti ti-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-options">
                    <label class="checkbox-container">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="ti ti-login"></i>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>

            <div class="demo-credentials">
                <span>Data akun tersimpan di tabel <code>users</code> MySQL</span><br>
                <span>Email & password dapat diubah langsung di database.</span>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('ti-eye');
                toggleIcon.classList.add('ti-eye-off');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('ti-eye-off');
                toggleIcon.classList.add('ti-eye');
            }
        }
    </script>
</body>
</html>
