<form method="GET" action="{{ $action ?? request()->url() }}" class="row g-3 align-items-end" id="report-filter-form">
    <div class="col-md-6 col-lg-4">
        <label class="form-label small fw-semibold text-muted mb-1">Tahun Ajaran</label>
        <select name="academic_year_id" class="form-select form-select-sm">
            <option value="">Semua</option>
            @foreach($filterOptions['academicYears'] as $year)
                <option value="{{ $year->id }}" @selected($filter->academicYearId == $year->id)>
                    {{ $year->year }}{{ $year->is_active ? ' (Aktif)' : '' }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 col-lg-4">
        <label class="form-label small fw-semibold text-muted mb-1">Semester</label>
        <select name="semester_id" class="form-select form-select-sm">
            <option value="">Semua</option>
            @foreach($filterOptions['semesters'] as $semester)
                <option value="{{ $semester->id }}" @selected($filter->semesterId == $semester->id)>
                    {{ $semester->full_label }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 col-lg-4">
        <label class="form-label small fw-semibold text-muted mb-1">Kelas</label>
        <select name="class_id" class="form-select form-select-sm">
            <option value="">Semua</option>
            @foreach($filterOptions['classes'] as $class)
                <option value="{{ $class->id }}" @selected($filter->classId == $class->id)>
                    {{ $class->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 col-lg-4">
        <label class="form-label small fw-semibold text-muted mb-1">Mata Pelajaran</label>
        <select name="subject_id" class="form-select form-select-sm">
            <option value="">Semua</option>
            @foreach($filterOptions['subjects'] as $subject)
                <option value="{{ $subject->id }}" @selected($filter->subjectId == $subject->id)>
                    {{ $subject->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 col-lg-2">
        <label class="form-label small fw-semibold text-muted mb-1">Dari Tanggal</label>
        <input type="date" name="date_from" class="form-control form-control-sm"
            value="{{ $filter->dateFrom }}">
    </div>
    <div class="col-md-3 col-lg-2">
        <label class="form-label small fw-semibold text-muted mb-1">Sampai Tanggal</label>
        <input type="date" name="date_to" class="form-control form-control-sm"
            value="{{ $filter->dateTo }}">
    </div>
    <div class="col-md-6 col-lg-4 d-flex gap-2 flex-wrap">
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="bi bi-funnel me-1"></i>Terapkan Filter
        </button>
        @isset($type)
            <a href="{{ route('reports.type.pdf', array_merge(['type' => $type], $filter->toQueryArray())) }}"
                class="btn btn-outline-danger btn-sm">
                <i class="bi bi-file-earmark-pdf me-1"></i>PDF
            </a>
            <a href="{{ route('reports.type.excel', array_merge(['type' => $type], $filter->toQueryArray())) }}"
                class="btn btn-outline-success btn-sm">
                <i class="bi bi-file-earmark-excel me-1"></i>Excel
            </a>
        @endisset
    </div>
</form>
