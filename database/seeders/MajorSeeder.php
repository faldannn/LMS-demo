<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    public function run(): void
    {
        $majors = [
            ['MIPA', 'Matematika dan Ilmu Pengetahuan Alam'],
            ['IPS', 'Ilmu Pengetahuan Sosial'],
            ['BAHASA', 'Bahasa dan Budaya'],
        ];

        foreach ($majors as [$code, $name]) {
            Major::create([
                'code' => $code,
                'name' => $name,
                'status' => true,
            ]);
        }
    }
}
