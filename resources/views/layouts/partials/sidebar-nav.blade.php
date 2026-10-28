{{-- Sidebar navigation — 3 role utama --}}
<div class="py-2">
    <div class="px-5 py-2 text-[10px] font-extrabold uppercase tracking-widest text-white/40">Utama</div>
    <a href="{{ route('dashboard') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('dashboard', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-speedometer2 text-base @active('dashboard', 'text-white', 'text-white/70')"></i> Dashboard
    </a>
</div>

@role('siswa')
<div class="py-2">
    <div class="px-5 py-2 text-[10px] font-extrabold uppercase tracking-widest text-white/40">Portal Siswa</div>
    <a href="{{ route('elearning.index') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('elearning.*', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-laptop text-base @active('elearning.*', 'text-white', 'text-white/70')"></i> Portal E-Learning
    </a>
    <a href="{{ route('attendance.self-checkin') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('attendance.self-checkin', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-person-bounding-box text-base @active('attendance.self-checkin', 'text-white', 'text-white/70')"></i> Presensi Muka &amp; Barcode
    </a>
    <a href="{{ route('attendance.mine') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('attendance.mine', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-calendar-check text-base @active('attendance.mine', 'text-white', 'text-white/70')"></i> Riwayat Absensi
    </a>
    <a href="{{ route('schedules.index') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('schedules.*', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-calendar3 text-base @active('schedules.*', 'text-white', 'text-white/70')"></i> Jadwal Pelajaran
    </a>
    <a href="{{ route('materials.mine') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('materials.mine', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-journal-text text-base @active('materials.mine', 'text-white', 'text-white/70')"></i> Materi Pelajaran
    </a>
    <a href="{{ route('student.assignments.index') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('student.assignments.*', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-journal-check text-base @active('student.assignments.*', 'text-white', 'text-white/70')"></i> Tugas Saya
    </a>
    <a href="{{ route('cbt.student.index') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('cbt.student.*', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-pencil-square text-base @active('cbt.student.*', 'text-white', 'text-white/70')"></i> Ujian CBT
    </a>
</div>
@endrole

@role('guru')
<div class="py-2">
    <div class="px-5 py-2 text-[10px] font-extrabold uppercase tracking-widest text-white/40">Akademik</div>
    <a href="{{ route('elearning.index') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('elearning.*', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white') @active('materials.*', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white') @active('assignments.*', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-laptop text-base @active('elearning.*', 'text-white', 'text-white/70')"></i> Portal E-Learning
    </a>

    <a href="{{ route('attendance.teacher-checkin') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('attendance.teacher-checkin', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-person-bounding-box text-base @active('attendance.teacher-checkin', 'text-white', 'text-white/70')"></i> Presensi Muka Guru (GPS)
    </a>
    <a href="{{ route('attendance.barcode-center') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('attendance.barcode-center', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-upc-scan text-base @active('attendance.barcode-center', 'text-white', 'text-white/70')"></i> Pusat Barcode &amp; Kartu
    </a>

    @if(\App\Models\Setting::isFeatureEnabled('subject_attendance'))
    <a href="{{ route('attendance.create') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('attendance.create', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-check2-square text-base @active('attendance.create', 'text-white', 'text-white/70')"></i> Input / Presensi Mapel
    </a>
    @endif
    <a href="{{ route('attendance.scan-qr') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('attendance.scan-qr', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-camera-fill text-base @active('attendance.scan-qr', 'text-white', 'text-white/70')"></i> Scan QR Siswa
    </a>
    @php
        $teacherModel = auth()->user()->teacher;
        $homeroomClasses = $teacherModel ? $teacherModel->homeroomClasses()->get() : collect();
        $isAbsensiKelasActive = request()->routeIs('attendance.index', 'attendance.show', 'attendance.homeroom-create');
    @endphp
    @if($homeroomClasses->isNotEmpty())
        @foreach($homeroomClasses as $hClass)
        <a href="{{ route('attendance.index', ['class_id' => $hClass->id]) }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all {{ $isAbsensiKelasActive ? 'sidebar-active-pill' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
            <i class="bi bi-clipboard-check text-base {{ $isAbsensiKelasActive ? 'text-white' : 'text-white/70' }}"></i> Absensi Kelas {{ $hClass->name }}
        </a>
        @endforeach
    @endif
    <a href="{{ route('schedules.index') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('schedules.*', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-calendar3 text-base @active('schedules.*', 'text-white', 'text-white/70')"></i> Jadwal Pelajaran
    </a>
    <a href="{{ route('cbt.index') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('cbt.*', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-pencil-square text-base @active('cbt.*', 'text-white', 'text-white/70')"></i> Ujian CBT &amp; Token
    </a>
