{{-- Sidebar navigation — 3 role utama --}}
<div class="py-2">
    <div class="px-5 py-2 text-[10px] font-bold uppercase tracking-wider text-white/30">Utama</div>
    <a href="{{ route('dashboard') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('dashboard', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-speedometer2 @active('dashboard', 'text-emerald-400')"></i> Dashboard
    </a>
</div>

@role('siswa')
<div class="py-2">
    <div class="px-5 py-2 text-[10px] font-bold uppercase tracking-wider text-white/30">Portal Siswa</div>
    <a href="{{ route('elearning.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('elearning.*', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-laptop @active('elearning.*', 'text-emerald-400')"></i> Portal E-Learning
    </a>
    <a href="{{ route('attendance.mine') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('attendance.mine', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white') @active('attendance.self-checkin', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-calendar-check @active('attendance.mine', 'text-emerald-400') @active('attendance.self-checkin', 'text-emerald-400')"></i> Absensi
    </a>
    <a href="{{ route('schedules.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('schedules.*', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-calendar3 @active('schedules.*', 'text-emerald-400')"></i> Jadwal Pelajaran
    </a>
    <a href="{{ route('materials.mine') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('materials.mine', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-journal-text @active('materials.mine', 'text-emerald-400')"></i> Materi Pelajaran
    </a>
    <a href="{{ route('student.assignments.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('student.assignments.*', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-journal-check @active('student.assignments.*', 'text-emerald-400')"></i> Tugas Saya
    </a>
    <a href="{{ route('sso.cbt') }}" target="_blank" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors text-white/60 hover:bg-white/5 hover:text-white">
        <i class="bi bi-pencil-square text-white/60 group-hover:text-white"></i> Ujian CBT
    </a>
</div>
@endrole

@role('guru')
<div class="py-2">
    <div class="px-5 py-2 text-[10px] font-bold uppercase tracking-wider text-white/30">Akademik</div>
    <a href="{{ route('elearning.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('elearning.*', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white') @active('materials.*', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white') @active('assignments.*', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-laptop @active('elearning.*', 'text-emerald-400')"></i> Portal E-Learning
    </a>
    <a href="{{ route('attendance.teacher-checkin') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('attendance.teacher-checkin', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-person-bounding-box @active('attendance.teacher-checkin', 'text-emerald-400')"></i> Presensi Saya (Guru)
    </a>
    @if(\App\Models\Setting::isFeatureEnabled('subject_attendance'))
    <a href="{{ route('attendance.create') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('attendance.create', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-check2-square @active('attendance.create', 'text-emerald-400')"></i> Input / Presensi Mapel
    </a>
    @endif
    @php
        $teacherModel = auth()->user()->teacher;
        $homeroomClasses = $teacherModel ? $teacherModel->homeroomClasses()->get() : collect();
        $isAbsensiKelasActive = request()->routeIs('attendance.index', 'attendance.show', 'attendance.homeroom-create');
    @endphp
    @if($homeroomClasses->isNotEmpty())
        @foreach($homeroomClasses as $hClass)
        <a href="{{ route('attendance.index', ['class_id' => $hClass->id]) }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ $isAbsensiKelasActive ? 'bg-white/15 text-white font-bold shadow-sm' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
            <i class="bi bi-clipboard-check {{ $isAbsensiKelasActive ? 'text-emerald-400 font-bold' : '' }}"></i> Absensi Kelas {{ $hClass->name }}
        </a>
        @endforeach
    @endif
    <a href="{{ route('schedules.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('schedules.*', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-calendar3 @active('schedules.*', 'text-emerald-400')"></i> Jadwal Pelajaran
    </a>
    <a href="{{ route('sso.cbt') }}" target="_blank" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors text-white/60 hover:bg-white/5 hover:text-white">
        <i class="bi bi-pencil-square text-white/60 group-hover:text-white"></i> Ujian CBT
    </a>
