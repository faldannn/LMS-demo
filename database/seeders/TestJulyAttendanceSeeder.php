<?php

namespace Database\Seeders;

use App\Models\{AcademicYear, Attendance, AttendanceDetail, Classes, ClassSubject, Schedule, Student, Subject, Teacher};
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TestJulyAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Dapatkan atau buat kelas XII IPA 1
        $class = Classes::firstOrCreate(
            ['name' => 'XII IPA 1'],
            ['grade' => '12', 'session' => 'pagi']
        );

        $academicYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();

        // 2. Pastikan TEPAT 5 SISWA di kelas XII IPA 1
        $existingStudents = Student::where('class_id', $class->id)->orderBy('id')->get();
        if ($existingStudents->count() < 5) {
            $needed = 5 - $existingStudents->count();
            $names = ['Ammar Syafi\'i', 'Andi Kurniawan', 'Budi Santoso', 'Citra Dewi', 'Eka Pratama'];
            for ($i = 0; $i < $needed; $i++) {
                $idx = $existingStudents->count() + $i;
                $sName = $names[$idx] ?? "Siswa Testing " . ($idx + 1);
                Student::create([
                    'class_id' => $class->id,
                    'nis' => '1200' . ($idx + 1),
                    'nisn' => '0051200' . ($idx + 1),
                    'name' => $sName,
                    'gender' => ($idx % 2 === 0) ? 'L' : 'P',
                    'status' => true,
                ]);
            }
            $students = Student::where('class_id', $class->id)->orderBy('id')->get();
        } else {
            $students = $existingStudents->take(5);
        }

        // 3. Ambil Guru & Mapel ASLI yang sudah terdaftar di Database
        $getTeacherBySubject = function (string $subjectCode, string $defaultSubjectName) {
            $subject = Subject::where('code', $subjectCode)
                ->orWhere('name', 'like', "%{$defaultSubjectName}%")
                ->first();

            $teacher = null;
            if ($subject) {
                // Cari guru pengampu mapel ini
                $teacher = Teacher::whereHas('subjects', fn($q) => $q->where('subjects.id', $subject->id))->first();
            }

            if (!$teacher) {
                // Fallback guru sesuai spesialisasi
                $teacherMap = [
                    'B-IND' => 'Wahyuni Elfa S.Pd',
                    'MTK-W' => 'Lazuardi Sastra Al Ashri',
                    'FIS' => 'Anang Faturahman Fauzi',
                    'PAI' => 'Rosdiantini',
                    'KIM' => 'EKI KHAERUL FIKRI',
                    'BIO' => 'Asih Pertiwi',
                    'B-ING' => 'Sri Nofianda',
                    'SEJ' => 'Sa\'durrofiq, SH',
                    'INF' => 'ANGGARA NUR AMRI MUKMININ',
                    'PJO' => 'Azhar Nurrosyid',
                    'PPKN' => 'Zakaria',
                    'KON' => 'Mukhlisa Yulinda',
                ];
                $targetTeacherName = $teacherMap[$subjectCode] ?? null;
                if ($targetTeacherName) {
                    $teacher = Teacher::where('name', 'like', "%{$targetTeacherName}%")->first();
                }
            }

            if (!$teacher) {
                $teacher = Teacher::first();
            }

            return [
                'subject_id' => $subject?->id,
                'subject_name' => $subject?->name ?? $defaultSubjectName,
                'teacher' => $teacher,
            ];
        };

        // Pemetaan Mapel Asli & Guru Asli dari Database
        $mapBind = $getTeacherBySubject('B-IND', 'Bahasa Indonesia');
        $mapMtk  = $getTeacherBySubject('MTK-W', 'Matematika Wajib');
        $mapFis  = $getTeacherBySubject('FIS', 'Fisika');
        $mapPai  = $getTeacherBySubject('PAI', 'Pendidikan Agama Islam');
        $mapKim  = $getTeacherBySubject('KIM', 'Kimia');
        $mapBio  = $getTeacherBySubject('BIO', 'Biologi');
        $mapBing = $getTeacherBySubject('B-ING', 'Bahasa Inggris');
        $mapSej  = $getTeacherBySubject('SEJ', 'Sejarah Indonesia');
        $mapInf  = $getTeacherBySubject('INF', 'Informatika');
        $mapPjok = $getTeacherBySubject('PJO', 'Pendidikan Jasmani Olahraga');
        $mapPpkn = $getTeacherBySubject('PPKN', 'Pendidikan Pancasila dan Kewarganegaraan');
        $mapBk   = $getTeacherBySubject('KON', 'Bimbingan');

        // 4. Update / Buat Jadwal Pelajaran (Schedule) & Mapel Kelas (ClassSubject) dengan Guru ASLI
        Schedule::where('class_id', $class->id)->delete();
        ClassSubject::where('class_id', $class->id)->delete();

        $scheduleConfig = [
            // Hari 1: Senin
            1 => [
                ['info' => $mapBind, 'start' => '07:00', 'end' => '08:15'],
                ['info' => $mapMtk,  'start' => '08:15', 'end' => '09:30'],
                ['info' => $mapFis,  'start' => '09:45', 'end' => '11:00'],
                ['info' => $mapPai,  'start' => '11:00', 'end' => '12:15'],
            ],
            // Hari 2: Selasa
            2 => [
                ['info' => $mapKim,  'start' => '07:00', 'end' => '08:15'],
                ['info' => $mapBio,  'start' => '08:15', 'end' => '09:30'],
                ['info' => $mapBing, 'start' => '09:45', 'end' => '11:00'],
                ['info' => $mapSej,  'start' => '11:00', 'end' => '12:15'],
            ],
            // Hari 3: Rabu
            3 => [
                ['info' => $mapMtk,  'start' => '07:00', 'end' => '08:15'],
                ['info' => $mapFis,  'start' => '08:15', 'end' => '09:30'],
                ['info' => $mapBind, 'start' => '09:45', 'end' => '11:00'],
                ['info' => $mapInf,  'start' => '11:00', 'end' => '12:15'],
            ],
            // Hari 4: Kamis
            4 => [
                ['info' => $mapBio,  'start' => '07:00', 'end' => '08:15'],
                ['info' => $mapKim,  'start' => '08:15', 'end' => '09:30'],
                ['info' => $mapPjok, 'start' => '09:45', 'end' => '11:00'],
                ['info' => $mapBing, 'start' => '11:00', 'end' => '12:15'],
            ],
            // Hari 5: Jumat
            5 => [
                ['info' => $mapPai,  'start' => '07:00', 'end' => '08:15'],
                ['info' => $mapInf,  'start' => '08:15', 'end' => '09:30'],
                ['info' => $mapPpkn, 'start' => '09:45', 'end' => '11:00'],
                ['info' => $mapBk,   'start' => '11:00', 'end' => '11:45'],
            ],
        ];

        $scheduleMapByDayAndSlot = [];

        foreach ($scheduleConfig as $dayOfWeek => $slots) {
            foreach ($slots as $slotIndex => $slot) {
                $info = $slot['info'];
                $teacherObj = $info['teacher'];

                Schedule::create([
                    'class_id' => $class->id,
                    'teacher_id' => $teacherObj->id,
                    'subject' => $info['subject_name'],
                    'day_of_week' => $dayOfWeek,
                    'start_time' => $slot['start'],
                    'end_time' => $slot['end'],
                    'academic_year_id' => $academicYear?->id,
                ]);

                if ($info['subject_id']) {
                    ClassSubject::firstOrCreate([
                        'class_id' => $class->id,
                        'subject_id' => $info['subject_id'],
                    ], [
                        'teacher_id' => $teacherObj->id,
                        'academic_year_id' => $academicYear?->id,
                    ]);
                }

                $slotNum = $slotIndex + 1;
                $scheduleMapByDayAndSlot[$dayOfWeek][$slotNum] = [
                    'subject_name' => $info['subject_name'],
                    'teacher' => $teacherObj,
                ];
            }
        }

        // 5. Bersihkan data absensi lama di kelas ini untuk bulan Juli 2026 agar tidak ganda
        $oldAttIds = Attendance::where('class_id', $class->id)
            ->whereBetween('date', ['2026-07-01', '2026-07-31'])
            ->pluck('id');
        AttendanceDetail::whereIn('attendance_id', $oldAttIds)->delete();
        Attendance::whereIn('id', $oldAttIds)->delete();

        // 6. Suntik Data Absensi Bulan Juli 2026 dengan Guru & Mapel Sesuai Jadwal
        $startDate = Carbon::create(2026, 7, 1);
        $endDate = Carbon::create(2026, 7, 31);
        $defaultTeacher = Teacher::first();

        $currDate = $startDate->copy();
        while ($currDate->lte($endDate)) {
            if (!$currDate->isWeekend()) {
                $dateStr = $currDate->toDateString();
                $dayOfWeek = $currDate->dayOfWeekIso; // 1 (Mon) - 5 (Fri)

                // a) Absensi Masuk Gerbang / Harian (Pagi)
                $dailyAtt = Attendance::create([
                    'date' => $dateStr,
                    'class_id' => $class->id,
                    'teacher_id' => $defaultTeacher?->id,
                    'session' => 'pagi',
                    'notes' => 'Presensi Harian Pagi',
                ]);

                $studentDailyStatus = [];
                foreach ($students as $student) {
                    $rand = rand(1, 100);
                    if ($rand <= 80) {
                        $st = 'hadir';
                        $checkIn = '06:' . str_pad(rand(35, 58), 2, '0', STR_PAD_LEFT);
                        $note = 'Hadir tepat waktu';
                    } elseif ($rand <= 90) {
                        $st = 'terlambat';
                        $checkIn = '07:' . str_pad(rand(5, 25), 2, '0', STR_PAD_LEFT);
                        $note = 'Terlambat masuk sekolah';
                    } elseif ($rand <= 95) {
                        $st = rand(0, 1) ? 'sakit' : 'izin';
                        $checkIn = null;
                        $note = ($st === 'sakit') ? 'Surat dokter terlampir' : 'Izin acara keluarga';
                    } else {
                        $st = 'alfa';
                        $checkIn = null;
                        $note = 'Tanpa keterangan';
                    }

                    $studentDailyStatus[$student->id] = $st;

                    AttendanceDetail::create([
                        'attendance_id' => $dailyAtt->id,
                        'student_id' => $student->id,
                        'status' => $st,
                        'check_in' => $checkIn,
                        'notes' => $note,
                    ]);
                }

                // b) Absensi Mapel 1 s/d Mapel 4 sesuai Guru & Mapel di Hari Tersebut
                for ($m = 1; $m <= 4; $m++) {
                    $slotData = $scheduleMapByDayAndSlot[$dayOfWeek][$m] ?? null;
                    $mapelTeacher = $slotData['teacher'] ?? $defaultTeacher;
                    $mapelName = $slotData['subject_name'] ?? "Mapel {$m}";

                    $mapelAtt = Attendance::create([
                        'date' => $dateStr,
                        'class_id' => $class->id,
                        'teacher_id' => $mapelTeacher->id,
                        'session' => "mapel_{$m}",
                        'notes' => "Absensi Mapel {$m} ({$mapelName})",
                    ]);

                    foreach ($students as $student) {
                        $dStatus = $studentDailyStatus[$student->id] ?? 'hadir';

                        if (in_array($dStatus, ['sakit', 'izin'], true)) {
                            $mStatus = $dStatus;
                            $mCheckIn = null;
                            $mNote = 'Disinkronkan dari Wali Kelas (' . ucfirst($dStatus) . ')';
                        } else {
                            $mRand = rand(1, 100);
                            if ($mRand <= 85) {
                                $mStatus = 'hadir';
                                $mCheckIn = '07:05';
                                $mNote = 'Hadir mengikuti KBM ' . $mapelName;
                            } elseif ($mRand <= 92) {
                                $mStatus = 'terlambat';
                                $mCheckIn = '07:15';
                                $mNote = 'Terlambat masuk jam pelajaran ' . $mapelName;
                            } else {
                                $mStatus = 'alfa';
                                $mCheckIn = null;
                                $mNote = 'Tidak ada di kelas saat KBM ' . $mapelName;
                            }
                        }

                        AttendanceDetail::create([
                            'attendance_id' => $mapelAtt->id,
                            'student_id' => $student->id,
                            'status' => $mStatus,
                            'check_in' => $mCheckIn,
                            'notes' => $mNote,
                        ]);
                    }
                }
            }

            $currDate->addDay();
        }
    }
}
