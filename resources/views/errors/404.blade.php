<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan — {{ config('school.system_short', 'Sekolah Digital') }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1rem;
        }
        .error-container {
            text-align: center;
            max-width: 28rem;
            width: 100%;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 800;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            line-height: 1;
        }
        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        .error-message {
            color: #64748b;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .error-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            color: #94a3b8;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.3);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-compass"></i>
        </div>
        <h1 class="error-code">404</h1>
        <h2 class="error-title">Halaman Tidak Ditemukan</h2>
        <p class="error-message">
            Halaman yang Anda cari mungkin telah dipindahkan, dihapus, atau tidak pernah ada.
            Periksa URL atau kembali ke dashboard.
        </p>
        <a href="{{ url('/') }}" class="btn-back">
            <i class="bi bi-house-door"></i>
            Kembali ke Dashboard
        </a>
        <div class="mt-8 text-sm text-slate-400">
            {{ config('school.system_name') }} &copy; {{ date('Y') }}
        </div>
    </div>
</body>
</html>