</div>
<div class="py-2">
    <div class="px-5 py-2 text-[10px] font-bold uppercase tracking-wider text-white/30">Log Presensi</div>
    <a href="{{ route('attendance.self-logs') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('attendance.self-logs', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-geo-alt @active('attendance.self-logs', 'text-emerald-400')"></i> Log Presensi GPS
    </a>
</div>
@endrole

@role('kepala_sekolah')
<div class="py-2">
    <div class="px-5 py-2 text-[10px] font-bold uppercase tracking-wider text-white/30">Monitoring & Presensi</div>
    <a href="{{ route('attendance.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('attendance.index', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white') @active('attendance.show', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-clipboard-check @active('attendance.index', 'text-emerald-400') @active('attendance.show', 'text-emerald-400')"></i> Rekap Presensi(Guru dan Kelas)
    </a>
    <a href="{{ route('attendance.self-logs') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('attendance.self-logs', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-person-bounding-box @active('attendance.self-logs', 'text-emerald-400')"></i> Log Selfie & GPS Siswa
    </a>
    <a href="{{ route('attendance.tap-simulator') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('attendance.tap-simulator', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-credit-card-2-front @active('attendance.tap-simulator', 'text-emerald-400')"></i> Simulator Tap Kartu RFID
    </a>
    @if(\App\Models\Setting::isFeatureEnabled('subject_attendance'))
    <a href="{{ route('attendance.create') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('attendance.create', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-check2-square @active('attendance.create', 'text-emerald-400')"></i> Detail Presensi Mapel
    </a>
    @endif
    <a href="{{ route('elearning.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('elearning.*', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-laptop @active('elearning.*', 'text-emerald-400')"></i> Portal E-Learning
    </a>
    <a href="{{ route('sso.cbt') }}" target="_blank" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors text-white/60 hover:bg-white/5 hover:text-white">
        <i class="bi bi-pencil-square text-white/60 group-hover:text-white"></i> Ujian CBT
    </a>
</div>
<div class="py-2">
    <div class="px-5 py-2 text-[10px] font-bold uppercase tracking-wider text-white/30">Data Master</div>
    <a href="{{ route('subjects.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('subjects.*', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-book @active('subjects.*', 'text-emerald-400')"></i> Mata Pelajaran
    </a>
    <a href="{{ route('majors.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('majors.*', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-mortarboard @active('majors.*', 'text-emerald-400')"></i> Jurusan
    </a>
    <a href="{{ route('teachers.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('teachers.*', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-person-badge @active('teachers.*', 'text-emerald-400')"></i> Data Guru
    </a>
    <a href="{{ route('classes.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('classes.*', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-building @active('classes.*', 'text-emerald-400')"></i> Data Kelas
    </a>
    <a href="{{ route('schedules.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('schedules.*', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-calendar3 @active('schedules.*', 'text-emerald-400')"></i> Jadwal Pelajaran
    </a>
    <a href="{{ route('students.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('students.*', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-people @active('students.*', 'text-emerald-400')"></i> Data Siswa
    </a>
    <a href="{{ route('academic-years.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('academic-years.*', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-calendar-event @active('academic-years.*', 'text-emerald-400')"></i> Tahun Ajaran
    </a>
</div>
<div class="py-2">
    <div class="px-5 py-2 text-[10px] font-bold uppercase tracking-wider text-white/30">Sistem</div>
    <a href="{{ route('users.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('users.*', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-people-fill @active('users.*', 'text-emerald-400')"></i> Manajemen User
    </a>
    <a href="{{ route('security.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('security.*', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-shield-lock @active('security.*', 'text-emerald-400')"></i> Keamanan
    </a>
    <a href="{{ route('settings.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('settings.*', 'bg-white/15 text-white font-bold shadow-sm', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-gear @active('settings.*', 'text-emerald-400')"></i> Pengaturan
    </a>
</div>
@endrole
