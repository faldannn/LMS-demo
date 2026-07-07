{{-- resources/views/students/print-card.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kartu Pelajar - {{ $student->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: var(--color-brand-500);
            --primary-dark: #1e3a8a;
            --primary-light: #3b82f6;
            --accent: #06b6d4;
            --accent-glow: rgba(6, 182, 212, 0.3);
            --surface: #ffffff;
            --surface-dim: #f1f5f9;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --border: #e2e8f0;
            --success: var(--color-brand-500);
            --gold: #f59e0b;
            --card-w: 340px;
            --card-h: 540px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(160deg, #0f172a 0%, #1e3a8a 40%, var(--color-brand-600) 70%, #0ea5e9 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient background orbs */
        body::before,
        body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
            z-index: 0;
            pointer-events: none;
        }
        body::before {
            width: 400px; height: 400px;
            background: var(--accent);
            top: -100px; right: -100px;
            animation: float 8s ease-in-out infinite;
        }
        body::after {
            width: 300px; height: 300px;
            background: #8b5cf6;
            bottom: -80px; left: -80px;
            animation: float 10s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, -30px) scale(1.1); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.9); opacity: 0.6; }
            50% { transform: scale(1.05); opacity: 0.3; }
            100% { transform: scale(0.9); opacity: 0.6; }
        }

        /* ====== Page Title ====== */
        .page-title {
            text-align: center;
            margin-bottom: 28px;
            z-index: 1;
            animation: fadeInUp 0.6s ease-out;
        }
        .page-title h1 {
            font-size: 14px;
            font-weight: 600;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 4px;
        }
        .page-title p {
            font-size: 11px;
            color: rgba(255,255,255,0.3);
        }

        /* ====== Card Scene (3D perspective) ====== */
        .card-scene {
            perspective: 1200px;
            z-index: 1;
            animation: fadeInUp 0.8s ease-out 0.1s both;
        }
        .card-flip {
            width: var(--card-w);
            height: var(--card-h);
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.7s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }
        .card-flip.flipped {
            transform: rotateY(180deg);
        }

        .card-face {
            position: absolute;
            inset: 0;
            backface-visibility: hidden;
            border-radius: 20px;
            overflow: hidden;
            box-shadow:
                0 25px 50px -12px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(255, 255, 255, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }
        .card-front { z-index: 2; }
        .card-back {
            transform: rotateY(180deg);
            z-index: 1;
        }

        /* ====== FRONT FACE ====== */
        .card-front {
            background: var(--surface);
            display: flex;
            flex-direction: column;
        }

        /* Header with gradient */
        .card-header-section {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 50%, var(--primary-light) 100%);
            padding: 22px 20px 48px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .card-header-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .card-header-section::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 40px;
            background: var(--surface);
            border-radius: 50% 50% 0 0 / 100% 100% 0 0;
        }

        .card-type-badge {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: rgba(255,255,255,0.9);
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            padding: 4px 14px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.15);
            margin-bottom: 10px;
        }

        .school-name {
            font-size: 15px;
            font-weight: 800;
            color: white;
            letter-spacing: 0.5px;
            text-shadow: 0 1px 3px rgba(0,0,0,0.2);
            margin-bottom: 4px;
            position: relative;
            z-index: 1;
        }
        .school-address {
            font-size: 10px;
            color: rgba(255,255,255,0.7);
            position: relative;
            z-index: 1;
        }

        /* Body content */
        .card-body-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 24px 16px;
            position: relative;
        }

        /* Photo with ring */
        .photo-ring {
            margin-top: -36px;
            position: relative;
            z-index: 3;
        }
        .photo-ring::before {
            content: '';
            position: absolute;
            inset: -5px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-light), var(--accent));
            z-index: -1;
            animation: pulse-ring 3s ease-in-out infinite;
        }
        .student-photo {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--surface);
            display: block;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Student info */
        .student-info {
            text-align: center;
            margin-top: 14px;
            width: 100%;
        }
        .student-name {
            font-size: 17px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.3px;
            margin-bottom: 6px;
            line-height: 1.2;
        }
        .student-class {
            font-size: 12px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 8px;
        }
        .nis-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            border: 1px solid #bae6fd;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: var(--primary-dark);
        }
        .nis-chip .label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--text-muted);
        }
        .nis-chip .value {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            font-size: 13px;
        }

        /* Divider */
        .divider {
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-light), var(--accent));
            border-radius: 3px;
            margin: 16px auto;
        }

        /* QR Section */
        .qr-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .qr-frame {
            background: white;
            padding: 10px;
            border-radius: 14px;
            border: 2px solid var(--border);
            box-shadow:
                0 4px 6px -1px rgba(0,0,0,0.05),
                0 2px 4px -2px rgba(0,0,0,0.05);
            position: relative;
        }
        .qr-frame::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 15px;
            background: linear-gradient(135deg, var(--primary-light), var(--accent));
            z-index: -1;
        }
        .qr-frame svg {
            display: block;
            width: 120px;
            height: 120px;
        }
        .qr-label {
            font-size: 9px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 10px;
        }

        /* Footer */
        .card-footer-section {
            background: var(--surface-dim);
            border-top: 1px solid var(--border);
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .footer-brand {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .footer-brand .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 6px var(--success);
        }
        .footer-brand span {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-secondary);
            letter-spacing: 1px;
        }
        .footer-text {
            font-size: 9px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ====== BACK FACE ====== */
        .card-back {
            background: linear-gradient(160deg, var(--primary-dark) 0%, var(--primary) 100%);
            display: flex;
            flex-direction: column;
            color: white;
        }

        .back-header {
            padding: 24px 24px 16px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .back-header h3 {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.9);
        }

        .back-body {
            flex: 1;
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .info-row {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }
        .info-row .info-label {
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.5);
        }
        .info-row .info-value {
            font-size: 13px;
            font-weight: 600;
            color: white;
        }

        .back-rules {
            padding: 16px 24px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .back-rules h4 {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.5);
            margin-bottom: 10px;
        }
        .back-rules ul {
            list-style: none;
            padding: 0;
        }
        .back-rules li {
            font-size: 10px;
            color: rgba(255,255,255,0.7);
            padding: 4px 0;
            padding-left: 16px;
            position: relative;
            line-height: 1.4;
        }
        .back-rules li::before {
            content: '•';
            position: absolute;
            left: 0;
            color: var(--accent);
            font-weight: 700;
        }

        .back-footer {
            padding: 16px 24px;
            border-top: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        .back-footer p {
            font-size: 9px;
            color: rgba(255,255,255,0.4);
            line-height: 1.5;
        }
        .back-footer strong {
            color: rgba(255,255,255,0.7);
        }

        /* ====== Flip Hint ====== */
        .flip-hint {
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.4);
            font-size: 12px;
            font-weight: 500;
            z-index: 1;
            animation: fadeInUp 0.8s ease-out 0.3s both;
            cursor: pointer;
            transition: color 0.2s;
        }
        .flip-hint:hover { color: rgba(255,255,255,0.7); }
        .flip-hint svg {
            width: 16px; height: 16px;
            animation: float 2s ease-in-out infinite;
        }

        /* ====== Action Buttons ====== */
        .action-bar {
            display: flex;
            gap: 10px;
            margin-top: 24px;
            z-index: 1;
            animation: fadeInUp 0.8s ease-out 0.5s both;
        }
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 14px;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
        }
        .btn-print {
            background: linear-gradient(135deg, var(--success), #059669);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5);
        }
        .btn-print:disabled {
            opacity: 0.6 !important;
            cursor: not-allowed !important;
            transform: none !important;
        }
        .btn-download {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .btn-download:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }
        .btn-download:disabled {
            opacity: 0.6 !important;
            cursor: not-allowed !important;
            transform: none !important;
        }

        /* ====== RESPONSIVE — MOBILE ====== */
        @media (max-width: 420px) {
            :root {
                --card-w: calc(100vw - 40px);
                --card-h: auto;
            }
            body {
                padding: 16px 12px;
                justify-content: flex-start;
                min-height: 100dvh;
            }
            .card-flip {
                height: auto;
            }
            .card-face {
                position: relative;
            }
            .card-back {
                display: none;
            }
            .card-flip.flipped {
                transform: none;
            }
            .card-flip.flipped .card-front {
                display: none;
            }
            .card-flip.flipped .card-back {
                display: flex;
                transform: none;
            }

            .card-header-section {
                padding: 16px 14px 38px;
            }
            .school-name { 
                font-size: 13px; 
                margin-bottom: 2px;
            }
            .school-address {
                font-size: 9px;
            }
            .student-photo { 
                width: 74px; 
                height: 74px; 
            }
            .photo-ring { 
                margin-top: -30px; 
            }
            .student-name { 
                font-size: 15px;
                margin-bottom: 4px;
            }
            .student-class {
                font-size: 11px;
                margin-bottom: 6px;
            }
            .card-body-section { 
                padding: 0 16px 12px; 
            }
            .qr-frame svg { 
                width: 100px; 
                height: 100px; 
            }
            .action-bar { 
                flex-direction: column; 
                width: 100%; 
                max-width: var(--card-w);
                gap: 8px;
            }
            .action-btn { 
                justify-content: center;
                width: 100%;
                padding: 13px 20px;
                font-size: 14px;
            }
            .divider {
                width: 40px;
                margin: 12px auto;
            }
        }

        /* ====== TABLET ====== */
        @media (min-width: 421px) and (max-width: 768px) {
            :root {
                --card-w: 340px;
                --card-h: 540px;
            }
        }

        /* ====== DESKTOP ====== */
        @media (min-width: 769px) {
            :root {
                --card-w: 360px;
                --card-h: 570px;
            }
            .qr-frame svg { width: 130px; height: 130px; }
            .student-photo { width: 96px; height: 96px; }
            .photo-ring { margin-top: -40px; }
            .card-header-section {
                padding: 24px 22px 52px;
            }
            .student-name { font-size: 18px; }
            .student-class { font-size: 13px; margin-bottom: 10px; }
            .divider { margin: 18px auto; }
            .qr-section { 
                flex: 0 0 auto;
                justify-content: flex-end;
                padding-bottom: 8px;
            }
        }

        /* ====== PRINT STYLES ====== */
        @media print {
            :root {
                --print-card-w: 63mm;
                --print-card-h: 100mm;
            }
            @page {
                size: A4 portrait;
                margin: 0;
                padding: 0;
            }
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                min-height: 100% !important;
                display: flex !important;
                align-items: flex-start !important;
                justify-content: center !important;
            }
            body::before, body::after { display: none !important; }

            .page-title,
            .flip-hint,
            .action-bar { display: none !important; }

            .card-scene { 
                animation: none !important;
                perspective: none !important;
                width: 100% !important;
                display: flex !important;
                justify-content: center !important;
                padding-top: 14mm !important;
            }
            .card-flip {
                width: auto !important;
                height: auto !important;
                transform: none !important;
                cursor: default !important;
                transform-style: flat !important;
                display: grid !important;
                grid-template-columns: var(--print-card-w) var(--print-card-w) !important;
                gap: 6mm !important;
                align-items: start !important;
            }
            .card-face {
                width: var(--print-card-w) !important;
                height: var(--print-card-h) !important;
                border-radius: 8px !important;
                box-shadow: 0 0 2px rgba(0,0,0,0.1) !important;
                border: none !important;
                position: relative !important;
                backface-visibility: visible !important;
                inset: auto !important;
                overflow: hidden !important;
            }
            .card-front { 
                display: flex !important;
                transform: none !important;
                z-index: 2 !important;
            }
            .card-back {
                display: flex !important;
                transform: none !important;
                z-index: 1 !important;
            }

            .card-header-section {
                padding: 8px 10px 18px !important;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
                background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 50%, var(--primary-light) 100%) !important;
            }
            .card-header-section::after {
                height: 14px !important;
            }
            .card-type-badge { display: none !important; }
            .school-name { 
                font-size: 10px !important; 
                font-weight: 800 !important;
                margin-bottom: 2px !important;
            }
            .school-address { 
                font-size: 7px !important;
                line-height: 1.2 !important;
            }

            .card-body-section {
                padding: 0 10px 8px !important;
            }

            .photo-ring { 
                margin-top: -12px !important; 
            }
            .photo-ring::before { display: none !important; }
            .student-photo {
                width: 46px !important;
                height: 46px !important;
                border-width: 2px !important;
            }

            .student-info { 
                margin-top: 5px !important; 
            }
            .student-name { 
                font-size: 10px !important; 
                font-weight: 800 !important;
                margin-bottom: 1px !important; 
                line-height: 1.1 !important;
            }
            .student-class { 
                font-size: 8px !important; 
                margin-bottom: 2px !important; 
            }
            .nis-chip { 
                padding: 2px 8px !important; 
                font-size: 7px !important;
                background: #f0f9ff !important;
                border-color: #bae6fd !important;
            }
            .nis-chip .label { 
                font-size: 5.5px !important; 
            }
            .nis-chip .value { 
                font-size: 7px !important; 
            }

            .divider { 
                margin: 5px auto !important; 
                height: 1.5px !important; 
                width: 25px !important; 
            }

            .qr-section {
                flex: 1;
            }

            .qr-frame { 
                padding: 6px !important; 
                border-radius: 6px !important;
                border: 1px solid #bae6fd !important;
            }
            .qr-frame::before { display: none !important; }
            .qr-frame svg { 
                width: 55px !important; 
                height: 55px !important; 
            }
            .qr-label { 
                font-size: 6px !important; 
                margin-top: 3px !important; 
            }

            .card-footer-section { 
                padding: 4px 10px !important;
                background: #f1f5f9 !important;
                border-top-width: 0.5px !important;
            }
            .footer-brand .dot { display: none !important; }
            .footer-brand span { 
                font-size: 7px !important;
                font-weight: 700 !important;
            }
            .footer-text { 
                font-size: 6px !important; 
            }

            .back-header {
                padding: 8px 10px 6px !important;
            }
            .back-header h3 {
                font-size: 8px !important;
                letter-spacing: 1.2px !important;
            }
            .back-body {
                padding: 8px 10px !important;
                gap: 6px !important;
            }
            .info-row {
                gap: 1px !important;
            }
            .info-row .info-label {
                font-size: 6px !important;
                letter-spacing: 1px !important;
            }
            .info-row .info-value {
                font-size: 8px !important;
                line-height: 1.2 !important;
            }
            .back-rules {
                padding: 8px 10px !important;
            }
            .back-rules h4 {
                font-size: 7px !important;
                margin-bottom: 4px !important;
            }
            .back-rules li {
                font-size: 6px !important;
                line-height: 1.2 !important;
                padding: 2px 0 2px 9px !important;
            }
            .back-footer {
                padding: 6px 10px !important;
            }
            .back-footer p {
                font-size: 6px !important;
                line-height: 1.25 !important;
            }
        }
    </style>
