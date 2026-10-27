@props(['title', 'subtitle' => null])

<div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <p class="text-sm font-bold tracking-wide text-indigo-600 flex items-center gap-1.5">
            <span>{{ $greeting ?? 'Selamat datang' }}, {{ $user->name }}</span>
            @if(isset($homeroom_class) && $homeroom_class) 
                <span class="ml-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-indigo-100/80 text-indigo-800 border border-indigo-200/60 shadow-sm">
                    Wali Kelas {{ $homeroom_class->name }}
                </span>
            @endif 
            <span>👋</span>
        </p>
        <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900 md:text-3xl">{{ $title }}</h1>
        @if($subtitle)
            <p class="mt-1.5 max-w-2xl text-xs sm:text-sm font-medium text-slate-500 leading-relaxed">{{ $subtitle }}</p>
        @endif
    </div>
    <div class="flex flex-wrap items-center gap-2.5">
        <span class="glass-pill inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-700">
            <i class="bi bi-calendar3 text-indigo-600"></i>{{ $today_label }}
        </span>
        <span class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-md shadow-blue-500/25">
            <i class="bi bi-clock"></i><span id="dash-live-clock">--:--:--</span>
        </span>
    </div>
</div>

@once
@push('scripts')
<script>
    function tickDashboardClock() {
        const el = document.getElementById('dash-live-clock');
        if (el) el.textContent = new Date().toLocaleTimeString('id-ID');
    }
    tickDashboardClock();
    setInterval(tickDashboardClock, 1000);
</script>
@endpush
@endonce
