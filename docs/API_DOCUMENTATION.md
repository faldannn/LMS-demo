# API Documentation

## Overview

LMS SMA Islam Assyafiiyah 02 Bekasi menggunakan traditional web application architecture dengan Blade templating. Dokumen ini mendokumentasikan struktur routing, controllers, dan services yang tersedia.

## Authentication Endpoints

### Web Authentication
| Method | Endpoint | Description | Access |
|--------|----------|-------------|---------|
| GET | `/login` | Login form | Public |
| POST | `/login` | Process login | Public |
| POST | `/logout` | Logout user | Authenticated |

### Authentication Flow
1. User accesses `/login` → Login form
2. POST credentials → `AuthController@login`
3. Successful login → Redirect to role-based dashboard
4. Failed login → Rate limiting (10 attempts/minute)

## Role-Based Routing Structure

### 1. **Authenticated Routes** (All Roles)
```php
Route::middleware(['auth', 'check.active'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // Profile management
    Route::get('profile', [UserController::class, 'profile']);
    Route::put('profile', [UserController::class, 'updateProfile']);
    Route::put('profile/password', [UserController::class, 'updatePassword']);
});
```

### 2. **Admin Routes** (Kepala Sekolah)
```php
Route::middleware('can:manage-admin')->prefix('admin')->group(function () {
    // Master Data Management
    Route::resource('students', StudentController::class);
    Route::resource('teachers', TeacherController::class);
    Route::resource('classes', ClassController::class);
    Route::resource('majors', MajorController::class);
    Route::resource('subjects', SubjectController::class);
    Route::resource('users', UserController::class);
    
    // System Settings
    Route::get('settings', [SettingController::class, 'index']);
    Route::post('settings/update-school', [SettingController::class, 'updateSchool']);
    
    // Security Management
    Route::prefix('security')->name('security.')->group(function () {
        Route::get('audit-logs', [AuditLogController::class, 'index']);
        Route::get('login-history', [LoginHistoryController::class, 'index']);
        Route::get('sessions', [SessionManagementController::class, 'index']);
        Route::get('maintenance', [SystemMaintenanceController::class, 'index']);
    });
});
```

### 3. **Academic Routes** (Guru & Admin)
```php
Route::middleware('can:access-elearning')->prefix('akademik')->group(function () {
    // E-Learning Portal
    Route::get('elearning', [DashboardController::class, 'elearning']);
    
    // Materials Management
    Route::get('materials/{classSubject}', [MaterialController::class, 'index']);
    Route::get('materials/{classSubject}/create', [MaterialController::class, 'create']);
    Route::post('materials/{classSubject}', [MaterialController::class, 'store']);
    
    // Assignments Management
    Route::get('assignments/{classSubject}', [AssignmentController::class, 'index']);
    Route::get('assignments/{classSubject}/create', [AssignmentController::class, 'create']);
    Route::post('assignments/{classSubject}', [AssignmentController::class, 'store']);
    
    // Staff-only operations
    Route::middleware('can:access-staff')->group(function () {
        Route::get('attendance/create', [AttendanceController::class, 'create']);
        Route::post('attendance', [AttendanceController::class, 'store']);
        Route::get('schedules/create', [ScheduleController::class, 'create']);
        Route::post('schedules', [ScheduleController::class, 'store']);
    });
});
```

### 4. **Student Routes**
```php
Route::middleware('can:access-siswa')->prefix('siswa')->group(function () {
    // Self attendance
    Route::get('attendance/self-checkin', [SelfAttendanceController::class, 'index']);
    
    // Student materials view
    Route::get('materials/mine', [MaterialController::class, 'studentMaterials']);
    
    // Student assignments
    Route::prefix('assignments')->name('student.assignments.')->group(function () {
        Route::get('/', [StudentAssignmentController::class, 'index']);
        Route::get('{assignment}', [StudentAssignmentController::class, 'show']);
        Route::post('{assignment}/submit', [StudentAssignmentController::class, 'store']);
    });
});
```

## Core Controllers

### 1. **DashboardController**
```php
class DashboardController extends Controller
{
    public function index()          // Role-based dashboard
    public function elearning()      // E-learning portal
    public function chartData()      // Attendance chart data
    public function gradeChartData() // Grade analytics
    public function lmsActivityChartData() // LMS activity trends
}
```

