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
            ['2025002', '0057391235', 'Budi Santoso', 'L', '2007-06-22', 'XII IPA 1', '081234567891', 'Ibu Dewi'],
            ['2025003', '0057391236', 'Citra Dewi', 'P', '2007-09-10', 'XII IPS 1', '081234567892', 'Bapak Agus'],
            ['2025004', '0057391237', 'Dian Purnama', 'P', '2008-01-05', 'XI MIPA 1', '081234567893', 'Ibu Sri'],
            ['2025005', '0057391238', 'Eka Saputra', 'L', '2008-04-18', 'XI MIPA 1', '081234567894', 'Bapak Hendra'],
            ['2025006', '0057391239', 'Fajar Rizki', 'L', '2008-07-30', 'XI IPS 1', '081234567895', 'Ibu Yanti'],
            ['2025007', '0057391240', 'Gina Amalia', 'P', '2009-02-14', 'X MIPA 1', '081234567896', 'Bapak Rudi'],
            ['2025008', '0057391241', 'Hendra Gunawan', 'L', '2009-05-25', 'X MIPA 1', '081234567897', 'Ibu Lina'],
            ['2025009', '0057391242', 'Indah Lestari', 'P', '2009-08-11', 'X IPS 1', '081234567898', 'Bapak Tono'],
            ['2025010', '0057391243', 'Joko Prabowo', 'L', '2009-11-03', 'X IPS 1', '081234567899', 'Ibu Sari'],
            ['2025011', '0057391244', 'Kartika Sari', 'P', '2007-12-20', 'XII IPA 1', '081234567800', 'Bapak Bambang'],
            ['2025012', '0057391245', 'Lukman Hakim', 'L', '2008-03-07', 'XI MIPA 1', '081234567801', 'Ibu Nunung'],
            ['2025013', '0057391246', 'Maya Indriati', 'P', '2008-06-15', 'XI MIPA 1', '081234567802', 'Bapak Dodi'],
            ['2025014', '0057391247', 'Nando Setiawan', 'L', '2009-01-22', 'X MIPA 2', '081234567803', 'Ibu Ratna'],
            ['2025015', '0057391248', 'Okta Fitriani', 'P', '2009-04-30', 'X MIPA 2', '081234567804', 'Bapak Andi'],
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