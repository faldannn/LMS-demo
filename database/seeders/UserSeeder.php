<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'password' => Hash::make('superadmin123'),
                'name' => 'Super Admin',
                'email' => 'admin@assyafiiyah02bekasi.sch.id',
                'role' => UserRole::SuperAdmin,
                'status' => true,
            ]
        );

        User::updateOrCreate(
            ['username' => '198001012005011003'],
            [
                'password' => Hash::make('kepsek123'),
                'name' => 'Kepala Sekolah',
                'email' => 'kepala@assyafiiyah02bekasi.sch.id',
                'role' => UserRole::Admin,
                'status' => true,
            ]
        );

        $guruAccounts = [
            ['199001012020121001', 'Dra. Hj. Siti Rahayu, M.Pd', 'siti.rahayu@assyafiiyah02bekasi.sch.id'],
            ['198503052019031002', 'Budi Santoso, S.Pd', 'budi.santoso@assyafiiyah02bekasi.sch.id'],
            ['199208152021122003', 'Indah Permata, S.Si', 'indah.permata@assyafiiyah02bekasi.sch.id'],
            ['198712302018031004', 'Ahmad Firdaus, S.Sos', 'ahmad.firdaus@assyafiiyah02bekasi.sch.id'],
            ['199504122022121005', 'Dewi Kusuma, S.Pd', 'dewi.kusuma@assyafiiyah02bekasi.sch.id'],
        ];

        foreach ($guruAccounts as [$username, $name, $email]) {
            User::updateOrCreate(
                ['username' => $username],
                [
                    'password' => Hash::make('guru123'),
                    'name' => $name,
                    'email' => $email,
                    'role' => UserRole::Guru,
                    'status' => true,
                ]
            );
        }
    }
}
