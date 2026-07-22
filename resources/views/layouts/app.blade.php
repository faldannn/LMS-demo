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
    <link rel="icon" type="image/png" href="{{ config('school.brand.favicon', '/images/logo.png') }}">
    
    <!-- Dark mode removed -->

    @stack('styles')
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-brand-500 selection:text-white transition-colors duration-300">

    {{-- SIDEBAR OVERLAY --}}
    <div id="sidebarOverlay" class="fixed inset-0 z-40 hidden bg-slate-900/60 backdrop-blur-sm lg:hidden" onclick="toggleSidebar()"></div>

    {{-- SIDEBAR --}}
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col overflow-y-auto text-white shadow-2xl transition-transform duration-300 lg:translate-x-0" style="background: linear-gradient(180deg, var(--color-brand-800), var(--color-brand-700));">
        
        <div class="flex items-center gap-3 border-b border-white/10 px-5 py-4">
            <img src="{{ config('school.brand.logo', '/images/logo.png') }}" alt="Logo" class="h-10 w-10 shrink-0 rounded-full object-cover p-1 bg-white ring-1 ring-white/20">
            <div>
                <div class="text-sm font-bold text-white">{{ config('school.system_name') }}</div>
            </div>
        </div>

        @include('layouts.partials.sidebar-nav')

        <div class="mt-auto border-t border-white/10 p-4">
            <div class="mb-3 flex items-center gap-3 rounded-xl bg-white/5 p-3">
                <img src="{{ auth()->user()->avatar_url }}" class="h-9 w-9 shrink-0 rounded-lg border-2 border-white/10 object-cover" alt="Avatar">
                <div class="min-w-0 flex-1">
                    <div class="truncate text-xs font-bold text-white">{{ auth()->user()->name }}</div>
                    <div class="text-[10px] text-white/40">{{ auth()->user()->getRoleLabel() }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg border border-white/20 bg-transparent py-2 text-xs font-semibold text-white transition-colors hover:bg-white/10 hover:text-white">
                    <i class="bi bi-box-arrow-left"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex min-h-screen flex-col lg:pl-64">
        
        {{-- TOPBAR --}}
        <header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-4 border-b border-slate-200 bg-white/80 px-4 backdrop-blur-md sm:px-6 lg:px-8">
            <button class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition-colors hover:border-brand-500 hover:bg-brand-50 hover:text-brand-600 lg:hidden" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            
            <div class="flex-1 text-lg font-extrabold text-slate-900">@yield('page-title', 'Dashboard')</div>
            
            <div class="hidden rounded-lg bg-brand-50 px-3 py-1.5 font-mono text-xs font-bold text-brand-600 sm:block">
                {{ now()->translatedFormat('D, d M Y') }}
            </div>
            
            <a href="{{ route('profile') }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition-colors hover:border-brand-500 hover:bg-brand-50 hover:text-brand-600" title="Profil">
                <i class="bi bi-person"></i>
            </a>
            
            @role('kepala_sekolah')
                <a href="{{ route('settings.index') }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition-colors hover:border-brand-500 hover:bg-brand-50 hover:text-brand-600" title="Pengaturan">
                    <i class="bi bi-gear"></i>
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
            <footer class="mt-auto border-t border-slate-200 bg-white py-4 text-center text-xs text-slate-500">
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
        });

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