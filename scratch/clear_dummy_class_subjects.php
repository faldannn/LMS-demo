<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ClassSubject;
use App\Models\Schedule;
use App\Models\Material;
use App\Models\Assignment;
use Illuminate\Support\Facades\Schema;

echo "=== MEMBERSIHKAN DATA DUMMY CLASS_SUBJECT & JADWAL ===\n";

Schema::disableForeignKeyConstraints();

$deletedMaterials = Material::query()->delete();
echo "Materials dihapus: {$deletedMaterials}\n";

$deletedAssignments = Assignment::query()->delete();
echo "Assignments dihapus: {$deletedAssignments}\n";

$deletedSchedules = Schedule::query()->delete();
echo "Schedules dihapus: {$deletedSchedules}\n";

$deletedClassSubjects = ClassSubject::query()->delete();
echo "ClassSubject (Dummy) dihapus: {$deletedClassSubjects}\n";

Schema::enableForeignKeyConstraints();

echo "BERHASIL! Seluruh data dummy mata pelajaran & jadwal telah dibersihkan.\n";
