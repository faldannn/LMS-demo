{{-- resources/views/schedules/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Jadwal Pelajaran')
@section('page-title', 'Jadwal Pelajaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <p class="text-muted mb-0">Jadwal pelajaran per kelas</p>
    @role('admin,guru')
        <a href="{{ route('schedules.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Jadwal
        </a>
    @endrole
</div>

{{-- Pilih Kelas --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted mb-1">Pilih Kelas</label>
                <select name="class_id" class="form-select" onchange="this.form.submit()">
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

@if($class)
    <div class="card mb-3" style="background:linear-gradient(135deg,var(--color-brand-600),var(--color-brand-500));border:none">
        <div class="card-body p-3 d-flex align-items-center gap-3">
            <div style="width:44px;height:44px;border-radius:10px;background:rgba(255,255,255,.2);display:grid;place-items:center;font-size:20px;font-weight:800;color:#fff">
                {{ $class->grade }}
            </div>
            <div>
                <div class="text-white fw-bold" style="font-size:16px">{{ $class->name }}</div>
                <div style="color:rgba(255,255,255,.7);font-size:12px">Wali Kelas: {{ $class->homeroomTeacher?->name ?? '-' }}</div>
            </div>
        </div>
    </div>

    @php $dayNames = ['','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']; @endphp

    @forelse($schedules as $dayNum => $daySchedules)
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                    <span style="font-weight:700;color:var(--color-brand-600)"><i class="bi bi-calendar-day me-2"></i>{{ $dayNames[$dayNum] }}</span>
                <span class="badge bg-primary">{{ $daySchedules->count() }} mata pelajaran</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Jam</th><th>Mata Pelajaran</th><th>Guru</th><th>Ruang</th><th>Durasi</th>@role('admin,guru')<th>Aksi</th>@endrole</tr>
                    </thead>
                    <tbody>
                        @foreach($daySchedules as $sched)
                            <tr>
                                <td style="font-family:'JetBrains Mono',monospace;font-size:13px;white-space:nowrap">
                                    {{ substr($sched->start_time,0,5) }} – {{ substr($sched->end_time,0,5) }}
                                </td>
                                <td><span class="fw-semibold">{{ $sched->subject }}</span></td>
                                <td style="font-size:13px">{{ $sched->teacher?->name ?? '-' }}</td>
                                <td style="font-size:13px">{{ $sched->room ?? '-' }}</td>
                                <td>
                                    @php
                                        $start = \Carbon\Carbon::parse($sched->start_time);
                                        $end = \Carbon\Carbon::parse($sched->end_time);
                                        $dur = $start->diffInMinutes($end);
                                    @endphp
                                    <span class="badge" style="background:var(--color-brand-50);color:var(--color-brand-600)">{{ $dur }} menit</span>
                                </td>
                                @role('admin,guru')
                                    <td>
                                        <form id="del-s{{ $sched->id }}" method="POST" action="{{ route('schedules.destroy', $sched) }}">@csrf @method('DELETE')</form>
                                        <button class="btn btn-sm btn-outline-danger"
                                            data-confirm="Hapus jadwal {{ $sched->subject }}?"
                                            data-form="del-s{{ $sched->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                @endrole
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                Belum ada jadwal untuk kelas ini.
                @role('admin,guru')
                    <a href="{{ route('schedules.create', ['class_id' => $classId]) }}" class="btn btn-sm btn-primary mt-2">+ Tambah Jadwal</a>
                @endrole
            </div>
        </div>
    @endforelse
@endif
@endsection
