<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyUserSeeder extends Seeder
{
    public function run(): void
    {
        $dummyStudents = Student::with('user')
            ->where('status', true)
            ->whereNotNull('user_id')
            ->limit(5)
            ->get();

        foreach ($dummyStudents as $student) {
            $user = $student->user;
            if ($user) {
                $user->update([
                    'role' => UserRole::Siswa,
                    'status' => true,
                ]);
                continue;
            }

            User::updateOrCreate(
                ['username' => $student->nis],
                [
                    'password' => Hash::make($student->nis),
                    'name' => $student->name,
                    'email' => strtolower($student->nis) . '@siswa.assyafiiyah02bekasi.sch.id',
                    'role' => UserRole::Siswa,
                    'status' => true,
                ]
            );
        }
    }
}
