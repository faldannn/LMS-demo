<?php

return [
    'types' => [
        'teacher-attendance' => [
            'title' => 'Kehadiran Guru',
            'description' => 'Rekap sesi presensi yang diinput guru per periode',
            'icon' => 'bi-person-check-fill',
            'color' => '#10b981',
            'bg' => '#d1fae5',
        ],
        'student-attendance' => [
            'title' => 'Kehadiran Siswa',
            'description' => 'Rekap kehadiran siswa per status (hadir, izin, sakit, alfa)',
            'icon' => 'bi-people-fill',
            'color' => '#1a56db',
            'bg' => '#e8f0fe',
        ],
        'grades' => [
            'title' => 'Nilai',
            'description' => 'Rekap nilai tugas, kuis, dan nilai akhir per mata pelajaran',
            'icon' => 'bi-journal-check',
            'color' => '#8b5cf6',
            'bg' => '#ede9fe',
        ],
        'teacher-activity' => [
            'title' => 'Aktivitas Guru',
            'description' => 'Materi, tugas, kuis, dan log aktivitas guru',
            'icon' => 'bi-activity',
            'color' => '#f59e0b',
            'bg' => '#fef3c7',
        ],
        'student-activity' => [
            'title' => 'Aktivitas Siswa',
            'description' => 'Pengumpulan tugas, kuis, presensi mandiri, dan aktivitas siswa',
            'icon' => 'bi-lightning-charge-fill',
            'color' => '#ec4899',
            'bg' => '#fce7f3',
        ],
        'learning' => [
            'title' => 'Pembelajaran',
            'description' => 'Ringkasan konten pembelajaran per kelas dan mata pelajaran',
            'icon' => 'bi-book-fill',
            'color' => '#0ea5e9',
            'bg' => '#e0f2fe',
        ],
    ],
];