</div>

@endrole

@role('kepala_sekolah')
<div class="py-2">
    <div class="px-5 py-2 text-[10px] font-extrabold uppercase tracking-widest text-white/40">Presensi &amp; Perangkat Pintar</div>
    <a href="{{ route('attendance.barcode-center') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('attendance.barcode-center', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-upc-scan text-base @active('attendance.barcode-center', 'text-white', 'text-white/70')"></i> Pusat Barcode &amp; Kartu
    </a>
    <a href="{{ route('attendance.scan-qr') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('attendance.scan-qr', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-camera-fill text-base @active('attendance.scan-qr', 'text-white', 'text-white/70')"></i> Scanner Presensi Multi-Alat
    </a>
    <a href="{{ route('attendance.index') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('attendance.index', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white') @active('attendance.show', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-clipboard-check text-base @active('attendance.index', 'text-white', 'text-white/70')"></i> Rekap Presensi (Guru &amp; Kelas)
    </a>
    <a href="{{ route('attendance.self-logs') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('attendance.self-logs', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-qr-code-scan text-base @active('attendance.self-logs', 'text-white', 'text-white/70')"></i> Log Presensi Mandiri
    </a>
    <a href="{{ route('attendance.teacher-checkin') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('attendance.teacher-checkin', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-person-bounding-box text-base @active('attendance.teacher-checkin', 'text-white', 'text-white/70')"></i> Presensi Mandiri Guru (GPS)
    </a>
    @if(\App\Models\Setting::isFeatureEnabled('subject_attendance'))
    <a href="{{ route('attendance.create') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('attendance.create', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-check2-square text-base @active('attendance.create', 'text-white', 'text-white/70')"></i> Detail Presensi Mapel
    </a>
    @endif
    <a href="{{ route('elearning.index') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('elearning.*', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-laptop text-base @active('elearning.*', 'text-white', 'text-white/70')"></i> Portal E-Learning
    </a>
    <a href="{{ route('cbt.index') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('cbt.*', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-pencil-square text-base @active('cbt.*', 'text-white', 'text-white/70')"></i> Ujian CBT &amp; Token
    </a>
</div>
<div class="py-2">
    <div class="px-5 py-2 text-[10px] font-extrabold uppercase tracking-widest text-white/40">Data Master</div>
    <a href="{{ route('subjects.index') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('subjects.*', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-book text-base @active('subjects.*', 'text-white', 'text-white/70')"></i> Mata Pelajaran
    </a>
    <a href="{{ route('majors.index') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('majors.*', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-mortarboard text-base @active('majors.*', 'text-white', 'text-white/70')"></i> Jurusan
    </a>
    <a href="{{ route('teachers.index') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('teachers.*', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-person-badge text-base @active('teachers.*', 'text-white', 'text-white/70')"></i> Data Guru
    </a>
    <a href="{{ route('classes.index') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('classes.*', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-building text-base @active('classes.*', 'text-white', 'text-white/70')"></i> Data Kelas
    </a>
    <a href="{{ route('schedules.index') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('schedules.*', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-calendar3 text-base @active('schedules.*', 'text-white', 'text-white/70')"></i> Jadwal Pelajaran
    </a>
    <a href="{{ route('students.index') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('students.*', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-people text-base @active('students.*', 'text-white', 'text-white/70')"></i> Data Siswa
    </a>
    <a href="{{ route('academic-years.index') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('academic-years.*', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-calendar-event text-base @active('academic-years.*', 'text-white', 'text-white/70')"></i> Tahun Ajaran
    </a>
</div>
<div class="py-2">
    <div class="px-5 py-2 text-[10px] font-extrabold uppercase tracking-widest text-white/40">Sistem</div>
    <a href="{{ route('users.index') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('users.*', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-people-fill text-base @active('users.*', 'text-white', 'text-white/70')"></i> Manajemen User
    </a>
    <a href="{{ route('security.index') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('security.*', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-shield-lock text-base @active('security.*', 'text-white', 'text-white/70')"></i> Keamanan
    </a>
    <a href="{{ route('settings.index') }}" class="group mx-3 flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-all @active('settings.*', 'sidebar-active-pill', 'text-white/75 hover:bg-white/10 hover:text-white')">
        <i class="bi bi-gear text-base @active('settings.*', 'text-white', 'text-white/70')"></i> Pengaturan
    </a>
</div>
@endrole