### 2. **MaterialController**
```php
class MaterialController extends Controller
{
    public function index(ClassSubject $classSubject)    // List materials
    public function create(ClassSubject $classSubject)   // Create form
    public function store(StoreMaterialRequest $request) // Store material
    public function show(Material $material)             // View material
    public function download(Material $material)         // Download file
    public function studentMaterials()                   // Student view
}
```

### 3. **AttendanceController**
```php
class AttendanceController extends Controller
{
    public function index()                    // Attendance list
    public function create()                   // Create attendance form
    public function store(StoreAttendanceRequest $request) // Store attendance
    public function show(Attendance $attendance) // View attendance details
    public function edit(Attendance $attendance) // Edit form
    public function update(UpdateAttendanceRequest $request) // Update
}
```

### 4. **AssignmentController**
```php
class AssignmentController extends Controller
{
    public function index(ClassSubject $classSubject)    // List assignments
    public function create(ClassSubject $classSubject)   // Create form
    public function store(StoreAssignmentRequest $request) // Store assignment
    public function show(Assignment $assignment)         // View assignment
    public function gradeSubmissions(Assignment $assignment) // Grade submissions
    public function storeGrade(StoreGradeRequest $request) // Store grade
}
```

## Service Layer Architecture

### Core Services

#### 1. **DashboardService**
```php
class DashboardService
{
    public function buildFor(User $user): array
    public function viewFor(User $user): string
    public function getLmsActivityTrend(int $days = 7): array
    
    private function buildKepalaSekolahDashboard(): array
    private function buildGuruDashboard(User $user): array  
    private function buildSiswaDashboard(User $user): array
}
```

#### 2. **AttendanceService**
```php
class AttendanceService
{
    public function getTodaySummary(): array
    public function getMonthlyTrend(int $year = null): array
    public function getClassAttendanceSummary(string|object $date): Collection
    public function getLowAttendanceStudents(int $threshold = null, int $limit = 10): Collection
    public function storeAttendance(array $data, ?Teacher $teacher): Attendance
    public function updateAttendance(Attendance $attendance, array $data): Attendance
}
```

#### 3. **ReportCardService**
```php
class ReportCardService
{
    public function generateForClass(Classes $class, AcademicYear $academicYear, string $semester): array
    public function getClassGradeAverages(string $semester = 'ganjil'): array
    
    private function calculateAssignmentScore(Student $student, ClassSubject $cs): float
    private function calculateQuizScore(Student $student, ClassSubject $cs): float
    private function determineGrade(float $score): string
}
```

#### 4. **FileStorageService**
```php
class FileStorageService
{
    public function upload(UploadedFile $file, string $directory, ?string $filename = null): string
    public function download(string $path, ?string $filename = null): Response
    public function delete(string $path): bool
    public function assertSafeUpload(UploadedFile $file): void
    public function extractYoutubeId(string $url): ?string
}
```

## Data Models & Relationships

### User Model Relationships
```php
class User extends Authenticatable
{
    public function teacher(): HasOne
    public function student(): HasOne
    public function studentParent(): HasOne
    public function loginLogs(): HasMany
    public function activityLogs(): HasMany
    
    // Role check methods
    public function isKepalaSekolah(): bool
    public function isGuru(): bool
    public function isSiswa(): bool
    public function isStaff(): bool
    public function hasRole(string|array $roles): bool
}
```

### Academic Models
```php
// ClassSubject - Many-to-Many between Class and Subject
class ClassSubject extends Model
{
    public function class(): BelongsTo
    public function subject(): BelongsTo
    public function teacher(): BelongsTo
    public function materials(): HasMany
    public function assignments(): HasMany
    public function quizzes(): HasMany
    public function discussionThreads(): HasMany
}
```

## Form Request Validation

### Example: StoreMaterialRequest
```php
class StoreMaterialRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'type' => 'required|in:pdf,ppt,video,youtube,image,link',
            'file' => 'required_if:type,pdf,ppt,video,image|file|max:51200',
            'url' => 'required_if:type,youtube,link|url|max:500',
            'topic' => 'nullable|string|max:100',
            'is_published' => 'boolean',
        ];
    }
}
```

