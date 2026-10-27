<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ config('school.system_name') }} — Masuk ke sistem pembelajaran digital">
    <link rel="icon" type="image/png" href="{{ config('school.brand.favicon', '/images/logo-sekolah.png') }}">
    <title>Login – {{ config('school.system_short', 'Sekolah Digital') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --bg-base: #ebf1f8;
            --bg-card: #edf3fa;
            --text-title: #0f172a;
            --text-subtitle: #64748b;
            --text-muted: #94a3b8;
            --text-body: #334155;
            --neu-shadow-light: #ffffff;
            --neu-shadow-dark: rgba(166, 180, 200, 0.45);
            --neu-shadow-dark-subtle: rgba(166, 180, 200, 0.3);
            --border-glass: rgba(255, 255, 255, 0.85);
            --input-bg: #ebf1f8;
            --input-text: #0f172a;
            --input-placeholder: #94a3b8;
            --btn-gradient-from: #3b82f6;
            --btn-gradient-mid: #1d4ed8;
            --btn-gradient-to: #1e40af;
            --btn-shadow: rgba(37, 99, 235, 0.4);
            --decor-bubble-1: rgba(59, 130, 246, 0.15);
            --decor-bubble-2: rgba(99, 102, 241, 0.12);
        }

        html.dark {
            --bg-base: #0b1120;
            --bg-card: #0f172a;
            --text-title: #f8fafc;
            --text-subtitle: #94a3b8;
            --text-muted: #64748b;
            --text-body: #cbd5e1;
            --neu-shadow-light: rgba(255, 255, 255, 0.04);
            --neu-shadow-dark: rgba(0, 0, 0, 0.65);
            --neu-shadow-dark-subtle: rgba(0, 0, 0, 0.5);
            --border-glass: rgba(255, 255, 255, 0.08);
            --input-bg: #111a2e;
            --input-text: #f8fafc;
            --input-placeholder: #64748b;
            --btn-gradient-from: #2563eb;
            --btn-gradient-mid: #1d4ed8;
            --btn-gradient-to: #1e3a8a;
            --btn-shadow: rgba(37, 99, 235, 0.35);
            --decor-bubble-1: rgba(37, 99, 235, 0.1);
            --decor-bubble-2: rgba(99, 102, 241, 0.08);
        }

        * {
            box-sizing: border-box;
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-base);
            color: var(--text-body);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Ambient floating neumorphic spheres & rings */
        .ambient-sphere {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
        }

        .ambient-top-left-ring {
            top: -120px;
            left: -120px;
            width: 380px;
            height: 380px;
            border: 24px solid var(--neu-shadow-light);
            border-radius: 50%;
            box-shadow: 18px 18px 36px var(--neu-shadow-dark-subtle),
                        inset 12px 12px 24px var(--neu-shadow-dark-subtle),
                        inset -12px -12px 24px var(--neu-shadow-light);
            opacity: 0.85;
        }

        .ambient-top-left-glow {
            top: 40px;
            left: 20px;
            width: 140px;
            height: 140px;
            background: radial-gradient(circle, var(--decor-bubble-1) 0%, transparent 70%);
            filter: blur(20px);
        }

        .ambient-top-right-sphere {
            top: 140px;
            right: -60px;
            width: 220px;
            height: 220px;
            background: var(--bg-base);
            box-shadow: 16px 16px 36px var(--neu-shadow-dark),
                        -16px -16px 36px var(--neu-shadow-light),
                        inset -8px -8px 20px var(--decor-bubble-1);
        }

        .ambient-bottom-left-sphere {
            bottom: -90px;
            left: -80px;
            width: 260px;
            height: 260px;
            border: 18px solid var(--neu-shadow-light);
            border-radius: 50%;
            box-shadow: 16px 16px 34px var(--neu-shadow-dark-subtle);
            opacity: 0.7;
        }

        /* Theme Toggle Button (Top Right) */
        .theme-toggle-btn {
            position: fixed;
            top: 24px;
            right: 28px;
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            box-shadow: 6px 6px 14px var(--neu-shadow-dark-subtle),
                        -6px -6px 14px var(--neu-shadow-light);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 50;
            color: var(--text-title);
            font-size: 16px;
            outline: none;
        }

        .theme-toggle-btn:hover {
            transform: scale(1.05);
        }

        .theme-toggle-btn:active {
            box-shadow: inset 3px 3px 6px var(--neu-shadow-dark-subtle),
                        inset -3px -3px 6px var(--neu-shadow-light);
        }

        /* Main Container */
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 390px;
            padding: 24px 26px 18px;
            margin: auto;
            border-radius: 36px;
            background: var(--bg-card);
            border: 1.5px solid var(--border-glass);
            box-shadow: 18px 18px 40px var(--neu-shadow-dark),
                        -18px -18px 40px var(--neu-shadow-light);
            text-align: center;
        }

        /* 3D Neumorphic Logo Badge */
        .logo-squircle {
            width: 84px;
            height: 84px;
            margin: 0 auto 12px;
            border-radius: 26px;
            background: var(--bg-card);
            border: 1.5px solid var(--border-glass);
            box-shadow: 8px 8px 20px var(--neu-shadow-dark),
                        -8px -8px 20px var(--neu-shadow-light),
                        inset 2px 2px 4px rgba(255, 255, 255, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 10px;
        }

        .logo-squircle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            filter: drop-shadow(0 6px 12px rgba(37, 99, 235, 0.3));
        }

        /* Headings */
        .login-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-title);
            margin: 0 0 2px;
            letter-spacing: -0.5px;
        }

        .login-subtitle {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-subtitle);
            margin: 0 0 16px;
            letter-spacing: 0.5px;
        }

        /* Alert Box */
        .alert-error {
            background: rgba(244, 63, 94, 0.1);
            border: 1px solid rgba(244, 63, 94, 0.25);
            color: #e11d48;
            padding: 12px 16px;
            border-radius: 18px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-align: left;
        }

        html.dark .alert-error {
            color: #fb7185;
            background: rgba(244, 63, 94, 0.15);
        }

        /* Neumorphic Input Group */
        .input-pill {
            position: relative;
            margin-bottom: 12px;
            width: 100%;
            height: 50px;
            border-radius: 20px;
            background: var(--input-bg);
            border: 1px solid var(--border-glass);
            box-shadow: 5px 5px 12px var(--neu-shadow-dark-subtle),
                        -5px -5px 12px var(--neu-shadow-light);
            display: flex;
            align-items: center;
            padding: 0 18px;
            transition: all 0.25s ease;
        }

        .input-pill:focus-within {
            border-color: #3b82f6;
            box-shadow: inset 4px 4px 8px var(--neu-shadow-dark-subtle),
                        inset -4px -4px 8px var(--neu-shadow-light),
                        0 0 0 3px rgba(59, 130, 246, 0.25);
        }

        .input-pill .input-icon {
            font-size: 17px;
            color: var(--text-subtitle);
            margin-right: 12px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
        }

        .input-pill input {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            font-family: inherit;
            font-size: 13.5px;
            font-weight: 500;
            color: var(--input-text);
        }

        .input-pill input::placeholder {
            color: var(--input-placeholder);
            font-weight: 400;
        }

        .input-pill .toggle-pwd-btn {
            background: none;
            border: none;
            outline: none;
            color: var(--text-subtitle);
            cursor: pointer;
            padding: 4px;
            font-size: 17px;
            display: flex;
            align-items: center;
        }

        .input-pill .toggle-pwd-btn:hover {
            color: var(--text-title);
        }

        /* Options Row: Remember & Forgot */
        .options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding: 0 4px;
            font-size: 12px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: var(--text-body);
            font-weight: 500;
            user-select: none;
        }

        .checkbox-label input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 17px;
            height: 17px;
            border-radius: 5px;
            background: var(--input-bg);
            border: 1px solid var(--border-glass);
            box-shadow: 2px 2px 5px var(--neu-shadow-dark-subtle),
                        -2px -2px 5px var(--neu-shadow-light);
            cursor: pointer;
            position: relative;
            outline: none;
            transition: all 0.2s ease;
        }

        .checkbox-label input[type="checkbox"]:checked {
            background: #2563eb;
            border-color: #2563eb;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.4);
        }

        .checkbox-label input[type="checkbox"]:checked::after {
            content: '';
            position: absolute;
            left: 5px;
            top: 2px;
            width: 4px;
            height: 8px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .forgot-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
            font-size: 12.5px;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .forgot-link:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        /* Glowing Blue Pill Button */
        .btn-login {
            width: 100%;
            height: 50px;
            border-radius: 25px;
            border: none;
            outline: none;
            background: linear-gradient(135deg, var(--btn-gradient-from) 0%, var(--btn-gradient-mid) 50%, var(--btn-gradient-to) 100%);
            box-shadow: 0 10px 24px -2px var(--btn-shadow),
                        0 4px 10px -2px var(--btn-shadow);
            color: #ffffff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            padding: 0 8px 0 24px;
            transition: all 0.25s ease;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px -2px var(--btn-shadow),
                        0 6px 14px -2px var(--btn-shadow);
            filter: brightness(1.05);
        }

        .btn-login:active {
            transform: translateY(1px);
            box-shadow: 0 6px 16px -2px var(--btn-shadow);
        }

        .btn-login .btn-text {
            flex-grow: 1;
            text-align: center;
            padding-left: 30px;
        }

        .btn-login .btn-arrow-badge {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            color: #ffffff;
            transition: transform 0.25s ease;
            flex-shrink: 0;
        }

        .btn-login:hover .btn-arrow-badge {
            transform: translateX(3px);
            background: rgba(255, 255, 255, 0.3);
        }

        /* Divider with subtle line */
        .divider-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 16px 0 12px;
            position: relative;
        }

        .divider-line {
            flex-grow: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, var(--neu-shadow-dark-subtle) 50%, transparent 100%);
        }

        .divider-text {
            padding: 0 14px;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 2px;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        /* Social / Demo Quick Fill Squircles */
        .social-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-bottom: 14px;
        }

        .social-btn {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: var(--bg-card);
            border: 1.5px solid var(--border-glass);
            box-shadow: 5px 5px 12px var(--neu-shadow-dark-subtle),
                        -5px -5px 12px var(--neu-shadow-light);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            outline: none;
            position: relative;
            transition: all 0.2s ease;
        }

        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: 7px 7px 16px var(--neu-shadow-dark-subtle),
                        -7px -7px 16px var(--neu-shadow-light);
        }

        .social-btn:active {
            transform: translateY(1px);
            box-shadow: inset 3px 3px 6px var(--neu-shadow-dark-subtle),
                        inset -3px -3px 6px var(--neu-shadow-light);
        }

        .social-btn svg {
            width: 20px;
            height: 20px;
        }

        /* Bottom Footer */
        .footer-tagline {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        /* Quick Account Helper Modal / Bottom Sheet */
        .role-helper-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(8px);
            z-index: 100;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .role-helper-modal.active {
            display: flex;
        }

        .role-helper-card {
            background: var(--bg-card);
            border: 1.5px solid var(--border-glass);
            border-radius: 28px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            max-width: 380px;
            width: 100%;
            padding: 24px;
            text-align: left;
        }

        .role-pill-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 12px 16px;
            margin-bottom: 10px;
            border-radius: 16px;
            background: var(--input-bg);
            border: 1px solid var(--border-glass);
            box-shadow: 4px 4px 10px var(--neu-shadow-dark-subtle),
                        -4px -4px 10px var(--neu-shadow-light);
            cursor: pointer;
            text-align: left;
            transition: all 0.2s ease;
        }

        .role-pill-btn:hover {
            border-color: #3b82f6;
            transform: translateY(-1px);
        }

        @media (max-height: 600px) {
            body {
                overflow-y: auto;
            }
        }

        @media (max-width: 480px) {
            body {
                overflow-y: auto;
            }
            .login-wrapper {
                box-shadow: none;
                border: none;
                background: transparent;
                padding: 16px 12px;
            }
            .ambient-top-left-ring, .ambient-top-right-sphere, .ambient-bottom-left-sphere {
                opacity: 0.4;
            }
        }
    </style>
