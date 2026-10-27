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
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="bg-gradient-to-br from-brand-800 via-brand-700 to-brand-600 font-sans antialiased selection:bg-brand-500 selection:text-white">
    <div class="relative flex min-h-screen w-full items-center justify-center overflow-hidden px-4 py-8 sm:px-6 lg:px-8">
        <!-- Background Ornaments -->
        <div class="absolute -left-40 -top-40 h-72 w-72 rounded-full bg-brand-500/20 blur-[120px]"></div>
        <div class="absolute -right-40 -bottom-40 h-72 w-72 rounded-full bg-brand-600/20 blur-[120px]"></div>

        <div class="relative z-10 w-full max-w-[420px] rounded-2xl border border-white/10 bg-white/10 backdrop-blur-2xl shadow-2xl shadow-brand-800/40 p-6 sm:p-8" style="max-width: 420px;">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-5 flex h-16 w-16 sm:h-20 sm:w-20 items-center justify-center overflow-hidden rounded-full bg-white/10 ring-1 ring-white/20 shadow-lg shadow-brand-500/20 p-1">
                <img src="{{ config('school.brand.logo', '/images/logo-sekolah.png') }}" alt="Logo Sekolah" class="h-full w-full rounded-full object-cover">
            </div>
            <h1 class="text-lg sm:text-xl md:text-2xl font-extrabold text-white text-center leading-snug">{{ config('school.name') }}</h1>
            <p class="mt-2 text-xs sm:text-sm text-white/70">Sistem Pembelajaran Digital</p>
        </div>

        @if($errors->any())
            <div class="mb-5 flex items-center gap-2 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 h-12 text-sm text-rose-200">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <p>{{ $errors->first() }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf

            <div>
                <label class="mb-1.5 block text-xs font-semibold text-white/80" for="username">Username / NIP / NISN</label>
                <div class="relative">
                    <i class="bi bi-person absolute top-1/2 -translate-y-1/2 text-white/40" style="left: 1rem;"></i>
                    
                    <input type="text" id="username" name="username" placeholder="Masukkan NIP atau NISN..." value="{{ old('username') }}" required autofocus
                        class="w-full rounded-xl border border-white/10 bg-white/5 h-12 pr-4 text-sm text-white placeholder-white/30 outline-none transition-colors focus:border-brand-500 focus:bg-white/10"
                        style="padding-left: 2.75rem;">
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold text-white/80" for="password">Password</label>
                <div class="relative">
                    <i class="bi bi-lock absolute top-1/2 -translate-y-1/2 text-white/40" style="left: 1rem;"></i>
                    
                    <input type="password" id="password" name="password" placeholder="Masukkan password..." required
                        class="w-full rounded-xl border border-white/10 bg-white/5 h-12 text-sm text-white placeholder-white/30 outline-none transition-colors focus:border-brand-500 focus:bg-white/10"
                        style="padding-left: 2.75rem; padding-right: 3rem;">
                    
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-white/40 hover:text-white focus:outline-none" onclick="togglePasswordVisibility('password', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-2 pb-2 pt-1">
                <input type="checkbox" id="remember" name="remember" class="h-4 w-4 accent-brand-500 cursor-pointer rounded border-white/10 bg-white/5">
                <label for="remember" class="cursor-pointer text-xs font-medium text-white/60 hover:text-white/80">Ingat Saya</label>
            </div>

            <button type="submit" class="group relative flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl bg-brand-500 h-12 text-sm font-bold text-white shadow-lg shadow-brand-500/30 transition-all hover:bg-brand-600 hover:shadow-brand-500/50 active:scale-[0.98]">
                <div class="absolute inset-0 flex h-full w-full justify-center [transform:skew(-12deg)_translateX(-100%)] group-hover:duration-1000 group-hover:[transform:skew(-12deg)_translateX(100%)]">
                    <div class="relative h-full w-8 bg-white/20"></div>
                </div>
                <i class="bi bi-box-arrow-in-right relative z-10 text-lg leading-none"></i>
                <span class="relative z-10">Masuk Sekarang</span>
            </button>
        </form>

        <div class="mt-2 text-center text-[10px] text-white/30">
            &copy; {{ date('Y') }} {{ config('school.name') }} &middot; {{ config('school.developer', 'NexDigi System') }}
        </div>
    </div>

    <script>
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
    </script>
</body>

</html>
