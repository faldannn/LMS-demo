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
use App\Http\Controllers\ReportCardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\SelfAttendanceController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\LoginHistoryController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\SessionManagementController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SystemMaintenanceController;
use App\Http\Controllers\StudentAssignmentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentReportCardController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Authenticated Application
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check.active'])->group(function () {

    // Dashboard & analytics
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('can:access-elearning')->group(function () {
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

        registerMasterDataRoutes('students', StudentController::class);
        Route::get('students/{student}/attendance-history', [StudentController::class, 'attendanceHistory'])
            ->name('students.attendance-history');
        Route::get('students/{student}/print-card', [StudentController::class, 'printCard'])
            ->name('students.print-card');
        Route::resource('students', StudentController::class);

        registerMasterDataRoutes('teachers', TeacherController::class);
        Route::resource('teachers', TeacherController::class);

        registerMasterDataRoutes('classes', ClassController::class);
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

        Route::resource('users', UserController::class);
        Route::put('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::put('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings/update-school', [SettingController::class, 'updateSchool'])
            ->middleware('throttle:' . config('security.rate_limits.settings', '10,1'))
            ->name('settings.school');
        Route::post('settings/update-attendance', [SettingController::class, 'updateAttendance'])
            ->middleware('throttle:' . config('security.rate_limits.settings', '10,1'))
            ->name('settings.attendance');

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

        // Attendance management
        Route::get('attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
        Route::post('attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('attendance/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
        Route::put('attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');

        Route::get('attendance/self-logs', [SelfAttendanceController::class, 'logs'])->name('attendance.self-logs');

        // Schedules management
        Route::get('schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
        Route::post('schedules', [ScheduleController::class, 'store'])->name('schedules.store');
        Route::delete('schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
    });

    // Attendance & schedules — read access
    Route::middleware('can:access-siswa')->prefix('siswa')->group(function () {
        Route::get('attendance/self-checkin', [SelfAttendanceController::class, 'index'])->name('attendance.self-checkin');
    });

    Route::prefix('lms')->group(function () {
        Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('attendance/{attendance}', [AttendanceController::class, 'show'])->name('attendance.show');
        Route::get('schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    });


    /*
    |----------------------------------------------------------------------
    | Reports, Report Cards & Announcements
    |----------------------------------------------------------------------
    */
    Route::middleware('can:access-elearning')->prefix('akademik')->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
        Route::get('reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
        Route::get('reports/recap', [ReportController::class, 'recap'])->name('reports.recap');
        Route::get('reports/export/pdf', [ReportController::class, 'exportLegacyPdf'])->name('reports.pdf');
        Route::get('reports/export/excel', [ReportController::class, 'exportLegacyExcel'])->name('reports.excel');
        Route::get('reports/{type}/export/pdf', [ReportController::class, 'exportPdf'])
            ->middleware('throttle:' . config('security.rate_limits.reports_export', '15,1'))
            ->name('reports.type.pdf');
        Route::get('reports/{type}/export/excel', [ReportController::class, 'exportExcel'])
            ->middleware('throttle:' . config('security.rate_limits.reports_export', '15,1'))
            ->name('reports.type.excel');
        Route::get('reports/{type}', [ReportController::class, 'show'])->name('reports.show');

        Route::get('report-cards', [ReportCardController::class, 'index'])->name('report-cards.index');
        Route::post('report-cards/generate', [ReportCardController::class, 'generate'])->name('report-cards.generate');
        Route::get('report-cards/{reportCard}', [ReportCardController::class, 'show'])->name('report-cards.show');
        Route::put('report-cards/{reportCard}/toggle-publish', [ReportCardController::class, 'togglePublish'])
            ->name('report-cards.toggle-publish');
        Route::get('report-cards/{reportCard}/print', [ReportCardController::class, 'print'])->name('report-cards.print');

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


        Route::get('my-report-cards', [StudentReportCardController::class, 'index'])->name('student.report-cards.index');
        Route::get('my-report-cards/{reportCard}', [StudentReportCardController::class, 'show'])->name('student.report-cards.show');
    });

});
