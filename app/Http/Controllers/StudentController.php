<?php

namespace App\Http\Controllers;

use App\Exports\MasterData\StudentsExport;
use App\Http\Controllers\Concerns\HandlesMasterDataActions;
use App\Http\Requests\StoreStudentRequest;
use App\Imports\StudentsImport;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Student;
use App\Services\MasterData\StudentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class StudentController extends Controller
{
    use HandlesMasterDataActions;

    public function __construct(private readonly StudentService $service)
    {
    }

    protected function masterDataService(): StudentService
    {
        return $this->service;
    }

    protected function routePrefix(): string
    {
        return 'students';
    }

    protected function masterResourceVariable(): string
    {
        return 'students';
    }

    protected function exportExcelClass(): string
    {
        return StudentsExport::class;
    }

    protected function exportPdfFilename(): string
    {
        return 'Data_Siswa';
    }

    protected function importClass(): ?string
    {
        return StudentsImport::class;
    }

    protected function pdfColumns(): array
    {
        return ['#', 'NIS', 'Nama', 'Kelas', 'JK', 'Ortu', 'Status'];
    }

    protected function pdfRows($items): array
    {
        return $items->values()->map(fn ($s, $i) => [
            $i + 1, $s->nis, $s->name, $s->class?->name, $s->gender, $s->parent_name, $s->status ? 'Aktif' : 'Non-Aktif',
        ])->all();
    }

    public function index(Request $request)
    {
        $students = $this->service->paginate($request->all(), 20);
        $classes = Classes::orderBy('name')->get();
        $academicYear = AcademicYear::active();

        return view('students.index', compact('students', 'classes', 'academicYear'));
    }

    public function create()
    {
        $classes = Classes::with('academicYear')->orderBy('name')->get();

        return view('students.create', compact('classes'));
    }

    public function store(StoreStudentRequest $request)
    {
        $data = $request->validated();
        $data['create_account'] = true;
        $student = $this->service->create($data);

        $msg = "Siswa {$student->name} berhasil ditambahkan.";
        if ($student->generated_password ?? null) {
            $msg .= " Username: {$student->nis}, Password: {$student->generated_password}";
        }

        return redirect()->route('students.index')->with('success', $msg);
    }

    public function show(Student $student)
    {
        $student->load([
            'class.homeroomTeacher',
            'class.major',
            'class.academicYear',
            'academicHistories' => fn ($q) => $q->with(['academicYear', 'class.homeroomTeacher', 'class.major'])->orderByDesc('academic_year_id'),
        ]);

        $recentAttendance = $student->attendanceDetails()
            ->with('attendance')
            ->join('attendance', 'attendance_details.attendance_id', '=', 'attendance.id')
            ->orderByDesc('attendance.date')
            ->orderByDesc('attendance_details.id')
            ->select('attendance_details.*')
            ->take(10)
            ->get();

        $student->setRelation('attendanceDetails', $recentAttendance);

        $monthlyStats = $student->attendanceDetails()
            ->selectRaw('status, COUNT(*) as total')
            ->whereHas('attendance', fn ($q) => $q->whereMonth('date', now()->month)->whereYear('date', now()->year))
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('students.show', compact('student', 'monthlyStats', 'recentAttendance'));
    }

    public function edit(Student $student)
    {
        $classes = Classes::with('academicYear')->orderBy('name')->get();

        return view('students.edit', compact('student', 'classes'));
    }

    public function update(StoreStudentRequest $request, Student $student)
    {
        $this->service->update($student, $request->validated());

        return redirect()->route('students.index')->with('success', "Data siswa {$student->name} berhasil diperbarui.");
    }

    public function destroy(Student $student)
    {
        $name = $student->name;
        $this->service->delete($student);

        return back()->with('success', "Siswa {$name} berhasil dihapus.");
    }

    public function resetPassword(Student $student)
    {
        $newPass = $student->dob ? $student->dob->format('Y-m-d') : (string) ($student->nisn ?: $student->nis);

        if ($student->user) {
            $student->user->update(['password' => \Illuminate\Support\Facades\Hash::make($newPass), 'password_changed_at' => null]);
            app(\App\Services\SessionManagementService::class)->revokeAllForUser($student->user->id);
            return back()->with('success', "Password akun siswa {$student->name} berhasil direset ke default ({$newPass}).");
        } else {
            $user = \App\Models\User::create([
                'username' => $student->nisn ?: $student->nis,
                'password' => \Illuminate\Support\Facades\Hash::make($newPass),
                'name' => $student->name,
                'email' => $student->parent_email ?? null,
                'role' => \App\Enums\UserRole::Siswa,
                'status' => true,
                'password_changed_at' => null,
            ]);
            $student->update(['user_id' => $user->id, 'status' => true]);
            return back()->with('success', "Akun siswa berhasil dibuat dan password direset ke default ({$newPass}).");
        }
    }

    public function attendanceHistory(Student $student, Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);

        $history = $student->attendanceDetails()
            ->with('attendance')
            ->join('attendance', 'attendance_details.attendance_id', '=', 'attendance.id')
            ->whereMonth('attendance.date', $month)
            ->whereYear('attendance.date', $year)
            ->orderByDesc('attendance.date')
            ->orderByDesc('attendance_details.id')
            ->select('attendance_details.*')
            ->get();

        return view('students.attendance-history', compact('student', 'history', 'month', 'year'));
    }

    public function printCard(Student $student)
    {
        $student->load('class');
        $payload = json_encode(['id' => $student->id, 'nis' => $student->nis]);
        $qrString = Crypt::encryptString($payload);
        $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->margin(1)->style('round')->generate($qrString);

        return view('students.print-card', compact('student', 'qrSvg'));
    }

    public function myCard()
    {
        $student = auth()->user()->student;
        if (!$student) {
            abort(404, 'Data siswa tidak ditemukan.');
        }
        $student->load('class');

        $payload = json_encode(['id' => $student->id, 'nis' => $student->nis]);
        $qrString = Crypt::encryptString($payload);
        $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->margin(1)->style('round')->generate($qrString);

        return view('students.print-card', compact('student', 'qrSvg'));
    }

    public function importTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Format_Import_Siswa.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $columns = ['nama', 'nisn', 'kelas', 'tanggal_lahir', 'jenis_kelamin_opsional', 'nama_ortu', 'no_hp_ortu'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            // Add a sample row to guide users
            fputcsv($file, [
                'Ahmad Yusuf', // nama
                '0012345678', // nisn
                'X-RPL-1', // kelas (must exist in classes table)
                '2008-04-12', // tanggal_lahir (YYYY-MM-DD)
                'L', // jenis_kelamin_opsional (L/P, boleh kosong)
                'Bambang Yusuf', // nama_ortu
                '081234567890' // no_hp_ortu
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function mutateOut(Request $request, Student $student)
    {
        $data = $request->validate([
            'student_status' => 'required|in:mutasi_keluar,drop_out',
            'exit_date' => 'required|date',
            'exit_reason' => 'required|string|max:255',
            'destination_school' => 'nullable|string|max:150',
        ]);

        $this->service->recordMutation($student, $data);

        $actionLabel = $data['student_status'] === 'mutasi_keluar' ? 'Pindah Sekolah' : 'Keluar / Mengundurkan Diri';
        return back()->with('success', "Status mutasi siswa {$student->name} ({$actionLabel}) berhasil dicatat.");
    }

    public function cancelMutation(Student $student)
    {
        $this->service->cancelMutation($student);

        return back()->with('success', "Status siswa {$student->name} berhasil dikembalikan menjadi Aktif.");
    }
}
