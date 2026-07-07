<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ config('school.system_name') }} — Masuk ke sistem pembelajaran digital">
    <link rel="icon" type="image/png" href="{{ config('school.brand.favicon', '/images/logo.png') }}">
    <title>Login – {{ config('school.system_short', 'LMS Assyafiiyah') }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="flex min-h-screen items-center justify-center bg-gradient-to-br from-brand-800 via-brand-700 to-brand-600 p-5 font-sans antialiased selection:bg-brand-500 selection:text-white relative overflow-hidden">
    
    <!-- Background Ornaments -->
    <div class="absolute -left-20 -top-20 h-64 w-64 rounded-full bg-brand-500/20 blur-3xl"></div>
    <div class="absolute -bottom-20 -right-20 h-64 w-64 rounded-full bg-brand-400/20 blur-3xl"></div>

    <div class="relative z-10 w-full max-w-md rounded-[24px] border border-white/10 bg-white/5 p-8 backdrop-blur-xl shadow-2xl shadow-brand-900/50 sm:p-10">
        <div class="mb-8 text-center">
                <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center overflow-hidden rounded-full bg-white/10 shadow-lg shadow-brand-500/20 ring-1 ring-white/20 p-1">
                    <img src="{{ config('school.brand.logo', '/images/logo.png') }}" alt="Logo Sekolah" class="h-full w-full rounded-full object-contain p-1 bg-white">
                </div>
            <h1 class="text-2xl font-extrabold leading-tight text-white">{{ config('school.system_name') }}</h1>
            <p class="mt-2 text-sm text-white/60">Sistem Pembelajaran Digital</p>
        </div>

        <div class="mb-6 rounded-xl border border-white/10 bg-white/5 p-4 text-center">
            <div class="text-sm font-semibold text-white/90">{{ config('school.name') }}</div>
            <div class="mt-1 text-xs text-white/50">{{ config('school.address') }}</div>
        </div>

        @if($errors->any())
            <div class="mb-5 flex items-center gap-2 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <p>{{ $errors->first() }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf

            <div>
                <label class="mb-1.5 block text-xs font-semibold text-white/80" for="username">Username / NIP / NIS</label>
                <div class="relative">
                    <i class="bi bi-person absolute left-4 top-1/2 -translate-y-1/2 text-white/40"></i>
                    <input type="text" id="username" name="username" placeholder="Masukkan NIP atau NIS..." value="{{ old('username') }}" required autofocus
                        class="w-full rounded-xl border border-white/10 bg-white/5 py-2.5 pl-10 pr-4 text-sm text-white placeholder-white/30 outline-none transition-colors focus:border-brand-500 focus:bg-white/10">
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold text-white/80" for="password">Password</label>
                <div class="relative">
                    <i class="bi bi-lock absolute left-4 top-1/2 -translate-y-1/2 text-white/40"></i>
                    <input type="password" id="password" name="password" placeholder="Masukkan password..." required
                        class="w-full rounded-xl border border-white/10 bg-white/5 py-2.5 pl-10 pr-4 text-sm text-white placeholder-white/30 outline-none transition-colors focus:border-brand-500 focus:bg-white/10">
                </div>
            </div>

            <div class="flex items-center gap-2 pb-2 pt-1">
                <input type="checkbox" id="remember" name="remember" class="h-4 w-4 accent-brand-500 cursor-pointer rounded border-white/10 bg-white/5">
                <label for="remember" class="cursor-pointer text-xs font-medium text-white/60 hover:text-white/80">Ingat Saya</label>
            </div>

            <button type="submit" class="group relative flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl bg-brand-500 py-3 text-sm font-bold text-white shadow-lg shadow-brand-500/30 transition-all hover:bg-brand-400 hover:shadow-brand-500/50 active:scale-[0.98]">
                <div class="absolute inset-0 flex h-full w-full justify-center [transform:skew(-12deg)_translateX(-100%)] group-hover:duration-1000 group-hover:[transform:skew(-12deg)_translateX(100%)]">
                    <div class="relative h-full w-8 bg-white/20"></div>
                </div>
                <i class="bi bi-box-arrow-in-right relative z-10 text-lg leading-none"></i>
                <span class="relative z-10">Masuk ke Sistem</span>
            </button>
        </form>

        @if(config('app.debug'))
        <div class="my-6 border-t border-white/10"></div>
        <div class="mb-3 text-center text-xs font-bold text-white/40">DEMO CEPAT (Dev Only)</div>
        <div class="grid grid-cols-2 gap-2 mb-4">
            <button type="button" class="rounded-lg border border-white/10 bg-white/5 py-2 text-xs font-semibold text-white/60 transition-colors hover:border-brand-500 hover:bg-brand-500/20 hover:text-white" onclick="fillLogin('198001012005011003','kepsek123')">🏫 Kepala Sekolah</button>
            <button type="button" class="rounded-lg border border-white/10 bg-white/5 py-2 text-xs font-semibold text-white/60 transition-colors hover:border-brand-500 hover:bg-brand-500/20 hover:text-white" onclick="fillLogin('199001012020121001','guru123')">📚 Guru</button>
            <button type="button" class="col-span-2 rounded-lg border border-white/10 bg-white/5 py-2 text-xs font-semibold text-white/60 transition-colors hover:border-brand-500 hover:bg-brand-500/20 hover:text-white" onclick="fillLogin('2025001','2025001')">🎒 Siswa</button>
        </div>
        <div class="text-center text-[11px] text-white/30">Demo: Klik tombol di atas untuk login cepat</div>
        @endif

        <div class="mt-8 text-center text-[10px] text-white/30">
            &copy; {{ date('Y') }} {{ config('school.name') }} &middot; {{ config('school.developer', 'NexDigi System') }}
        </div>
    </div>

    <script>
        function fillLogin(username, password) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
            document.querySelector('button[type="submit"]').click();
        }
    </script>
</body>

</html>
