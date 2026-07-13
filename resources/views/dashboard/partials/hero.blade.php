@props(['title', 'subtitle' => null])

<div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <p class="text-sm font-medium text-brand-600">
            {{ $greeting ?? 'Selamat datang' }}, {{ $user->name }}@if(isset($homeroom_class) && $homeroom_class) <span class="ml-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-brand-100 text-brand-800 border border-brand-200">Wali Kelas {{ $homeroom_class->name }}</span>@endif 👋
        </p>
        <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 md:text-3xl">{{ $title }}</h1>
        @if($subtitle)
            <p class="mt-2 max-w-2xl text-sm text-slate-600">{{ $subtitle }}</p>
        @endif
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <span class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm ring-1 ring-slate-200/80">
            <i class="bi bi-calendar3 text-brand-600"></i>{{ $today_label }}
        </span>
        <span class="inline-flex items-center gap-2 rounded-full bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm">
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
