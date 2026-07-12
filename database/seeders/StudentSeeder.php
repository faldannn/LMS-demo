<?php

namespace Database\Seeders;

use App\Models\{Classes, Student, User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $classes = Classes::all()->keyBy('name');

        $students = [
            ['2025001', '0057391234', 'Andi Kurniawan', 'L', '2007-03-15', 'XII IPA 1', '081234567890', 'Bapak Surya'],
        ];

        foreach ($students as [$nis, $nisn, $name, $gender, $dob, $kelas, $phone, $parentName]) {
            $class = $classes[$kelas] ?? null;
            if (!$class)
                continue;

            // Create student login
            $user = User::create([
                'username' => $nis,
                'password' => Hash::make($nis),
                'name' => $name,
                'role' => 'siswa',
                'status' => true,
            ]);

            Student::create([
                'nis' => $nis,
                'nisn' => $nisn,
                'name' => $name,
                'gender' => $gender,
                'dob' => $dob,
                'class_id' => $class->id,
                'parent_name' => $parentName,
                'parent_phone' => $phone,
                'user_id' => $user->id,
                'status' => true,
            ]);
        }
    }
}