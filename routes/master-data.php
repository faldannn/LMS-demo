<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

if (!function_exists('registerMasterDataRoutes')) {
    function registerMasterDataRoutes(string $uri, string $controller, bool $withImport = true): void
    {
        Route::get("{$uri}/trashed", [$controller, 'trashed'])->name("{$uri}.trashed");
        Route::post("{$uri}/{id}/restore", [$controller, 'restore'])->name("{$uri}.restore");
        Route::delete("{$uri}/{id}/force", [$controller, 'forceDestroy'])->name("{$uri}.force-destroy");
        Route::get("{$uri}/export/excel", [$controller, 'exportExcel'])->name("{$uri}.export.excel");
        Route::get("{$uri}/export/pdf", [$controller, 'exportPdf'])->name("{$uri}.export.pdf");
        if ($withImport) {
            Route::post("{$uri}/import", [$controller, 'import'])->name("{$uri}.import");
        }
    }
}
