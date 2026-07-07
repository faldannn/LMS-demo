<?php

namespace Database\Seeders;

use App\Models\{Attendance, AttendanceDetail, Classes, Student, Teacher};
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'terlambat', 'sakit', 'izin', 'alfa'];
        $teacher = Teacher::first();
        $classes = Classes::with('students')->get();

        // Seed last 30 weekdays
        $date = Carbon::now()->subDays(30);
        while ($date->lte(now())) {
            if ($date->isWeekend()) {
                $date->addDay();
                continue;
            }

            foreach ($classes as $class) {
                if (!$class->students->count()) {
                    continue;
                }

                $att = Attendance::create([
                    'date' => $date->toDateString(),
                    'class_id' => $class->id,
                    'teacher_id' => $teacher->id,
                    'session' => 'pagi',
                ]);

                foreach ($class->students as $student) {
                    AttendanceDetail::create([
                        'attendance_id' => $att->id,
                        'student_id' => $student->id,
                        'status' => $statuses[array_rand($statuses)],
                        'check_in' => '07:' . str_pad(rand(0, 30), 2, '0', STR_PAD_LEFT) . ':00',
                    ]);
                }
            }
            $date->addDay();
        }
    }
}