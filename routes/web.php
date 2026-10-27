<?php

use App\Http\Controllers\AcademicCalendarController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\SelfAttendanceController;
use App\Http\Controllers\AttendanceBarcodeController;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\LoginHistoryController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\SessionManagementController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SystemMaintenanceController;
use App\Http\Controllers\StudentAssignmentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login.post');
});

Route::match(['GET', 'POST'], '/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::get('/ping', function () {
    return response()->json([
        'status' => 'ok',
        'php' => PHP_VERSION,
        'app_env' => app()->environment(),
        'app_key' => config('app.key') ? 'set' : 'missing',
        'storage_path' => storage_path(),
        'storage_writable' => is_writable(storage_path()),
        'views_path' => config('view.compiled'),
        'views_writable' => is_writable(config('view.compiled')),
        'db' => (function () {
            try {
                \Illuminate\Support\Facades\DB::connection()->getPdo();
                return 'connected (' . \Illuminate\Support\Facades\DB::connection()->getDatabaseName() . ')';
            } catch (\Throwable $e) {
                return 'error: ' . $e->getMessage();
            }
        })(),
    ]);
});

/*
|--------------------------------------------------------------------------
| Authenticated Application
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.active'])->group(function () {

    // SSO CBT Redirect (Fallback compatibility)
    Route::get('/sso/to-cbt', [\App\Http\Controllers\SsoController::class, 'redirectToCbt'])->name('sso.cbt');

    // CBT Internal System - Admin & Guru Management
    Route::middleware('can:access-staff')->prefix('cbt')->name('cbt.')->group(function () {
        Route::get('/', [\App\Http\Controllers\CbtController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\CbtController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\CbtController::class, 'store'])->name('store');
        Route::get('/detail/{id}', [\App\Http\Controllers\CbtController::class, 'show'])->name('show');
        Route::post('/detail/{id}/questions', [\App\Http\Controllers\CbtController::class, 'storeQuestion'])->name('questions.store');
        Route::match(['PUT', 'POST'], '/detail/{quizId}/questions/{questionId}', [\App\Http\Controllers\CbtController::class, 'updateQuestion'])->name('questions.update');
        Route::delete('/detail/{quizId}/questions/{questionId}', [\App\Http\Controllers\CbtController::class, 'destroyQuestion'])->name('questions.destroy');
        Route::post('/detail/{id}/import', [\App\Http\Controllers\CbtController::class, 'importQuestions'])->name('questions.import');
        Route::get('/template/{format?}', [\App\Http\Controllers\CbtController::class, 'downloadTemplate'])->name('template.download');
        Route::get('/detail/{id}/results', [\App\Http\Controllers\CbtController::class, 'results'])->name('results');
        Route::get('/attempts/{attemptId}', [\App\Http\Controllers\CbtController::class, 'attemptDetail'])->name('attempts.show');
        Route::delete('/detail/{id}', [\App\Http\Controllers\CbtController::class, 'destroy'])->name('destroy');
        Route::get('/token/poll', [\App\Http\Controllers\CbtController::class, 'tokenPoll'])->name('token.poll');
        Route::post('/token/refresh', [\App\Http\Controllers\CbtController::class, 'forceRefreshToken'])->name('token.refresh');
    });

    // CBT Internal System - Siswa Exam Portal & Room
    Route::middleware('can:access-siswa')->prefix('cbt/student')->name('cbt.student.')->group(function () {
        Route::get('/', [\App\Http\Controllers\CbtStudentController::class, 'index'])->name('index');
        Route::post('/start/{quizId}', [\App\Http\Controllers\CbtStudentController::class, 'start'])->name('start');
        Route::get('/exam/{attemptId}', [\App\Http\Controllers\CbtStudentController::class, 'examRoom'])->name('exam');
        Route::post('/exam/{attemptId}/save', [\App\Http\Controllers\CbtStudentController::class, 'saveAnswer'])->name('save');
        Route::post('/exam/{attemptId}/finish', [\App\Http\Controllers\CbtStudentController::class, 'finish'])->name('finish');
        Route::get('/result/{attemptId}', [\App\Http\Controllers\CbtStudentController::class, 'result'])->name('result');
    });

    // Dashboard & analytics
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/index', [DashboardController::class, 'index'])->name('dashboard.index');

    // Laporan Analitik (Kepala Sekolah)
    Route::middleware('role:kepala_sekolah')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/',              [ReportController::class, 'index'])->name('index');
        Route::get('/{type}',        [ReportController::class, 'show'])->name('show');
        Route::get('/{type}/pdf',    [ReportController::class, 'pdf'])->name('type.pdf');
        Route::get('/{type}/excel',  [ReportController::class, 'excel'])->name('type.excel');
    });

    Route::middleware('can:access-staff')->group(function () {
        Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart-data');
        Route::get('/dashboard/grade-chart-data', [DashboardController::class, 'gradeChartData'])->name('dashboard.grade-chart-data');
        Route::get('/dashboard/lms-activity-data', [DashboardController::class, 'lmsActivityChartData'])->name('dashboard.lms-activity-data');
    });

    // Profile (all roles)
    Route::get('profile', [UserController::class, 'profile'])->name('profile');
    Route::put('profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::put('profile/password', [UserController::class, 'updatePassword'])->name('profile.password');

    /*
    |----------------------------------------------------------------------
    | Admin — Master Data & Settings
    |----------------------------------------------------------------------
    */
    Route::middleware('can:manage-admin')->prefix('admin')->group(function () {
        require __DIR__ . '/master-data.php';

        Route::get('students/import/template', [StudentController::class, 'importTemplate'])->name('students.import.template');
        registerMasterDataRoutes('students', StudentController::class);
        Route::get('students/{student}/attendance-history', [StudentController::class, 'attendanceHistory'])
            ->name('students.attendance-history');
        Route::get('students/{student}/print-card', [StudentController::class, 'printCard'])
            ->name('students.print-card');
        Route::post('students/{student}/reset-password', [StudentController::class, 'resetPassword'])
            ->name('students.reset-password');
        Route::resource('students', StudentController::class);

        Route::get('teachers/import/template', [TeacherController::class, 'importTemplate'])->name('teachers.import.template');
        registerMasterDataRoutes('teachers', TeacherController::class);
        Route::post('teachers/{teacher}/homeroom', [TeacherController::class, 'updateHomeroom'])->name('teachers.homeroom.update');
        Route::post('teachers/{teacher}/subjects', [TeacherController::class, 'storeClassSubject'])->name('teachers.subjects.store');
        Route::delete('teachers/{teacher}/subjects/{classSubject}', [TeacherController::class, 'destroyClassSubject'])->name('teachers.subjects.destroy');
        Route::post('teachers/{teacher}/reset-password', [TeacherController::class, 'resetPassword'])->name('teachers.reset-password');
        Route::resource('teachers', TeacherController::class);

        registerMasterDataRoutes('classes', ClassController::class);
        Route::post('classes/promote', [ClassController::class, 'promote'])->name('classes.promote');
        Route::post('classes/{class}/subjects', [ClassController::class, 'storeSubject'])->name('classes.subjects.store');
        Route::delete('classes/{class}/subjects/{classSubject}', [ClassController::class, 'destroySubject'])
            ->name('classes.subjects.destroy');
        Route::resource('classes', ClassController::class);

        registerMasterDataRoutes('majors', MajorController::class);
        Route::resource('majors', MajorController::class);

        registerMasterDataRoutes('subjects', SubjectController::class);
        Route::resource('subjects', SubjectController::class);

        registerMasterDataRoutes('academic-years', AcademicYearController::class, false);
        Route::resource('academic-years', AcademicYearController::class);

        registerMasterDataRoutes('semesters', SemesterController::class, false);
        Route::resource('semesters', SemesterController::class);

        Route::delete('users/bulk-destroy', [UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
        Route::resource('users', UserController::class)->except(['show']);
        Route::put('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::put('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings/update-school', [SettingController::class, 'updateSchool'])
            ->middleware('throttle:' . config('security.rate_limits.settings', '10,1'))
            ->name('settings.school');
        Route::post('settings/update-attendance', [SettingController::class, 'updateAttendance'])
            ->middleware('throttle:' . config('security.rate_limits.settings', '10,1'))
            ->name('settings.attendance');
        Route::post('settings/update-kartu', [SettingController::class, 'updateKartu'])
            ->middleware('throttle:' . config('security.rate_limits.settings', '10,1'))
            ->name('settings.kartu');
        Route::post('settings/update-features', [SettingController::class, 'updateFeatures'])
            ->middleware('throttle:' . config('security.rate_limits.settings', '10,1'))
            ->name('settings.features');
        Route::post('settings/kartu-sync-members', [SettingController::class, 'syncKartuMembers'])
            ->name('settings.kartu.sync-members');
        Route::post('settings/kartu-pull-attendance', [SettingController::class, 'pullKartuAttendance'])
            ->name('settings.kartu.pull-attendance');
        Route::post('settings/tap-regenerate-key', [SettingController::class, 'regenerateTapApiKey'])
            ->name('settings.tap.regenerate-key');
        Route::post('settings/cbt-sync', [SettingController::class, 'syncCbt'])
            ->name('settings.cbt.sync');


        Route::prefix('security')->name('security.')->group(function () {
            Route::get('/', [SecurityController::class, 'index'])->name('index');
            Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs');
            Route::get('login-history', [LoginHistoryController::class, 'index'])->name('login-history');
            Route::get('sessions', [SessionManagementController::class, 'index'])->name('sessions');
            Route::delete('sessions/{sessionId}', [SessionManagementController::class, 'destroy'])->name('sessions.destroy');
            Route::post('sessions/revoke-others', [SessionManagementController::class, 'revokeOthers'])->name('sessions.revoke-others');
            Route::post('sessions/revoke-user/{user}', [SessionManagementController::class, 'revokeUser'])->name('sessions.revoke-user');
            Route::get('maintenance', [SystemMaintenanceController::class, 'index'])->name('maintenance');
            Route::post('maintenance/backup', [SystemMaintenanceController::class, 'backup'])->name('maintenance.backup');
            Route::get('maintenance/download/{filename}', [SystemMaintenanceController::class, 'downloadBackup'])->name('maintenance.download');
        });
    });

    /*
    |----------------------------------------------------------------------
    | Guru & Admin — E-Learning & Attendance Management
    |----------------------------------------------------------------------
    */
    Route::get('materials/detail/{material}', [MaterialController::class, 'show'])->name('materials.show');
    Route::get('materials/detail/{material}/download', [MaterialController::class, 'download'])->name('materials.download');

    Route::middleware('can:access-elearning')->prefix('akademik')->group(function () {
        Route::get('elearning', [DashboardController::class, 'elearning'])->name('elearning.index');
        Route::get('class-subjects/{classSubject}', [StudentAssignmentController::class, 'showClassSubject'])
            ->name('teacher.class-subjects.show');

        // Materials
        Route::get('materials/{classSubject}', [MaterialController::class, 'index'])->name('materials.index');
        Route::get('materials/{classSubject}/create', [MaterialController::class, 'create'])->name('materials.create');
        Route::post('materials/{classSubject}', [MaterialController::class, 'store'])->name('materials.store');
        Route::get('materials/detail/{material}/edit', [MaterialController::class, 'edit'])->name('materials.edit');
        Route::put('materials/detail/{material}', [MaterialController::class, 'update'])->name('materials.update');
        Route::delete('materials/detail/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');
        Route::put('materials/detail/{material}/toggle-publish', [MaterialController::class, 'togglePublish'])
            ->name('materials.toggle-publish');

        // Assignments
        Route::get('assignments/{classSubject}', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('assignments/{classSubject}/create', [AssignmentController::class, 'create'])->name('assignments.create');
        Route::post('assignments/{classSubject}', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::get('assignments/detail/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');
        Route::get('assignments/detail/{assignment}/download', [AssignmentController::class, 'download'])->name('assignments.download');
        Route::get('assignments/submissions/{submission}/download', [AssignmentController::class, 'downloadSubmission'])
            ->name('assignments.submission.download');
        Route::get('assignments/detail/{assignment}/edit', [AssignmentController::class, 'edit'])->name('assignments.edit');
        Route::put('assignments/detail/{assignment}', [AssignmentController::class, 'update'])->name('assignments.update');
        Route::delete('assignments/detail/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');
        Route::put('assignments/detail/{assignment}/toggle-publish', [AssignmentController::class, 'togglePublish'])
            ->name('assignments.toggle-publish');
        Route::get('assignments/detail/{assignment}/grade', [AssignmentController::class, 'gradeSubmissions'])
            ->name('assignments.grade');
        Route::post('assignments/submissions/{submission}/grade', [AssignmentController::class, 'storeGrade'])
            ->name('assignments.store-grade');

        // Staff-only Academic management
        Route::middleware('can:access-staff')->group(function () {
            // Attendance management
            Route::get('attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
            Route::post('attendance', [AttendanceController::class, 'store'])->name('attendance.store');
            Route::post('attendance/quick-save', [AttendanceController::class, 'quickSave'])->name('attendance.quick-save');
            Route::post('attendance/update-detail', [AttendanceController::class, 'updateDetail'])->name('attendance.update-detail');
            Route::post('attendance/save-session-notes', [AttendanceController::class, 'saveSessionNotes'])->name('attendance.save-session-notes');
            Route::get('attendance/homeroom/create', [AttendanceController::class, 'homeroomCreate'])->name('attendance.homeroom-create');
            Route::post('attendance/homeroom', [AttendanceController::class, 'homeroomStore'])->name('attendance.homeroom-store');
            Route::get('attendance/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
            Route::put('attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
            Route::get('attendance/self-logs', [SelfAttendanceController::class, 'logs'])->name('attendance.self-logs');
            Route::get('attendance/scan-qr', [SelfAttendanceController::class, 'scanner'])->name('attendance.scan-qr');
            Route::post('attendance/scan-student-qr', [SelfAttendanceController::class, 'processScanStudentQr'])->name('attendance.scan-student-qr');

            // Stasiun Tap Kartu / Pink RFID Reader Gate
            Route::get('attendance/tap-station', [SelfAttendanceController::class, 'tapStation'])->name('attendance.tap-station');
            Route::post('attendance/update-student-rfid', [SelfAttendanceController::class, 'updateStudentRfid'])->name('attendance.update-student-rfid');

            // Pusat Generator Barcode & Kartu Siswa / Guru
            Route::get('attendance/barcode-center', [AttendanceBarcodeController::class, 'index'])->name('attendance.barcode-center');
            Route::get('attendance/barcode-print-sheet', [AttendanceBarcodeController::class, 'printSheet'])->name('attendance.barcode-print-sheet');
            Route::get('attendance/barcode-print-cards', [AttendanceBarcodeController::class, 'printCards'])->name('attendance.barcode-print-cards');

            // Presensi Mandiri Guru (Selfie + GPS)
            Route::get('attendance/teacher-checkin', [TeacherAttendanceController::class, 'checkinView'])->name('attendance.teacher-checkin');
            Route::post('attendance/teacher-checkin', [TeacherAttendanceController::class, 'checkIn'])->name('api.attendance.teacher.check-in');
            Route::post('attendance/teacher-checkout', [TeacherAttendanceController::class, 'checkOut'])->name('api.attendance.teacher.check-out');
            Route::get('attendance/teacher-status', [TeacherAttendanceController::class, 'status'])->name('api.attendance.teacher.status');

            // Schedules management (Kepala Sekolah/Admin only)
            Route::middleware('can:manage-admin')->group(function () {
                Route::get('schedules/batch', [ScheduleController::class, 'batchCreate'])->name('schedules.batch');
                Route::post('schedules/batch', [ScheduleController::class, 'batchStore'])->name('schedules.batch-store');
                Route::post('schedules/copy', [ScheduleController::class, 'copySchedule'])->name('schedules.copy');
                Route::post('schedules/bulk-delete', [ScheduleController::class, 'bulkDelete'])->name('schedules.bulk-delete');
                Route::get('schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
                Route::post('schedules', [ScheduleController::class, 'store'])->name('schedules.store');
                Route::get('schedules/{schedule}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
                Route::put('schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
                Route::delete('schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
            });

        });
    });

    // Attendance & schedules — read access
    Route::middleware('can:access-siswa')->prefix('siswa')->group(function () {
        Route::get('attendance/self-checkin', [SelfAttendanceController::class, 'index'])->name('attendance.self-checkin');
        Route::post('attendance/self-checkin/face-gps', [SelfAttendanceController::class, 'processFaceGpsCheckIn'])->name('attendance.self-checkin.face-gps');
        Route::get('attendance/status-poll', [SelfAttendanceController::class, 'checkStatusPoll'])->name('attendance.status-poll');
    });


    Route::prefix('lms')->group(function () {
        Route::middleware('can:access-staff')->group(function () {
            Route::get('attendance/export/students/excel', [AttendanceController::class, 'exportStudentsExcel'])->name('attendance.export.students.excel');
            Route::get('attendance/export/teachers/excel', [AttendanceController::class, 'exportTeachersExcel'])->name('attendance.export.teachers.excel');
        });
        Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('attendance/{attendance}', [AttendanceController::class, 'show'])->name('attendance.show');
        Route::get('schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    });


    /*
    |----------------------------------------------------------------------
    | Announcements
    |----------------------------------------------------------------------
    */
    Route::middleware('can:access-staff')->prefix('akademik')->group(function () {
        Route::get('announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
        Route::post('announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::delete('announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    });

    /*
    |----------------------------------------------------------------------
    | LMS — Shared (semua role)
    |----------------------------------------------------------------------
    */
    Route::prefix('lms')->group(function () {
        Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');
    Route::get('announcements/{announcement}/download', [AnnouncementController::class, 'download'])->name('announcements.download');

    Route::get('discussions/{classSubject}', [DiscussionController::class, 'index'])->name('discussions.index');
    Route::get('discussions/{classSubject}/create', [DiscussionController::class, 'create'])->name('discussions.create');
    Route::post('discussions/{classSubject}', [DiscussionController::class, 'store'])->name('discussions.store');
    Route::get('discussions/thread/{discussion}', [DiscussionController::class, 'show'])->name('discussions.show');
    Route::post('discussions/thread/{discussion}/reply', [DiscussionController::class, 'reply'])->name('discussions.reply');

    Route::post('comments/{type}/{id}', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    Route::get('academic-calendar', [AcademicCalendarController::class, 'index'])->name('academic-calendar.index');
    Route::middleware('can:access-elearning')->group(function () {
        Route::get('academic-calendar/create', [AcademicCalendarController::class, 'create'])->name('academic-calendar.create');
        Route::post('academic-calendar', [AcademicCalendarController::class, 'store'])->name('academic-calendar.store');
        Route::delete('academic-calendar/{academicCalendar}', [AcademicCalendarController::class, 'destroy'])
            ->name('academic-calendar.destroy');
    });
    }); // End of shared LMS prefix

    /*
    |----------------------------------------------------------------------
    | Student Portal
    |----------------------------------------------------------------------
    */
    Route::middleware('can:access-siswa')->prefix('siswa')->group(function () {
        Route::get('my-attendance', [AttendanceController::class, 'myAttendance'])->name('attendance.mine');
        Route::get('my-card', [StudentController::class, 'myCard'])->name('students.my-card');
        Route::get('my-materials', [MaterialController::class, 'studentMaterials'])->name('materials.mine');

        Route::get('my-assignments', [StudentAssignmentController::class, 'index'])->name('student.assignments.index');
        Route::get('my-assignments/{assignment}', [StudentAssignmentController::class, 'show'])->name('student.assignments.show');
        Route::post('my-assignments/{assignment}/submit', [StudentAssignmentController::class, 'submit'])
            ->name('student.assignments.submit');

        Route::get('class-subjects/{classSubject}', [StudentAssignmentController::class, 'showClassSubject'])
            ->name('student.class-subjects.show');
        Route::post('class-subjects/{classSubject}/check-in', [StudentAssignmentController::class, 'checkInSubject'])
            ->name('student.class-subjects.check-in');
    });


    // API self-attendance routes using web session
    Route::middleware(['throttle:30,1'])
        ->prefix('api/attendance/self')->name('api.attendance.self.')->group(function () {
        Route::get('config', [\App\Http\Controllers\Api\SelfAttendanceApiController::class, 'config'])->name('config');
        Route::post('face-profile', [\App\Http\Controllers\Api\SelfAttendanceApiController::class, 'saveFaceProfile'])->name('face-profile');
        Route::post('check-in', [\App\Http\Controllers\Api\SelfAttendanceApiController::class, 'checkIn'])->name('check-in');
        Route::get('history', [\App\Http\Controllers\Api\SelfAttendanceApiController::class, 'history'])->name('history');
    });



});

// Fallback route to serve public storage files when symlink/junction is not supported or working (e.g. PHP built-in server on Windows)
Route::get('storage/{path}', function ($path) {
    if (str_contains($path, '..') || str_contains($path, '\\')) {
        abort(403, 'Akses ditolak.');
    }

    $fullPath = storage_path('app/public/' . $path);
    $realBase = realpath(storage_path('app/public'));

    if (!file_exists($fullPath)) {
        abort(404, 'File tidak ditemukan.');
    }

    $realPath = realpath($fullPath);
    if (!$realPath || !$realBase || !str_starts_with($realPath, $realBase)) {
        abort(403, 'Akses ditolak.');
    }
    
    return response()->file($realPath);
})->where('path', '.*')->name('storage.fallback');
