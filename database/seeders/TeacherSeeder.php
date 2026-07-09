<?php

namespace Database\Seeders;

use App\Models\{Teacher, User};
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = [
            ['199001012020121001', 'Dra. Hj. Siti Rahayu, M.Pd', 'Bahasa Indonesia', '081298765432', '199001012020121001', 'siti.rahayu@assyafiiyah02bekasi.sch.id'],
            ['198503052019031002', 'Budi Santoso, S.Pd', 'Matematika', '081298765433', '198503052019031002', 'budi.santoso@assyafiiyah02bekasi.sch.id'],
            ['199208152021122003', 'Indah Permata, S.Si', 'Fisika', '081298765434', '199208152021122003', 'indah.permata@assyafiiyah02bekasi.sch.id'],
            ['198712302018031004', 'Ahmad Firdaus, S.Sos', 'Ekonomi', '081298765435', '198712302018031004', 'ahmad.firdaus@assyafiiyah02bekasi.sch.id'],
            ['199504122022121005', 'Dewi Kusuma, S.Pd', 'Bahasa Inggris', '081298765436', '199504122022121005', 'dewi.kusuma@assyafiiyah02bekasi.sch.id'],
        ];

        foreach ($teachers as [$nip, $name, $subjectName, $phone, $username, $email]) {
            $user = User::where('username', $username)->first();
            
            $subjects = [];
            if ($subjectName === 'Matematika') {
                $subjects = \App\Models\Subject::where('name', 'like', '%Matematika%')->get();
            } else {
                $subjects = \App\Models\Subject::where('name', 'like', "%{$subjectName}%")->get();
            }
            
            $dbSubjectNames = collect($subjects)->pluck('name')->toArray();
            $subjectString = !empty($dbSubjectNames) ? implode(', ', $dbSubjectNames) : $subjectName;

            $teacher = Teacher::create([
                'nip' => $nip,
                'name' => $name,
                'subject' => $subjectString,
                'phone' => $phone,
                'email' => $email,
                'user_id' => $user->id,
                'status' => true,
            ]);

            if (!empty($subjects)) {
                $teacher->subjects()->sync(collect($subjects)->pluck('id')->toArray());
            }
        }
    }
}