</head>
<body>

    <!-- Page Title -->
    <div class="page-title">
        <h1>Kartu Pelajar Digital</h1>
        <p>{{ config('school.name') }}</p>
    </div>

    <!-- Card Scene -->
    <div class="card-scene">
        <div class="card-flip" id="cardFlip">

            <!-- ===== FRONT ===== -->
            <div class="card-face card-front">
                <div class="card-header-section">
                    <div class="card-type-badge">Kartu Pelajar</div>
                    <h2 class="school-name">{{ config('school.name') }}</h2>
                    <p class="school-address">{{ config('school.address', 'Jl. Ir. H. Juanda No. 93, Bandung') }}</p>
                </div>

                <div class="card-body-section">
                    <div class="photo-ring">
                        <img src="{{ $student->photo_url }}"
                             alt="Foto {{ $student->name }}"
                             class="student-photo"
                             onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect fill=%22%23e2e8f0%22 width=%22100%22 height=%22100%22/><text x=%2250%25%22 y=%2255%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2236%22 fill=%22%2394a3b8%22>👤</text></svg>'">
                    </div>

                    <div class="student-info">
                        <h2 class="student-name">{{ $student->name }}</h2>
                        <div class="student-class">{{ $student->class?->name ?? 'Belum ada kelas' }}</div>
                        <div class="nis-chip">
                            <span class="label">NIS</span>
                            <span class="value">{{ $student->nis }}</span>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="qr-section">
                        <div class="qr-frame">
                            {!! $qrSvg !!}
                        </div>
                        <div class="qr-label">Scan untuk absensi</div>
                    </div>
                </div>

                <div class="card-footer-section">
                    <div class="footer-brand">
                        <span class="dot"></span>
                        <span>LMS / E-Learning</span>
                    </div>
                    <div class="footer-text">Kartu Pelajar Digital</div>
                </div>
            </div>

            <!-- ===== BACK ===== -->
            <div class="card-face card-back">
                <div class="back-header">
                    <h3>Informasi Siswa</h3>
                </div>
                <div class="back-body">
                    <div class="info-row">
                        <span class="info-label">Nama Lengkap</span>
                        <span class="info-value">{{ $student->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">NIS / NISN</span>
                        <span class="info-value" style="font-family:'JetBrains Mono',monospace;">{{ $student->nis }} / {{ $student->nisn ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kelas</span>
                        <span class="info-value">{{ $student->class?->name ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tanggal Lahir</span>
                        <span class="info-value">{{ $student->dob?->translatedFormat('d F Y') ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Jenis Kelamin</span>
                        <span class="info-value">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                </div>
                <div class="back-rules">
                    <h4>Ketentuan</h4>
                    <ul>
                        <li>Kartu ini adalah milik siswa yang bersangkutan</li>
                        <li>Wajib dibawa saat kegiatan sekolah</li>
                        <li>Jika hilang, segera lapor ke bagian administrasi</li>
                        <li>Dilarang memindahtangankan kartu ini</li>
                    </ul>
                </div>
                <div class="back-footer">
                    <p>Dikeluarkan oleh<br><strong>{{ config('school.name') }}</strong></p>
                </div>
            </div>

        </div>
    </div>

    <!-- Flip Hint -->
    <div class="flip-hint" onclick="toggleFlip()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/>
            <path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/>
        </svg>
        <span id="flipText">Ketuk kartu untuk membalik</span>
    </div>

    <!-- Action Buttons -->
    <div class="action-bar">
        <button class="action-btn btn-print" id="btnPrint" onclick="handlePrint()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                <rect x="6" y="14" width="12" height="8"/>
            </svg>
            Cetak Kartu
        </button>
        <button class="action-btn btn-download" id="btnDownload" onclick="handleDownload()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Simpan Gambar
        </button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        const card = document.getElementById('cardFlip');
        const flipText = document.getElementById('flipText');
        const btnPrint = document.getElementById('btnPrint');
        const btnDownload = document.getElementById('btnDownload');

        function toggleFlip() {
            card.classList.toggle('flipped');
            flipText.textContent = card.classList.contains('flipped')
                ? 'Ketuk untuk melihat depan'
                : 'Ketuk kartu untuk membalik';
        }

        card.addEventListener('click', toggleFlip);

        function setButtonLoading(btn, isLoading, text = null) {
            if (isLoading) {
                btn.disabled = true;
                btn.style.opacity = '0.6';
                btn.style.cursor = 'not-allowed';
            } else {
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
            }
        }

        function handlePrint() {
            // Reset to front before printing
            card.classList.remove('flipped');
            setButtonLoading(btnPrint, true);
            
            setTimeout(() => {
                window.print();
                setButtonLoading(btnPrint, false);
            }, 300);
        }

        async function handleDownload() {
            setButtonLoading(btnDownload, true);
            const originalHTML = btnDownload.innerHTML;

            btnDownload.innerHTML = `
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                Memproses...
            `;

            const wasFlipped = card.classList.contains('flipped');
            card.classList.remove('flipped');

            await new Promise((resolve) => setTimeout(resolve, 320));

            const frontCard = document.querySelector('.card-front');
            const backCard = document.querySelector('.card-back');
            const frontRect = frontCard.getBoundingClientRect();

            // Build dedicated export layout to prevent clipping and keep exact ratio.
            const exportWrap = document.createElement('div');
            exportWrap.style.position = 'fixed';
            exportWrap.style.left = '-10000px';
            exportWrap.style.top = '0';
            exportWrap.style.background = '#ffffff';
            exportWrap.style.padding = '24px';
            exportWrap.style.display = 'grid';
            exportWrap.style.gridTemplateColumns = `${frontRect.width}px ${frontRect.width}px`;
            exportWrap.style.gap = '24px';
            exportWrap.style.alignItems = 'start';

            const frontClone = frontCard.cloneNode(true);
            const backClone = backCard.cloneNode(true);
            [frontClone, backClone].forEach((node) => {
                node.style.position = 'relative';
                node.style.inset = 'auto';
                node.style.transform = 'none';
                node.style.backfaceVisibility = 'visible';
                node.style.width = `${frontRect.width}px`;
                node.style.height = `${frontRect.height}px`;
                node.style.display = 'flex';
            });

            // Rebuild NIS chip in clone with simple text to avoid html2canvas missing text rendering.
            const nisChip = frontClone.querySelector('.nis-chip');
            const sourceNisValue = (frontCard.querySelector('.nis-chip .value')?.textContent || '').trim() || '-';
            if (nisChip) {
                nisChip.innerHTML = '';
                nisChip.style.display = 'inline-block';
                nisChip.style.background = '#f0f9ff';
                nisChip.style.border = '1px solid #bae6fd';
                nisChip.style.borderRadius = '20px';
                nisChip.style.padding = '5px 14px';
                nisChip.style.color = '#0f172a';
                nisChip.style.opacity = '1';
                nisChip.style.visibility = 'visible';
                nisChip.style.whiteSpace = 'nowrap';

                const nisText = document.createElement('span');
                nisText.textContent = `NIS: ${sourceNisValue}`;
                nisText.style.display = 'inline-block';
                nisText.style.fontSize = '13px';
                nisText.style.fontWeight = '700';
                nisText.style.letterSpacing = '0.2px';
                nisText.style.color = '#0f172a';
                nisText.style.fontFamily = 'ui-monospace, Menlo, Consolas, monospace';
                nisText.style.opacity = '1';
                nisText.style.visibility = 'visible';
                nisText.style.webkitTextFillColor = '#0f172a';

                nisChip.appendChild(nisText);
            }

            exportWrap.appendChild(frontClone);
            exportWrap.appendChild(backClone);
            document.body.appendChild(exportWrap);

            try {
                const imageElements = Array.from(exportWrap.querySelectorAll('img'));
                const imageReadyPromises = imageElements.map((img) => {
                    if (img.complete) {
                        return Promise.resolve();
                    }
                    return new Promise((resolve) => {
                        img.addEventListener('load', resolve, { once: true });
                        img.addEventListener('error', resolve, { once: true });
                    });
                });

                const fontsReadyPromise = document.fonts && document.fonts.ready
                    ? document.fonts.ready.catch(() => undefined)
                    : Promise.resolve();

                await Promise.all([fontsReadyPromise, ...imageReadyPromises]);
                await new Promise((resolve) => requestAnimationFrame(() => requestAnimationFrame(resolve)));

                const canvas = await html2canvas(exportWrap, {
                    scale: 3,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    logging: false,
                    scrollX: 0,
                    scrollY: 0,
                    windowWidth: exportWrap.scrollWidth,
                    windowHeight: exportWrap.scrollHeight
                });

                const link = document.createElement('a');
                link.href = canvas.toDataURL('image/png');
                link.download = `kartu-pelajar-depan-belakang-${document.querySelector('.student-name').textContent.replace(/\s+/g, '-').toLowerCase()}-${new Date().getTime()}.png`;
                link.click();

                btnDownload.innerHTML = originalHTML;
                setButtonLoading(btnDownload, false);
                btnDownload.style.background = 'rgba(16, 185, 129, 0.2)';
                btnDownload.style.borderColor = 'rgba(16, 185, 129, 0.4)';
                setTimeout(() => {
                    btnDownload.style.background = '';
                    btnDownload.style.borderColor = '';
                }, 3000);
            } catch (error) {
                console.error('Error capturing card:', error);
                btnDownload.innerHTML = originalHTML;
                setButtonLoading(btnDownload, false);
                btnDownload.style.background = 'rgba(239, 68, 68, 0.2)';
                btnDownload.style.borderColor = 'rgba(239, 68, 68, 0.4)';
                btnDownload.innerHTML = `
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Gagal Disimpan
                `;
                setTimeout(() => {
                    btnDownload.innerHTML = originalHTML;
                    btnDownload.style.background = '';
                    btnDownload.style.borderColor = '';
                }, 3000);
            } finally {
                if (document.body.contains(exportWrap)) {
                    document.body.removeChild(exportWrap);
                }
                if (wasFlipped) {
                    card.classList.add('flipped');
                }
            }
        }
    </script>
</body>
</html>
