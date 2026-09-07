<?php

namespace App\Http\Controllers;

use App\Exports\MasterData\TeachersExport;
use App\Http\Controllers\Concerns\HandlesMasterDataActions;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Imports\TeachersImport;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\ClassSubject;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use App\Services\MasterData\TeacherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Services\ActivityLogService;

class TeacherController extends Controller
{
    use HandlesMasterDataActions;

    public function __construct(
        private readonly TeacherService $service,
        private readonly ActivityLogService $activityLogService
    ) {
    }

    protected function masterDataService(): TeacherService
    {
        return $this->service;
    }

    protected function routePrefix(): string
    {
        return 'teachers';
    }

    protected function masterResourceVariable(): string
    {
        return 'teachers';
    }

    protected function exportExcelClass(): string
    {
        return TeachersExport::class;
    }

    protected function exportPdfFilename(): string
    {
        return 'Data_Guru';
    }

    protected function importClass(): ?string
    {
        return TeachersImport::class;
    }

    protected function pdfColumns(): array
    {
        return ['#', 'NIP', 'Nama', 'Mapel', 'Telepon', 'Email', 'Status'];
    }

    protected function pdfRows($items): array
    {
        return $items->values()->map(fn ($t, $i) => [
            $i + 1, $t->nip, $t->name, $t->subject, $t->phone, $t->email, $t->status ? 'Aktif' : 'Non-Aktif',
        ])->all();
    }

    public function index(Request $request)
    {
        $teachers = $this->service->paginate($request->all());

        return view('teachers.index', compact('teachers'));
    }

    public function create()
    {
        $subjects = Subject::active()->orderBy('name')->get();
        return view('teachers.create', compact('subjects'));
    }

    public function store(StoreTeacherRequest $request)
    {
        $teacher = $this->service->create($request->validated());
        $msg = "Guru {$teacher->name} berhasil ditambahkan.";
        if ($teacher->generated_username ?? null) {
            $msg .= " Username: {$teacher->generated_username}, Password: {$teacher->generated_password}";
        }

        return redirect()->route('teachers.index')->with('success', $msg);
    }

    public function show(Teacher $teacher)
    {
        $teacher->load([
            'user',
            'homeroomClasses', 
            'schedules.class', 
            'classSubjects.class',
            'classSubjects.subject',
            'classSubjects.academicYear',
            'subjects',
            'attendances' => fn ($q) => $q->with(['class'])->withCount('details')->latest()->limit(10)
        ]);

        $classes = Classes::with('homeroomTeacher')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('teachers.show', compact('teacher', 'classes', 'subjects'));
    }

    public function edit(Teacher $teacher)
    {
        $teacher->load('subjects');
        $subjects = Subject::active()->orderBy('name')->get();
        return view('teachers.edit', compact('teacher', 'subjects'));
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        $this->service->update($teacher, $request->validated());

        return redirect()->route('teachers.index')->with('success', "Data guru {$teacher->name} berhasil diperbarui.");
    }

    public function destroy(Teacher $teacher)
    {
        $name = $teacher->name;
        $this->service->delete($teacher);

        return redirect()->route('teachers.index')->with('success', "Guru {$name} berhasil dihapus.");
    }

    public function updateHomeroom(Request $request, Teacher $teacher)
    {
        $data = $request->validate([
            'class_id' => 'nullable|exists:classes,id',
        ]);

        $classId = $data['class_id'] ?? null;

        DB::transaction(function () use ($teacher, $classId) {
            // Remove this teacher as homeroom from ALL classes first
            Classes::where('homeroom_teacher_id', $teacher->id)
                ->update(['homeroom_teacher_id' => null]);

            // Assign to the selected single class (if any)
            if ($classId) {
                // Also unset from any previous homeroom teacher on that class
                Classes::where('id', $classId)
                    ->update(['homeroom_teacher_id' => $teacher->id]);
            }
        });

        return back()->with('success', 'Wali kelas berhasil diperbarui.');
    }

    public function storeClassSubject(Request $request, Teacher $teacher)
    {
        $data = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $teacher->loadMissing('subjects');
        if (!$teacher->subjects->contains($data['subject_id'])) {
            $teacher->subjects()->syncWithoutDetaching([$data['subject_id']]);
        }

        $activeYear = AcademicYear::active();
        if (!$activeYear) {
            return back()->withErrors(['error' => 'Tidak ada tahun ajaran aktif.']);
        }

        ClassSubject::updateOrCreate([
            'class_id' => $data['class_id'],
            'subject_id' => $data['subject_id'],
            'academic_year_id' => $activeYear->id,
        ], [
            'teacher_id' => $teacher->id,
        ]);

        // Also update matching schedules for this class and subject
        $subject = Subject::find($data['subject_id']);
        if ($subject) {
            Schedule::where('class_id', $data['class_id'])
                ->where(function ($q) use ($subject) {
                    $q->where('subject', $subject->name)
                      ->orWhere('subject', 'like', "%{$subject->name}%");
                })
                ->update(['teacher_id' => $teacher->id]);
        }

        return back()->with('success', 'Tugas mengajar berhasil ditambahkan.');
    }

    public function destroyClassSubject(Teacher $teacher, ClassSubject $classSubject)
    {
        abort_unless((int) $classSubject->teacher_id === (int) $teacher->id, 404);

        $classSubject->delete();

        return back()->with('success', 'Tugas mengajar berhasil dihapus.');
    }

    public function importTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Format_Import_Guru.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $columns = [
            'Email Address',
            'Nama Lengkap',
            'Tempat Tanggal Lahir (Tempat, Tanggal, Bulan, Tahun)',
            'Email',
            'Nomer Telepon Whatsapp',
            'Guru Mata Pelajaran',
            'Wali kelas (jika bukan walas maka tidak perlu di isi)'
        ];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            // Add a sample row to guide users
            fputcsv($file, [
                'budi.santoso@email.com', // Email Address
                'Budi Santoso, S.Pd', // Nama Lengkap
                'Bekasi, 5 Maret 1985', // Tempat Tanggal Lahir (Tempat, Tanggal, Bulan, Tahun)
                'budi.santoso@email.com', // Email
                '081234567890', // Nomer Telepon Whatsapp
                'Matematika dan Fisika', // Guru Mata Pelajaran
                'X.1' // Wali kelas (jika bukan walas maka tidak perlu di isi)
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function resetPassword(Teacher $teacher)
    {
        $user = $teacher->user;
        if (!$user) {
            return back()->withErrors(['error' => 'Akun user tidak ditemukan untuk guru ini.']);
        }

        $newPass = 'guru123';
        
        $user->update(['password' => Hash::make($newPass), 'password_changed_at' => null]);

        $this->activityLogService->log(
            action: 'teacher_reset_password',
            user: auth()->user(),
            subject: $teacher,
            description: "Password untuk guru {$teacher->name} direset ke default ({$newPass})."
        );

        return back()->with('success', "Password untuk guru {$teacher->name} berhasil direset ke default ({$newPass}).");
    }
}
