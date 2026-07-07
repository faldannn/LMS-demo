<?php

namespace App\Services\Reports;

use App\Enums\UserRole;
use App\Models\{
    ActivityLog,
    Assignment,
    AssignmentSubmission,
    Attendance,
    AttendanceDetail,
    ClassSubject,
    DiscussionThread,
    Material,
    Quiz,
    QuizAttempt,
    ReportCardDetail,
    SelfAttendanceLog,
    Teacher,
};
use App\Support\ReportFilter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ComprehensiveReportService
{
    public function get(string $type, ReportFilter $filter): array
    {
        return match ($type) {
            'teacher-attendance' => $this->teacherAttendance($filter),
            'student-attendance' => $this->studentAttendance($filter),
            'grades' => $this->grades($filter),
            'teacher-activity' => $this->teacherActivity($filter),
            'student-activity' => $this->studentActivity($filter),
            'learning' => $this->learning($filter),
            default => throw new \InvalidArgumentException("Unknown report type: {$type}"),
        };
    }

    public function teacherAttendance(ReportFilter $filter): array
    {
        $query = Attendance::query()
            ->join('teachers', 'attendance.teacher_id', '=', 'teachers.id')
            ->join('classes', 'attendance.class_id', '=', 'classes.id')
            ->whereDate('attendance.date', '>=', $filter->dateFrom)
            ->whereDate('attendance.date', '<=', $filter->dateTo)
            ->when($filter->classId, fn ($q) => $q->where('attendance.class_id', $filter->classId))
            ->when($filter->academicYearId, fn ($q) => $q->where('classes.academic_year_id', $filter->academicYearId));

        $rows = (clone $query)
            ->select([
                'teachers.id',
                'teachers.nip',
                'teachers.name',
                DB::raw('COUNT(attendance.id) as sessions'),
                DB::raw('COUNT(DISTINCT attendance.date) as active_days'),
                DB::raw('COUNT(DISTINCT attendance.class_id) as classes_served'),
            ])
            ->groupBy('teachers.id', 'teachers.nip', 'teachers.name')
            ->orderBy('teachers.name')
            ->get()
            ->map(fn ($row) => [
                'nip' => $row->nip ?? '-',
                'name' => $row->name,
                'sessions' => (int) $row->sessions,
                'active_days' => (int) $row->active_days,
                'classes_served' => (int) $row->classes_served,
            ]);

        return [
            'title' => 'Laporan Kehadiran Guru',
            'headings' => ['NIP', 'Nama Guru', 'Sesi Presensi', 'Hari Aktif', 'Kelas Dilayani'],
            'keys' => ['nip', 'name', 'sessions', 'active_days', 'classes_served'],
            'rows' => $rows,
            'summary' => [
                'total_teachers' => $rows->count(),
                'total_sessions' => $rows->sum('sessions'),
            ],
        ];
    }

    public function studentAttendance(ReportFilter $filter): array
    {
        $query = AttendanceDetail::query()
            ->join('attendance', 'attendance_details.attendance_id', '=', 'attendance.id')
            ->join('students', 'attendance_details.student_id', '=', 'students.id')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->whereDate('attendance.date', '>=', $filter->dateFrom)
            ->whereDate('attendance.date', '<=', $filter->dateTo)
            ->when($filter->classId, fn ($q) => $q->where('students.class_id', $filter->classId))
            ->when($filter->academicYearId, fn ($q) => $q->where('classes.academic_year_id', $filter->academicYearId));

        $rows = (clone $query)
            ->select([
                'students.id',
                'students.nis',
                'students.name',
                'classes.name as class_name',
                DB::raw("SUM(CASE WHEN attendance_details.status = 'hadir' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN attendance_details.status = 'sakit' THEN 1 ELSE 0 END) as sakit"),
                DB::raw("SUM(CASE WHEN attendance_details.status = 'izin' THEN 1 ELSE 0 END) as izin"),
                DB::raw("SUM(CASE WHEN attendance_details.status = 'alfa' THEN 1 ELSE 0 END) as alfa"),
                DB::raw("SUM(CASE WHEN attendance_details.status = 'terlambat' THEN 1 ELSE 0 END) as terlambat"),
                DB::raw('COUNT(*) as total'),
            ])
            ->groupBy('students.id', 'students.nis', 'students.name', 'classes.name')
            ->orderBy('classes.name')
            ->orderBy('students.name')
            ->get()
            ->map(function ($row) {
                $effective = $row->hadir + $row->sakit + $row->izin + $row->terlambat;
                $percentage = $row->total > 0 ? round(($effective / $row->total) * 100, 1) : 0;

                return [
                    'nis' => $row->nis,
                    'name' => $row->name,
                    'class_name' => $row->class_name,
                    'hadir' => (int) $row->hadir,
                    'sakit' => (int) $row->sakit,
                    'izin' => (int) $row->izin,
                    'alfa' => (int) $row->alfa,
                    'terlambat' => (int) $row->terlambat,
                    'total' => (int) $row->total,
                    'percentage' => $percentage . '%',
                ];
            });

        return [
            'title' => 'Laporan Kehadiran Siswa',
            'headings' => ['NIS', 'Nama Siswa', 'Kelas', 'Hadir', 'Sakit', 'Izin', 'Alfa', 'Terlambat', 'Total', '% Kehadiran'],
            'keys' => ['nis', 'name', 'class_name', 'hadir', 'sakit', 'izin', 'alfa', 'terlambat', 'total', 'percentage'],
            'rows' => $rows,
            'summary' => [
                'total_students' => $rows->count(),
                'total_hadir' => $rows->sum('hadir'),
                'total_alfa' => $rows->sum('alfa'),
            ],
        ];
    }

    public function grades(ReportFilter $filter): array
    {
        $semester = $filter->mapSemesterToReportCard();

        $query = ReportCardDetail::query()
            ->join('report_cards', 'report_card_details.report_card_id', '=', 'report_cards.id')
            ->join('students', 'report_cards.student_id', '=', 'students.id')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->join('subjects', 'report_card_details.subject_id', '=', 'subjects.id')
            ->when($filter->academicYearId, fn ($q) => $q->where('report_cards.academic_year_id', $filter->academicYearId))
            ->when($semester, fn ($q) => $q->where('report_cards.semester', $semester))
            ->when($filter->classId, fn ($q) => $q->where('students.class_id', $filter->classId))
            ->when($filter->subjectId, fn ($q) => $q->where('report_card_details.subject_id', $filter->subjectId));

        $rows = (clone $query)
            ->select([
                'students.nis',
                'students.name as student_name',
                'classes.name as class_name',
                'subjects.name as subject_name',
                'report_cards.semester',
                'report_card_details.assignment_score',
                'report_card_details.quiz_score',
                'report_card_details.final_score',
                'report_card_details.grade',
            ])
            ->orderBy('classes.name')
            ->orderBy('students.name')
            ->orderBy('subjects.name')
            ->get()
            ->map(fn ($row) => [
                'nis' => $row->nis,
                'student_name' => $row->student_name,
                'class_name' => $row->class_name,
                'subject_name' => $row->subject_name,
                'semester' => ucfirst($row->semester),
                'assignment_score' => $row->assignment_score ?? '-',
                'quiz_score' => $row->quiz_score ?? '-',
                'final_score' => $row->final_score ?? '-',
                'grade' => $row->grade ?? '-',
            ]);

        return [
            'title' => 'Laporan Nilai',
            'headings' => ['NIS', 'Nama Siswa', 'Kelas', 'Mata Pelajaran', 'Semester', 'Nilai Tugas', 'Nilai Kuis', 'Nilai Akhir', 'Predikat'],
            'keys' => ['nis', 'student_name', 'class_name', 'subject_name', 'semester', 'assignment_score', 'quiz_score', 'final_score', 'grade'],
            'rows' => $rows,
            'summary' => [
                'total_records' => $rows->count(),
                'avg_final' => $rows->where('final_score', '!=', '-')->avg(fn ($r) => (float) $r['final_score']) ?: 0,
            ],
        ];
    }

    public function teacherActivity(ReportFilter $filter): array
    {
        $classSubjectIds = $this->resolveClassSubjectIds($filter);

        $teachers = Teacher::query()
            ->with('user')
            ->active()
            ->orderBy('name')
            ->get();

        $materialCounts = Material::query()
            ->when($classSubjectIds, fn ($q) => $q->whereIn('class_subject_id', $classSubjectIds))
            ->whereBetween('materials.created_at', [$filter->dateFrom . ' 00:00:00', $filter->dateTo . ' 23:59:59'])
            ->select('created_by', DB::raw('COUNT(*) as total'))
            ->groupBy('created_by')
            ->pluck('total', 'created_by');

        $assignmentCounts = Assignment::query()
            ->when($classSubjectIds, fn ($q) => $q->whereIn('assignments.class_subject_id', $classSubjectIds))
            ->whereBetween('assignments.created_at', [$filter->dateFrom . ' 00:00:00', $filter->dateTo . ' 23:59:59'])
            ->join('class_subject', 'assignments.class_subject_id', '=', 'class_subject.id')
            ->select('class_subject.teacher_id', DB::raw('COUNT(*) as total'))
            ->groupBy('class_subject.teacher_id')
            ->pluck('total', 'teacher_id');

        $quizCounts = Quiz::query()
            ->when($classSubjectIds, fn ($q) => $q->whereIn('quizzes.class_subject_id', $classSubjectIds))
            ->whereBetween('quizzes.created_at', [$filter->dateFrom . ' 00:00:00', $filter->dateTo . ' 23:59:59'])
            ->join('class_subject', 'quizzes.class_subject_id', '=', 'class_subject.id')
            ->select('class_subject.teacher_id', DB::raw('COUNT(*) as total'))
            ->groupBy('class_subject.teacher_id')
            ->pluck('total', 'teacher_id');

        $activityCounts = ActivityLog::query()
            ->join('users', 'activity_logs.user_id', '=', 'users.id')
            ->where('users.role', UserRole::Guru->value)
            ->whereBetween('activity_logs.logged_at', [$filter->dateFrom . ' 00:00:00', $filter->dateTo . ' 23:59:59'])
            ->select('users.id', DB::raw('COUNT(*) as total'))
            ->groupBy('users.id')
            ->pluck('total', 'id');

        $rows = $teachers->map(function (Teacher $teacher) use ($materialCounts, $assignmentCounts, $quizCounts, $activityCounts) {
            $userId = $teacher->user_id;

            return [
                'nip' => $teacher->nip ?? '-',
                'name' => $teacher->name,
                'materials' => (int) ($materialCounts[$userId] ?? 0),
                'assignments' => (int) ($assignmentCounts[$teacher->id] ?? 0),
                'quizzes' => (int) ($quizCounts[$teacher->id] ?? 0),
                'activities' => (int) ($activityCounts[$userId] ?? 0),
                'total' => (int) (($materialCounts[$userId] ?? 0) + ($assignmentCounts[$teacher->id] ?? 0) + ($quizCounts[$teacher->id] ?? 0) + ($activityCounts[$userId] ?? 0)),
            ];
        })->filter(fn ($row) => $row['total'] > 0)->values();

        return [
            'title' => 'Laporan Aktivitas Guru',
            'headings' => ['NIP', 'Nama Guru', 'Materi', 'Tugas', 'Kuis', 'Log Aktivitas', 'Total'],
            'keys' => ['nip', 'name', 'materials', 'assignments', 'quizzes', 'activities', 'total'],
            'rows' => $rows,
            'summary' => [
                'active_teachers' => $rows->count(),
                'total_materials' => $rows->sum('materials'),
            ],
        ];
    }

    public function studentActivity(ReportFilter $filter): array
    {
        $classSubjectIds = $this->resolveClassSubjectIds($filter);

        $submissionQuery = AssignmentSubmission::query()
            ->join('students', 'assignment_submissions.student_id', '=', 'students.id')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->join('assignments', 'assignment_submissions.assignment_id', '=', 'assignments.id')
            ->whereBetween('assignment_submissions.submitted_at', [$filter->dateFrom . ' 00:00:00', $filter->dateTo . ' 23:59:59'])
            ->when($filter->classId, fn ($q) => $q->where('students.class_id', $filter->classId))
            ->when($filter->academicYearId, fn ($q) => $q->where('classes.academic_year_id', $filter->academicYearId))
            ->when($classSubjectIds, fn ($q) => $q->whereIn('assignments.class_subject_id', $classSubjectIds));

        $submissionCounts = (clone $submissionQuery)
            ->select('students.id', DB::raw('COUNT(*) as total'))
            ->groupBy('students.id')
            ->pluck('total', 'id');

        $quizQuery = QuizAttempt::query()
            ->join('students', 'quiz_attempts.student_id', '=', 'students.id')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
            ->whereBetween('quiz_attempts.created_at', [$filter->dateFrom . ' 00:00:00', $filter->dateTo . ' 23:59:59'])
            ->when($filter->classId, fn ($q) => $q->where('students.class_id', $filter->classId))
            ->when($filter->academicYearId, fn ($q) => $q->where('classes.academic_year_id', $filter->academicYearId))
            ->when($classSubjectIds, fn ($q) => $q->whereIn('quizzes.class_subject_id', $classSubjectIds));

        $quizCounts = (clone $quizQuery)
            ->select('students.id', DB::raw('COUNT(*) as total'))
            ->groupBy('students.id')
            ->pluck('total', 'id');

        $selfCounts = SelfAttendanceLog::query()
            ->join('students', 'self_attendance_logs.student_id', '=', 'students.id')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->whereDate('self_attendance_logs.attendance_date', '>=', $filter->dateFrom)
            ->whereDate('self_attendance_logs.attendance_date', '<=', $filter->dateTo)
            ->when($filter->classId, fn ($q) => $q->where('students.class_id', $filter->classId))
            ->when($filter->academicYearId, fn ($q) => $q->where('classes.academic_year_id', $filter->academicYearId))
            ->select('students.id', DB::raw('COUNT(*) as total'))
            ->groupBy('students.id')
            ->pluck('total', 'id');

        $activityCounts = ActivityLog::query()
            ->join('users', 'activity_logs.user_id', '=', 'users.id')
            ->join('students', 'users.id', '=', 'students.user_id')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->where('users.role', UserRole::Siswa->value)
            ->whereBetween('activity_logs.logged_at', [$filter->dateFrom . ' 00:00:00', $filter->dateTo . ' 23:59:59'])
            ->when($filter->classId, fn ($q) => $q->where('students.class_id', $filter->classId))
            ->when($filter->academicYearId, fn ($q) => $q->where('classes.academic_year_id', $filter->academicYearId))
            ->select('students.id', DB::raw('COUNT(*) as total'))
            ->groupBy('students.id')
            ->pluck('total', 'id');

        $studentIds = collect()
            ->merge($submissionCounts->keys())
            ->merge($quizCounts->keys())
            ->merge($selfCounts->keys())
            ->merge($activityCounts->keys())
            ->unique();

        $students = \App\Models\Student::query()
            ->with('class')
            ->whereIn('id', $studentIds)
            ->when($filter->classId, fn ($q) => $q->where('class_id', $filter->classId))
            ->orderBy('name')
            ->get();

        $rows = $students->map(fn ($student) => [
            'nis' => $student->nis,
            'name' => $student->name,
            'class_name' => $student->class?->name ?? '-',
            'submissions' => (int) ($submissionCounts[$student->id] ?? 0),
            'quiz_attempts' => (int) ($quizCounts[$student->id] ?? 0),
            'self_attendance' => (int) ($selfCounts[$student->id] ?? 0),
            'activities' => (int) ($activityCounts[$student->id] ?? 0),
            'total' => (int) (($submissionCounts[$student->id] ?? 0) + ($quizCounts[$student->id] ?? 0) + ($selfCounts[$student->id] ?? 0) + ($activityCounts[$student->id] ?? 0)),
        ])->values();

        return [
            'title' => 'Laporan Aktivitas Siswa',
            'headings' => ['NIS', 'Nama Siswa', 'Kelas', 'Pengumpulan Tugas', 'Percobaan Kuis', 'Presensi Mandiri', 'Log Aktivitas', 'Total'],
            'keys' => ['nis', 'name', 'class_name', 'submissions', 'quiz_attempts', 'self_attendance', 'activities', 'total'],
            'rows' => $rows,
            'summary' => [
                'active_students' => $rows->count(),
                'total_submissions' => $rows->sum('submissions'),
            ],
        ];
    }

    public function learning(ReportFilter $filter): array
    {
        $query = ClassSubject::query()
            ->with(['class', 'subject', 'teacher'])
            ->when($filter->academicYearId, fn ($q) => $q->where('academic_year_id', $filter->academicYearId))
            ->when($filter->classId, fn ($q) => $q->where('class_id', $filter->classId))
            ->when($filter->subjectId, fn ($q) => $q->where('subject_id', $filter->subjectId));

        $dateFrom = $filter->dateFrom . ' 00:00:00';
        $dateTo = $filter->dateTo . ' 23:59:59';

        $rows = $query->get()->map(function (ClassSubject $cs) use ($dateFrom, $dateTo) {
            $materials = Material::where('class_subject_id', $cs->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo])->count();
            $assignments = Assignment::where('class_subject_id', $cs->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo])->count();
            $quizzes = Quiz::where('class_subject_id', $cs->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo])->count();
            $discussions = DiscussionThread::where('class_subject_id', $cs->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo])->count();

            return [
                'class_name' => $cs->class?->name ?? '-',
                'subject_name' => $cs->subject?->name ?? '-',
                'teacher_name' => $cs->teacher?->name ?? '-',
                'materials' => $materials,
                'assignments' => $assignments,
                'quizzes' => $quizzes,
                'discussions' => $discussions,
                'total' => $materials + $assignments + $quizzes + $discussions,
            ];
        })->filter(fn ($row) => $row['total'] > 0)->sortBy(['class_name', 'subject_name'])->values();

        return [
            'title' => 'Laporan Pembelajaran',
            'headings' => ['Kelas', 'Mata Pelajaran', 'Guru', 'Materi', 'Tugas', 'Kuis', 'Diskusi', 'Total Konten'],
            'keys' => ['class_name', 'subject_name', 'teacher_name', 'materials', 'assignments', 'quizzes', 'discussions', 'total'],
            'rows' => $rows,
            'summary' => [
                'class_subjects' => $rows->count(),
                'total_materials' => $rows->sum('materials'),
                'total_assignments' => $rows->sum('assignments'),
                'total_quizzes' => $rows->sum('quizzes'),
            ],
        ];
    }

    private function resolveClassSubjectIds(ReportFilter $filter): ?Collection
    {
        if (!$filter->academicYearId && !$filter->classId && !$filter->subjectId) {
            return null;
        }

        return ClassSubject::query()
            ->when($filter->academicYearId, fn ($q) => $q->where('academic_year_id', $filter->academicYearId))
            ->when($filter->classId, fn ($q) => $q->where('class_id', $filter->classId))
            ->when($filter->subjectId, fn ($q) => $q->where('subject_id', $filter->subjectId))
            ->pluck('id');
    }
}
