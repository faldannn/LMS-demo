<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'BIODATA_PRIBADIKELAS.xlsx';
if (!file_exists($file)) {
    echo "File not found: $file\n";
    exit(1);
}

$spreadsheet = IOFactory::load($file);

foreach ($spreadsheet->getSheetNames() as $sheetName) {
    echo "=== Sheet: $sheetName ===\n";
    $worksheet = $spreadsheet->getSheetByName($sheetName);
    $highestRow = min(20, $worksheet->getHighestRow());
    $highestCol = $worksheet->getHighestColumn();

    for ($r = 1; $r <= $highestRow; $r++) {
        $row = [];
        $colIndex = 0;
        foreach ($worksheet->getRowIterator($r, $r)->current()->getCellIterator() as $cell) {
            $val = $cell->getCalculatedValue();
            if ($val !== null && $val !== '') {
                $row[] = $cell->getColumn() . $r . ": " . trim((string)$val);
            }
            if (++$colIndex > 15) break;
        }
        if (!empty($row)) {
            echo "Row $r: " . implode(' | ', $row) . "\n";
        }
    }
}
