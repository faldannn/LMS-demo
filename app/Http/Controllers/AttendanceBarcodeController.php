<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\QrAttendanceService;
use Illuminate\Http\Request;

class AttendanceBarcodeController extends Controller
{
    public function __construct(
        private readonly QrAttendanceService $qrService
    ) {
    }

    /**
     * Halaman Utama Pusat Generator Barcode & Kartu Presensi
     */
    public function index(Request $request)
    {
        $this->authorizeStaff();

        $classes = Classes::orderBy('name')->get();
        $targetType = $request->input('target', 'student'); // 'student' or 'teacher'
        $selectedClassId = $request->input('class_id', $classes->first()?->id);
        $search = trim($request->input('q', ''));
        $barcodeType = $request->input('type', 'both'); // 'code128', 'qrcode', 'both'

        $students = collect();
        $teachers = collect();

        if ($targetType === 'teacher') {
            $teachersQuery = Teacher::with('user')->orderBy('name');
            if ($search) {
                $teachersQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%");
                });
            }
            $teachers = $teachersQuery->get();
        } else {
            $studentsQuery = Student::with(['class'])->where('status', true);
            if ($selectedClassId) {
                $studentsQuery->where('class_id', $selectedClassId);
            }
            if ($search) {
                $studentsQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%")
                      ->orWhere('nisn', 'like', "%{$search}%");
                });
            }
            $students = $studentsQuery->orderBy('name')->get();
        }

        $schoolName = Setting::get('school_name', config('school.name', 'Sekolah Digital'));
        $academicYear = Setting::get('school_academic_year', config('school.academic_year', date('Y') . '/' . (date('Y') + 1)));

        return view('attendance.barcode-center', compact(
            'classes',
            'targetType',
            'selectedClassId',
            'search',
            'barcodeType',
            'students',
            'teachers',
            'schoolName',
            'academicYear'
        ));
    }

    /**
     * Halaman Cetak Lembar Stiker Barcode (Barcode Sheet Grid A4)
     */
    public function printSheet(Request $request)
    {
        $this->authorizeStaff();

        $targetType = $request->input('target', 'student');
        $classId = $request->input('class_id');
        $barcodeFormat = $request->input('format', 'both'); // 'barcode', 'qr', 'both'

        if ($targetType === 'teacher') {
            $items = Teacher::orderBy('name')->get()->map(fn ($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'code' => 'GURU-' . ($t->nip ?: str_pad($t->id, 5, '0', STR_PAD_LEFT)),
                'subtext' => 'NIP: ' . ($t->nip ?: '-'),
                'role' => 'Guru / Pendidik',
            ]);
        } else {
            $query = Student::with('class')->where('status', true);
            if ($classId) {
                $query->where('class_id', $classId);
            }
            $items = $query->orderBy('name')->get()->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'code' => 'LMS-' . $s->nis,
                'raw_nis' => $s->nis,
                'subtext' => 'NIS: ' . $s->nis . ' | ' . ($s->class?->name ?? 'Kelas'),
                'role' => $s->class?->name ?? 'Siswa',
            ]);
        }

        $schoolName = Setting::get('school_name', config('school.name', 'Sekolah Digital'));

        return view('attendance.print-barcode-sheet', compact('items', 'targetType', 'barcodeFormat', 'schoolName'));
    }

    /**
     * Halaman Cetak Kartu Pelajar Digital Resmi (ID Card Badge)
     */
    public function printCards(Request $request)
    {
        $this->authorizeStaff();

        $targetType = $request->input('target', 'student');
        $classId = $request->input('class_id');
        $studentId = $request->input('student_id');

        $students = collect();
        if ($studentId) {
            $students = Student::with(['class.homeroomTeacher'])->where('id', $studentId)->get();
        } elseif ($classId) {
            $students = Student::with(['class.homeroomTeacher'])->where('class_id', $classId)->where('status', true)->orderBy('name')->get();
        } else {
            $students = Student::with(['class.homeroomTeacher'])->where('status', true)->orderBy('name')->take(40)->get();
        }

        $schoolName = Setting::get('school_name', config('school.name', 'Sekolah Digital'));
        $schoolAddress = Setting::get('school_address', config('school.address', ''));
        $academicYear = Setting::get('school_academic_year', config('school.academic_year', date('Y') . '/' . (date('Y') + 1)));

        return view('attendance.print-id-cards', compact(
            'students',
            'schoolName',
            'schoolAddress',
            'academicYear'
        ));
    }

    private function authorizeStaff(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isSuperAdmin() || $user->isKepalaSekolah() || $user->isGuru()), 403, 'Akses terbatas untuk Guru dan Admin.');
    }
}
