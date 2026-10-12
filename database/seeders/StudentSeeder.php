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
            ['2025002', '0057391235', 'Bella Safira', 'P', '2007-04-18', 'XII IPA 1', '081234567891', 'Ibu Ratna'],
            ['2025003', '0057391236', 'Budi Santoso', 'L', '2007-01-22', 'XII IPA 1', '081234567892', 'Bapak Joko'],
            ['2025004', '0057391237', 'Citra Lestari', 'P', '2007-06-11', 'XII IPA 1', '081234567893', 'Ibu Maya'],
            ['2025005', '0057391238', 'Dimas Pratama', 'L', '2007-08-05', 'XII IPA 1', '081234567894', 'Bapak Agus'],
            ['2025006', '0057391239', 'Eka Rahmawati', 'P', '2007-09-14', 'XII IPA 1', '081234567895', 'Ibu Nur'],
            ['2025007', '0057391240', 'Farhan Alamsyah', 'L', '2007-11-20', 'XII IPA 1', '081234567896', 'Bapak Hendra'],
            ['2025008', '0057391241', 'Gita Permata', 'P', '2007-02-28', 'XII IPA 1', '081234567897', 'Ibu Sri'],
            ['2025009', '0057391242', 'Muhammad Fikri', 'L', '2007-05-19', 'XII IPA 1', '081234567898', 'Bapak Gunawan'],
            ['2025010', '0057391243', 'Nabila Zahra', 'P', '2007-10-09', 'XII IPA 1', '081234567899', 'Ibu Yuliana'],
        ];

        foreach ($students as [$nis, $nisn, $name, $gender, $dob, $kelas, $phone, $parentName]) {
            $class = $classes[$kelas] ?? null;
            if (!$class)
                continue;

            // Create student login
            $user = User::firstOrCreate([
                'username' => $nis,
            ], [
                'password' => Hash::make($nis),
                'name' => $name,
                'role' => 'siswa',
                'status' => true,
            ]);

            Student::firstOrCreate([
                'nis' => $nis,
            ], [
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