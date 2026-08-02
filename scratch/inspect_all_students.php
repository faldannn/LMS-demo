<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'BIODATA_PRIBADIKELAS.xlsx';
$spreadsheet = IOFactory::load($file);

$sheet = $spreadsheet->getActiveSheet();
$highestRow = $sheet->getHighestRow();

echo "Total rows in sheet: $highestRow\n";

$classes = [];
$students = [];

for ($r = 2; $r <= $highestRow; $r++) {
    $email = trim((string)$sheet->getCell("A$r")->getValue());
    $nama = trim((string)$sheet->getCell("B$r")->getValue());
    $email2 = trim((string)$sheet->getCell("C$r")->getValue());
    $nisn = trim((string)$sheet->getCell("D$r")->getValue());
    $ttl = trim((string)$sheet->getCell("E$r")->getValue());
    $kelas = trim((string)$sheet->getCell("F$r")->getValue());

    if (!empty($nama) || !empty($nisn)) {
        $classes[$kelas] = ($classes[$kelas] ?? 0) + 1;
        $students[] = [
            'nama' => $nama,
            'nisn' => $nisn,
            'email' => $email2 ?: $email,
            'ttl' => $ttl,
            'kelas' => $kelas,
        ];
    }
}

echo "Total valid student rows: " . count($students) . "\n";
echo "Classes found in Excel:\n";
print_r($classes);
