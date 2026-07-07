<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Raport - {{ $reportCard->student->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.5; color: #333; }
        .header { text-align: center; border-bottom: 3px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .school-name { font-size: 24px; font-weight: bold; margin: 0; text-transform: uppercase; }
        .school-address { font-size: 12px; margin: 5px 0 0; }
        .title { text-align: center; font-size: 18px; font-weight: bold; margin: 20px 0; text-decoration: underline; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 4px; vertical-align: top; }
        .info-table .label { width: 150px; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #333; padding: 8px; }
        .data-table th { background-color: #f0f0f0; text-align: center; }
        .text-center { text-align: center; }
        .footer { margin-top: 50px; width: 100%; }
        .signature-box { float: right; width: 250px; text-align: center; }
        .signature-space { height: 80px; }
        .clear { clear: both; }
        
        @media print {
            body { font-size: 12pt; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer; background: var(--color-brand-500); color: white; border: none; border-radius: 5px;">Cetak Raport</button>
    </div>

    <div class="header">
        <h1 class="school-name">{{ config('school.name', 'SMA JAKARTA') }}</h1>
        <p class="school-address">Laporan Hasil Belajar Peserta Didik</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Peserta Didik</td>
            <td>: <strong>{{ $reportCard->student->name }}</strong></td>
            <td class="label">Kelas</td>
            <td>: {{ $reportCard->student->class->name }}</td>
        </tr>
        <tr>
            <td class="label">NIS / NISN</td>
            <td>: {{ $reportCard->student->nis }} / {{ $reportCard->student->nisn }}</td>
            <td class="label">Semester</td>
            <td style="text-transform: capitalize;">: {{ $reportCard->semester }}</td>
        </tr>
        <tr>
            <td class="label">Nama Sekolah</td>
            <td>: {{ config('school.name') }}</td>
            <td class="label">Tahun Pelajaran</td>
            <td>: {{ $reportCard->academicYear->name }}</td>
        </tr>
    </table>

    <div class="title">LAPORAN HASIL BELAJAR</div>

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 50px;">No</th>
                <th rowspan="2">Mata Pelajaran</th>
                <th colspan="3">Nilai Pengetahuan & Keterampilan</th>
            </tr>
            <tr>
                <th style="width: 100px;">Nilai Akhir</th>
                <th style="width: 100px;">Predikat</th>
                <th style="width: 250px;">Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportCard->details as $index => $detail)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $detail->subject->name }}</td>
                <td class="text-center fw-bold">{{ (float)$detail->final_score }}</td>
                <td class="text-center">{{ $detail->grade }}</td>
                <td style="font-size: 11px;">
                    @if($detail->grade === 'A')
                        Sangat baik dalam memahami materi dan sangat terampil dalam praktik.
                    @elseif($detail->grade === 'B')
                        Baik dalam memahami materi dan terampil dalam praktik.
                    @elseif($detail->grade === 'C')
                        Cukup memahami materi dan cukup terampil, perlu peningkatan.
                    @else
                        Kurang memahami materi dan perlu bimbingan intensif.
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="data-table" style="width: 50%;">
        <thead>
            <tr>
                <th colspan="2">Ketidakhadiran</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Sakit</td>
                <td class="text-center">{{ $reportCard->attendance_sakit }} hari</td>
            </tr>
            <tr>
                <td>Izin</td>
                <td class="text-center">{{ $reportCard->attendance_izin }} hari</td>
            </tr>
            <tr>
                <td>Tanpa Keterangan</td>
                <td class="text-center">{{ $reportCard->attendance_alfa }} hari</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <p>Jakarta, {{ date('d F Y') }}<br>Wali Kelas</p>
            <div class="signature-space"></div>
            <p style="text-decoration: underline; margin-bottom: 0;"><strong>_________________________</strong></p>
            <p style="margin-top: 5px;">NIP. -</p>
        </div>
        <div class="clear"></div>
    </div>
</body>
</html>