</head>

<body>
    <!-- Floating Neumorphic Ambient Ornaments -->
    <div class="ambient-sphere ambient-top-left-ring"></div>
    <div class="ambient-sphere ambient-top-left-glow"></div>
    <div class="ambient-sphere ambient-top-right-sphere"></div>
    <div class="ambient-sphere ambient-bottom-left-sphere"></div>

    <!-- Dark Mode Toggle Button (Top Right) -->
    <button type="button" class="theme-toggle-btn" id="themeToggleBtn" title="Ganti Tema (Light/Dark)">
        <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
    </button>

    <!-- Main Neumorphic Card -->
    <div class="login-wrapper">
        <!-- Center Logo Badge -->
        <div class="logo-squircle">
            <img src="{{ config('school.brand.logo', '/images/logo-sekolah.png') }}" alt="{{ config('school.name') }}">
        </div>

        <!-- Typography -->
        <h1 class="login-title">Welcome Back</h1>
        <p class="login-subtitle">Sign in to continue</p>

        @if($errors->any())
            <div class="alert-error">
                <i class="bi bi-exclamation-circle-fill" style="font-size: 16px;"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" id="loginForm">
            @csrf

            <!-- Username Input -->
            <div class="input-pill">
                <span class="input-icon">
                    <i class="bi bi-person"></i>
                </span>
                <input type="text" id="username" name="username" placeholder="Username or Email" value="{{ old('username') }}" required autofocus autocomplete="username">
            </div>

            <!-- Password Input -->
            <div class="input-pill">
                <span class="input-icon">
                    <i class="bi bi-shield-lock"></i>
                </span>
                <input type="password" id="password" name="password" placeholder="Password" required autocomplete="current-password">
                <button type="button" class="toggle-pwd-btn" onclick="togglePasswordVisibility('password', this)" title="Lihat password">
                    <i class="bi bi-eye"></i>
                </button>
            </div>

            <!-- Options Row -->
            <div class="options-row">
                <label class="checkbox-label" for="remember">
                    <input type="checkbox" id="remember" name="remember">
                    <span>Remember Me</span>
                </label>
                <a href="javascript:void(0)" class="forgot-link" onclick="openRoleModal()">Quick Demo?</a>
            </div>

            <!-- Main Glowing Blue Pill Button -->
            <button type="submit" class="btn-login" id="submitBtn">
                <span class="btn-text">Login</span>
                <span class="btn-arrow-badge">
                    <i class="bi bi-arrow-right"></i>
                </span>
            </button>
        </form>

        <!-- Divider -->
        <div class="divider-container">
            <span class="divider-line"></span>
            <span class="divider-text">OR DEMO ACCOUNT</span>
            <span class="divider-line"></span>
        </div>

        <!-- Role Quick Fill Neumorphic Squircles -->
        <div class="social-row">
            <!-- Super Admin Button -->
            <button type="button" class="social-btn" title="Login Cepat: Super Admin" onclick="quickFill('superadmin', 'superadmin123')">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none">
                    <circle cx="12" cy="12" r="10" fill="#3b82f6" opacity="0.15"/>
                    <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="#2563eb"/>
                </svg>
            </button>

            <!-- Guru Button -->
            <button type="button" class="social-btn" title="Login Cepat: Guru (Budi Santoso)" onclick="quickFill('198503052019031002', 'guru123')">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none">
                    <circle cx="12" cy="12" r="10" fill="#6366f1" opacity="0.15"/>
                    <path d="M12 3L1 9L12 15L21 10.09V17H23V9M5 13.18V17.18L12 21L19 17.18V13.18L12 17L5 13.18Z" fill="#4f46e5"/>
                </svg>
            </button>

            <!-- Siswa Button -->
            <button type="button" class="social-btn" title="Login Cepat: Siswa (Andi Kurniawan)" onclick="quickFill('2025001', '2025001')">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none">
                    <circle cx="12" cy="12" r="10" fill="#10b981" opacity="0.15"/>
                    <path d="M12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z" fill="#059669"/>
                </svg>
            </button>
        </div>

        <!-- Footer Tagline -->
        <div class="footer-tagline">
            LEARN &bull; COLLABORATE &bull; GROW TOGETHER
        </div>
    </div>

    <!-- Quick Role Helper Modal -->
    <div class="role-helper-modal" id="roleModal" onclick="if(event.target === this) closeRoleModal()">
        <div class="role-helper-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: var(--text-title);">Pilih Akun Demo</h3>
                <button type="button" onclick="closeRoleModal()" style="background: none; border: none; font-size: 20px; color: var(--text-subtitle); cursor: pointer;">&times;</button>
            </div>
            <p style="font-size: 12px; color: var(--text-subtitle); margin-top: 0; margin-bottom: 18px;">Klik akun di bawah untuk otomatis mengisi dan login ke dashboard:</p>

            <div class="role-pill-btn" onclick="quickFill('superadmin', 'superadmin123', true)">
                <div>
                    <strong style="display:block; font-size: 14px; color: var(--text-title);">Super Admin</strong>
                    <span style="font-size: 11px; color: var(--text-subtitle);">superadmin &bull; superadmin123</span>
                </div>
                <i class="bi bi-box-arrow-in-right" style="color: #2563eb; font-size: 18px;"></i>
            </div>

            <div class="role-pill-btn" onclick="quickFill('198001012005011003', 'kepsek123', true)">
                <div>
                    <strong style="display:block; font-size: 14px; color: var(--text-title);">Kepala Sekolah</strong>
                    <span style="font-size: 11px; color: var(--text-subtitle);">198001012005011003 &bull; kepsek123</span>
                </div>
                <i class="bi bi-box-arrow-in-right" style="color: #6366f1; font-size: 18px;"></i>
            </div>

            <div class="role-pill-btn" onclick="quickFill('198503052019031002', 'guru123', true)">
                <div>
                    <strong style="display:block; font-size: 14px; color: var(--text-title);">Guru (Budi Santoso, S.Pd)</strong>
                    <span style="font-size: 11px; color: var(--text-subtitle);">198503052019031002 &bull; guru123</span>
                </div>
                <i class="bi bi-box-arrow-in-right" style="color: #4f46e5; font-size: 18px;"></i>
            </div>

            <div class="role-pill-btn" onclick="quickFill('2025001', '2025001', true)">
                <div>
                    <strong style="display:block; font-size: 14px; color: var(--text-title);">Siswa (Andi Kurniawan)</strong>
                    <span style="font-size: 11px; color: var(--text-subtitle);">2025001 &bull; 2025001 (XII IPA 1)</span>
                </div>
                <i class="bi bi-box-arrow-in-right" style="color: #059669; font-size: 18px;"></i>
            </div>
        </div>
    </div>

    <script>
        // Password Visibility Toggle
        function togglePasswordVisibility(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        // Quick Fill Demo Credentials
        function quickFill(user, pwd, autoSubmit = false) {
            document.getElementById('username').value = user;
            document.getElementById('password').value = pwd;
            closeRoleModal();
            if (autoSubmit) {
                document.getElementById('loginForm').submit();
            }
        }

        function openRoleModal() {
            document.getElementById('roleModal').classList.add('active');
        }

        function closeRoleModal() {
            document.getElementById('roleModal').classList.remove('active');
        }

        // Dark / Light Neumorphic Mode Switcher
        const themeBtn = document.getElementById('themeToggleBtn');
        const themeIcon = document.getElementById('themeIcon');
        const savedTheme = localStorage.getItem('lms_theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

        if (savedTheme === 'dark') {
            document.documentElement.classList.add('dark');
            themeIcon.className = 'bi bi-sun-fill';
        } else {
            document.documentElement.classList.remove('dark');
            themeIcon.className = 'bi bi-moon-stars-fill';
        }

        themeBtn.addEventListener('click', () => {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('lms_theme', isDark ? 'dark' : 'light');
            themeIcon.className = isDark ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
        });
    </script>
</body>

</html>
