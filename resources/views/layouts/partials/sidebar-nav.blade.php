{{-- Sidebar navigation — 3 role utama --}}
<div class="py-2">
    <div class="px-5 py-2 text-[10px] font-bold uppercase tracking-wider text-white/30">Utama</div>
    <a href="{{ route('dashboard') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('dashboard', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-speedometer2 @active('dashboard', 'text-brand-500')"></i> Dashboard
    </a>
</div>

@role('siswa')
<div class="py-2">
    <div class="px-5 py-2 text-[10px] font-bold uppercase tracking-wider text-white/30">Portal Siswa</div>
    <a href="{{ route('elearning.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('elearning.*', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-laptop @active('elearning.*', 'text-brand-500')"></i> Portal E-Learning
    </a>
    <a href="{{ route('attendance.mine') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('attendance.mine', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white') @active('attendance.self-checkin', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-calendar-check @active('attendance.mine', 'text-brand-500') @active('attendance.self-checkin', 'text-brand-500')"></i> Absensi
    </a>
    <a href="{{ route('schedules.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('schedules.*', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-calendar3 @active('schedules.*', 'text-brand-500')"></i> Jadwal Pelajaran
    </a>
    <a href="{{ route('materials.mine') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('materials.mine', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-journal-text @active('materials.mine', 'text-brand-500')"></i> Materi Pelajaran
    </a>
    <a href="{{ route('student.assignments.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('student.assignments.*', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-journal-check @active('student.assignments.*', 'text-brand-500')"></i> Tugas Saya
    </a>
    <a href="{{ route('sso.cbt') }}" target="_blank" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors text-white/60 hover:bg-white/5 hover:text-white">
        <i class="bi bi-pencil-square text-white/60 group-hover:text-white"></i> Ujian CBT
    </a>
</div>
@endrole

@role('guru')
<div class="py-2">
    <div class="px-5 py-2 text-[10px] font-bold uppercase tracking-wider text-white/30">Akademik</div>
    <a href="{{ route('elearning.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('elearning.*', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white') @active('materials.*', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white') @active('assignments.*', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-laptop @active('elearning.*', 'text-brand-500')"></i> Portal E-Learning
    </a>
    <a href="{{ route('attendance.create') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('attendance.create', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-check2-square @active('attendance.create', 'text-brand-500')"></i> Input Absensi
    </a>
    <a href="{{ route('attendance.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('attendance.index', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-clipboard-check @active('attendance.index', 'text-brand-500')"></i> Daftar Absensi
    </a>
    <a href="{{ route('schedules.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('schedules.*', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-calendar3 @active('schedules.*', 'text-brand-500')"></i> Jadwal Pelajaran
    </a>
    <a href="{{ route('sso.cbt') }}" target="_blank" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors text-white/60 hover:bg-white/5 hover:text-white">
        <i class="bi bi-pencil-square text-white/60 group-hover:text-white"></i> Ujian CBT
    </a>
</div>
<div class="py-2">
    <div class="px-5 py-2 text-[10px] font-bold uppercase tracking-wider text-white/30">Log Presensi</div>
    <a href="{{ route('attendance.self-logs') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('attendance.self-logs', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-geo-alt @active('attendance.self-logs', 'text-brand-500')"></i> Log Presensi GPS
    </a>
</div>
@endrole

@role('kepala_sekolah')
<div class="py-2">
    <div class="px-5 py-2 text-[10px] font-bold uppercase tracking-wider text-white/30">Monitoring</div>
    <a href="{{ route('attendance.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('attendance.index', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-clipboard-check @active('attendance.index', 'text-brand-500')"></i> Presensi
    </a>
    <a href="{{ route('elearning.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('elearning.*', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-laptop @active('elearning.*', 'text-brand-500')"></i> E-Learning
    </a>
    <a href="{{ route('sso.cbt') }}" target="_blank" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors text-white/60 hover:bg-white/5 hover:text-white">
        <i class="bi bi-pencil-square text-white/60 group-hover:text-white"></i> Ujian CBT
    </a>
</div>
<div class="py-2">
    <div class="px-5 py-2 text-[10px] font-bold uppercase tracking-wider text-white/30">Data Master</div>
    <a href="{{ route('subjects.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('subjects.*', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-book @active('subjects.*', 'text-brand-400')"></i> Mata Pelajaran
    </a>
    <a href="{{ route('majors.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('majors.*', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-mortarboard @active('majors.*', 'text-brand-400')"></i> Jurusan
    </a>
    <a href="{{ route('teachers.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('teachers.*', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-person-badge @active('teachers.*', 'text-brand-400')"></i> Data Guru
    </a>
    <a href="{{ route('classes.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('classes.*', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-building @active('classes.*', 'text-brand-400')"></i> Data Kelas
    </a>
    <a href="{{ route('schedules.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('schedules.*', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-calendar3 @active('schedules.*', 'text-brand-400')"></i> Jadwal Pelajaran
    </a>
    <a href="{{ route('students.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('students.*', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-people @active('students.*', 'text-brand-400')"></i> Data Siswa
    </a>
    <a href="{{ route('academic-years.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('academic-years.*', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-calendar-event @active('academic-years.*', 'text-brand-400')"></i> Tahun Ajaran
    </a>
</div>
<div class="py-2">
    <div class="px-5 py-2 text-[10px] font-bold uppercase tracking-wider text-white/30">Sistem</div>
    <a href="{{ route('users.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('users.*', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-people-fill @active('users.*', 'text-brand-400')"></i> Manajemen User
    </a>
    <a href="{{ route('security.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('security.*', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-shield-lock @active('security.*', 'text-brand-400')"></i> Keamanan
    </a>
    <a href="{{ route('settings.index') }}" class="group mx-3 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors @active('settings.*', 'bg-brand-500/20 text-white', 'text-white/60 hover:bg-white/5 hover:text-white')">
        <i class="bi bi-gear @active('settings.*', 'text-brand-400')"></i> Pengaturan
    </a>
</div>
@endrole