### Example: StoreAttendanceRequest
```php
class StoreAttendanceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'class_id' => 'required|exists:classes,id',
            'session' => 'required|in:pagi,siang,sore',
            'details' => 'required|array',
            'details.*.status' => 'required|in:hadir,terlambat,sakit,izin,alfa',
            'details.*.check_in' => 'nullable|date_format:H:i',
            'details.*.notes' => 'nullable|string|max:255',
        ];
    }
}
```

## Policy Authorization

### Material Policy Example
```php
class MaterialPolicy
{
    public function view(User $user, Material $material): bool
    {
        if ($user->isStaff()) {
            return $this->canManageClassSubject($user, $material->classSubject)
                || $user->isKepalaSekolah();
        }
        
        return $user->isSiswa()
            && $material->is_published
            && (int) $material->classSubject->class_id === (int) $user->student?->class_id;
    }
    
    public function update(User $user, Material $material): bool
    {
        $material->loadMissing('classSubject');
        return $this->canManageClassSubject($user, $material->classSubject);
    }
}
```

## Middleware Stack

### Application Middleware
```php
// Kernel.php
protected $middleware = [
    \App\Http\Middleware\TrustProxies::class,
    \App\Http\Middleware\SecurityHeaders::class,
    \Illuminate\Http\Middleware\HandleCors::class,
    \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
    \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
    \App\Http\Middleware\TrimStrings::class,
    \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
];

protected $routeMiddleware = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'check.active' => \App\Http\Middleware\CheckActiveUser::class,
    'role' => \App\Http\Middleware\RoleMiddleware::class,
    'can' => \Illuminate\Auth\Middleware\Authorize::class,
];
```

## Queue Jobs

### Notification Jobs
```php
class SendAbsenceNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public function __construct(
        public int $studentId,
        public string $date
    ) {}
    
    public function handle(): void
    {
        // Send WhatsApp notification to parents
        // Using Fonnte API integration
    }
}
```

## Scheduled Tasks

### Kernel Schedule
```php
// Console/Kernel.php
protected function schedule(Schedule $schedule): void
{
    // Daily backup at 2 AM
    $schedule->command('db:backup')->dailyAt('02:00');
    
    // Weekly log pruning
    $schedule->command('logs:prune')->weekly();
    
    // Queue maintenance
    $schedule->command('queue:prune-failed')->weekly();
    
    // Cache optimization
    $schedule->command('cache:prune-stale-tags')->hourly();
}
```

## Error Handling

### Custom Error Pages
- `resources/views/errors/403.blade.php` - Forbidden
- `resources/views/errors/404.blade.php` - Not Found  
- `resources/views/errors/500.blade.php` - Server Error
- `resources/views/errors/503.blade.php` - Maintenance

### Exception Handling
```php
// Handler.php
public function register(): void
{
    $this->renderable(function (AuthenticationException $e) {
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    });
    
    $this->renderable(function (AuthorizationException $e) {
        return response()->view('errors.403', [], 403);
    });
}
```

## Security Configuration

### Rate Limiting
```php
// config/security.php
return [
    'rate_limits' => [
        'login' => '10,1',           // 10 attempts per minute
        'settings' => '10,1',        // Settings updates
        'reports_export' => '15,1',  // Report exports
        'api' => '60,1',             // Future API rate limiting
    ],
    
    'upload' => [
        'max_size' => 51200, // 50MB in kilobytes
        'allowed_mimes' => [
            'pdf' => 'application/pdf',
            'ppt' => ['application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'],
            'video' => ['video/mp4', 'video/webm', 'video/ogg'],
            'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        ],
    ],
];
```

---

## API Future Development

### Planned REST API Endpoints
```yaml
# Authentication
POST /api/auth/login
POST /api/auth/logout
GET  /api/auth/user

# Academic Data
GET  /api/classes
GET  /api/subjects
GET  /api/materials
GET  /api/assignments

# Student Operations  
GET  /api/student/attendance
GET  /api/student/grades
POST /api/student/assignments/{id}/submit

# Teacher Operations
POST /api/teacher/attendance
POST /api/teacher/assignments
POST /api/teacher/grades
```

### API Authentication Strategy
- JWT Token based authentication
- Role-based access control
- Rate limiting per endpoint
- API versioning (v1, v2, etc.)

---

*API Documentation Version: 1.0.0 | Last Updated: July 2026*