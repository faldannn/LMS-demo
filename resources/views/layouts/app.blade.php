<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') &mdash; {{ config('school.name', config('app.name')) }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="{{ config('school.brand.favicon', '/images/logo-sekolah.png') }}">
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.72);
            --glass-border: rgba(255, 255, 255, 0.85);
            --glass-shadow: 0 16px 36px rgba(100, 110, 160, 0.08);
            --sidebar-grad: linear-gradient(180deg, #5b28a8 0%, #3e1b7e 45%, #1d0f48 100%);
            --active-pill: linear-gradient(90deg, #448ef6 0%, #3074ea 100%);
        }

        body {
            background-color: #dbeafe;
            background-image: 
                radial-gradient(at 10% 12%, rgba(56, 189, 248, 0.45) 0px, transparent 45%),
                radial-gradient(at 90% 88%, rgba(168, 85, 247, 0.38) 0px, transparent 50%),
                radial-gradient(at 50% 8%, rgba(244, 114, 182, 0.22) 0px, transparent 40%),
                radial-gradient(at 85% 18%, rgba(99, 102, 241, 0.3) 0px, transparent 45%),
                linear-gradient(135deg, #dbeafe 0%, #ede9fe 48%, #e0e7ff 100%);
            background-attachment: fixed;
            min-height: 100vh;
            color: #1e1b4b;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Glassmorphism primitives */
        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1.5px solid rgba(255, 255, 255, 0.88);
            border-radius: 24px;
            box-shadow: 0 14px 34px rgba(100, 110, 160, 0.08);
            transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .glass-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 42px rgba(100, 110, 160, 0.12);
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1.5px solid rgba(255, 255, 255, 0.88);
            border-radius: 28px;
            box-shadow: 0 16px 40px rgba(99, 102, 241, 0.08);
        }

        .glass-pill {
            background: rgba(255, 255, 255, 0.68);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1.2px solid rgba(255, 255, 255, 0.9);
            border-radius: 9999px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        }

        /* Sidebar Glassmorphism */
        .sidebar-futuristic {
            background: linear-gradient(180deg, #5b28a8 0%, #3e1b7e 45%, #1d0f48 100%) !important;
            border-right: 1.5px solid rgba(255, 255, 255, 0.12);
            box-shadow: 14px 0 40px rgba(28, 11, 63, 0.3) !important;
        }

        .sidebar-active-pill {
            background: linear-gradient(90deg, #448ef6 0%, #3074ea 100%) !important;
            box-shadow: 0 4px 18px rgba(59, 130, 246, 0.5) !important;
            border-radius: 14px !important;
            color: #ffffff !important;
            font-weight: 700 !important;
        }

        /* Topbar Frosted Glass */
        .glass-topbar {
            background: rgba(255, 255, 255, 0.65) !important;
            backdrop-filter: blur(20px) !important;
            -webkit-backdrop-filter: blur(20px) !important;
            border-bottom: 1.5px solid rgba(255, 255, 255, 0.85) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02) !important;
        }
    </style>
    @stack('styles')
</head>

<body class="font-sans antialiased selection:bg-indigo-500 selection:text-white transition-colors duration-300">

    {{-- SIDEBAR OVERLAY --}}
    <div id="sidebarOverlay" class="fixed inset-0 z-40 hidden bg-slate-900/60 backdrop-blur-sm lg:hidden" onclick="toggleSidebar()"></div>

    {{-- SIDEBAR --}}
    <aside id="sidebar" class="sidebar-futuristic fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col overflow-y-auto text-white shadow-2xl transition-transform duration-300 lg:translate-x-0">
        
        <div class="flex items-center gap-3 border-b border-white/10 px-5 py-4">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white/15 p-1 backdrop-blur-md border border-white/30 shadow-md">
                <img src="{{ config('school.brand.logo', '/images/logo-sekolah.png') }}" alt="Logo" class="h-full w-full rounded-full object-cover">
            </div>
            <div>
                <div class="text-sm font-extrabold tracking-tight text-white">{{ config('school.system_name') }}</div>
                <div class="text-[10px] font-semibold text-white/50 tracking-wider uppercase">Portal Digital</div>
            </div>
        </div>

        @include('layouts.partials.sidebar-nav')

        <div class="mt-auto border-t border-white/10 p-4">
            <div class="mb-3 flex items-center gap-3 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 p-3">
                <img src="{{ auth()->user()->avatar_url }}" class="h-9 w-9 shrink-0 rounded-xl border border-white/20 object-cover" alt="Avatar">
                <div class="min-w-0 flex-1">
                    <div class="truncate text-xs font-bold text-white">{{ auth()->user()->name }}</div>
                    <div class="text-[10px] text-white/60 font-medium">{{ auth()->user()->getRoleLabel() }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl border border-white/20 bg-white/5 py-2.5 text-xs font-bold text-white transition-all hover:bg-white/15 hover:shadow-md">
                    <i class="bi bi-box-arrow-left"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex min-h-screen flex-col lg:pl-64">
        
        {{-- TOPBAR --}}
        <header class="glass-topbar sticky top-0 z-30 flex h-16 shrink-0 items-center gap-4 px-4 sm:px-6 lg:px-8">
            <button class="flex h-9 w-9 items-center justify-center rounded-xl border border-white/80 bg-white/80 text-slate-700 shadow-sm transition-all hover:scale-105 lg:hidden" onclick="toggleSidebar()">
                <i class="bi bi-list text-lg"></i>
            </button>
            
            <div class="flex-1 text-lg font-black tracking-tight text-slate-900">@yield('page-title', 'Dashboard')</div>
            
            <div class="hidden items-center gap-2 rounded-full border border-white/90 bg-white/70 px-4 py-1.5 font-mono text-xs font-bold text-indigo-700 shadow-sm backdrop-blur-md sm:flex">
                <i class="bi bi-calendar-event text-indigo-500"></i>
                {{ now()->translatedFormat('D, d M Y') }}
            </div>
            
            <a href="{{ route('profile') }}" class="flex h-9 w-9 items-center justify-center rounded-xl border border-white/90 bg-white/70 text-slate-700 shadow-sm backdrop-blur-md transition-all hover:scale-105 hover:bg-white" title="Profil">
                <i class="bi bi-person text-base"></i>
            </a>
            
            @role('kepala_sekolah')
                <a href="{{ route('settings.index') }}" class="flex h-9 w-9 items-center justify-center rounded-xl border border-white/90 bg-white/70 text-slate-700 shadow-sm backdrop-blur-md transition-all hover:scale-105 hover:bg-white" title="Pengaturan">
                    <i class="bi bi-gear text-base"></i>
                </a>
            @endrole
        </header>

        {{-- FLASH MESSAGES VIA TOAST --}}
        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    showToast('success', '{{ session("success") }}');
                });
            </script>
        @endif
        
        @if(session('error') || $errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    showToast('error', '{{ session("error") ?? $errors->first() }}');
                });
            </script>
        @endif

        {{-- PAGE CONTENT --}}
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>

        {{-- FOOTER --}}
        <footer class="mt-auto border-t border-white/40 bg-white/40 backdrop-blur-md py-4 text-center text-xs font-semibold text-slate-500">
            {{ config('school.system_name') }} &copy; {{ date('Y') }} &middot; {{ config('school.developer', 'NexDigi System') }}
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Dark mode features removed from UI and JS

        // 1. data-confirm handler
        document.querySelectorAll('[data-confirm]').forEach(el => {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                const form = document.getElementById(this.dataset.form);
                const rawMsg = this.dataset.confirm || 'Apakah Anda yakin?';
                const htmlMsg = rawMsg.replace(/\\n/g, '<br>').replace(/\n/g, '<br>').replace(/(<br>\s*){2,}/g, '<br>');
                Swal.fire({
                    title: 'Konfirmasi',
                    html: htmlMsg,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Lanjutkan!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'rounded-lg',
                        cancelButton: 'rounded-lg'
                    }
                }).then(r => { if (r.isConfirmed && form) form.submit(); });
            });
        });

        // 2. Intercept legacy inline confirm() for form submissions
        document.addEventListener('submit', function (e) {
            const form = e.target;
            const onsubmitAttr = form.getAttribute('onsubmit');
            if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                e.preventDefault();
                e.stopPropagation();
                
                let message = 'Apakah Anda yakin?';
                const match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
                if (match && match[1]) {
                    message = match[1];
                }
                
                form.removeAttribute('onsubmit');
                const htmlMsg = message.replace(/\\n/g, '<br>').replace(/\n/g, '<br>').replace(/(<br>\s*){2,}/g, '<br>');
                
                Swal.fire({
                    title: 'Konfirmasi',
                    html: htmlMsg,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Lanjutkan!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'rounded-lg',
                        cancelButton: 'rounded-lg'
                    }
                }).then(r => {
                    if (r.isConfirmed) {
                        form.submit();
                    } else {
                        form.setAttribute('onsubmit', onsubmitAttr);
                    }
                });
            }
        }, true);

        // 3. Intercept legacy inline confirm() for button clicks
        document.addEventListener('click', function (e) {
            const target = e.target.closest('[onclick]');
            if (target) {
                const onclickAttr = target.getAttribute('onclick');
                if (onclickAttr && onclickAttr.includes('confirm(')) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    let message = 'Apakah Anda yakin?';
                    const match = onclickAttr.match(/confirm\(['"](.*?)['"]\)/);
                    if (match && match[1]) {
                        message = match[1];
                    }
                    
                    const htmlMsg = message.replace(/\\n/g, '<br>').replace(/\n/g, '<br>').replace(/(<br>\s*){2,}/g, '<br>');

                    Swal.fire({
                        title: 'Konfirmasi',
                        html: htmlMsg,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, Lanjutkan!',
                        cancelButtonText: 'Batal',
                        customClass: {
                            confirmButton: 'rounded-lg',
                            cancelButton: 'rounded-lg'
                        }
                    }).then(r => {
                        if (r.isConfirmed) {
                            target.removeAttribute('onclick');
                            target.click();
                            target.setAttribute('onclick', onclickAttr);
                        }
                    });
                }
            }
        }, true); // Use capture phase to intercept before native inline handler runs

        // 4. SweetAlert2 Toast configuration
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        window.showToast = (icon, title) => {
            Toast.fire({ icon: icon, title: title });
        };

        window.togglePasswordVisibility = (inputId, button) => {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        };
    </script>

    @stack('scripts')
</body>
</html>