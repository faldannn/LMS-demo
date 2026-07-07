{{-- resources/views/reports/pdf-template.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid var(--color-brand-500);
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .school-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--color-brand-500);
        }

        .school-info {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }

        .doc-title {
            font-size: 13px;
            font-weight: 700;
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .doc-period {
            font-size: 11px;
            color: #64748b;
        }

        .summary-row {
            display: flex;
            gap: 12px;
            margin-bottom: 14px;
        }

        .sum-box {
            flex: 1;
            text-align: center;
            padding: 8px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }

        .sum-val {
            font-size: 18px;
            font-weight: 800;
        }

        .sum-lbl {
            font-size: 9px;
            text-transform: uppercase;
            color: #64748b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: var(--color-brand-600);
            color: #fff;
        }

        th {
            padding: 7px 10px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
        }

        td {
            padding: 6px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10.5px;
        }

        tr:nth-child(even) td {
            background: #f8faff;
        }

        .pct-good {
            color: #065f46;
            font-weight: 700;
        }

        .pct-warning {
            color: #92400e;
            font-weight: 700;
        }

        .pct-bad {
            color: #991b1b;
            font-weight: 700;
        }

        .footer-txt {
            font-size: 9px;
            color: #94a3b8;
            margin-top: 16px;
            text-align: right;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="school-name">{{ $school['name'] ?? config('school.name') }}</div>
        <div class="school-info">
            {{ $school['address'] ?? '' }} | Telp: {{ $school['phone'] ?? '' }}<br>
            NPSN: {{ $school['npsn'] ?? '' }} | Email: {{ $school['email'] ?? '' }}
        </div>
        <div class="doc-title">Rekap Absensi Siswa</div>
        <div class="doc-period">
            Kelas: {{ $class?->name ?? 'Semua Kelas' }} |
            Periode: {{ \Carbon\Carbon::create(null, $month)->translatedFormat('F') }} {{ $year }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Hadir</th>
                <th>Sakit</th>
                <th>Izin</th>
                <th>Alfa</th>
                <th>Terlambat</th>
                <th>Total HE</th>
                <th>% Hadir</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recap as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row['student']->nis }}</td>
                    <td><strong>{{ $row['student']->name }}</strong></td>
                    <td>{{ $row['hadir'] }}</td>
                    <td>{{ $row['sakit'] }}</td>
                    <td>{{ $row['izin'] }}</td>
                    <td>{{ $row['alfa'] }}</td>
                    <td>{{ $row['terlambat'] }}</td>
                    <td>{{ $row['total_he'] }}</td>
                    <td
                        class="{{ $row['percentage'] >= 90 ? 'pct-good' : ($row['percentage'] >= 75 ? 'pct-warning' : 'pct-bad') }}">
                        {{ $row['percentage'] }}%
                    </td>
                    <td>
                        @if($row['percentage'] < 75) ❌ TMS
                        @elseif($row['percentage'] < 85) ⚠️ Cukup
                        @else ✅ Baik
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:24px;display:flex;justify-content:flex-end">
        <div style="text-align:center;width:200px">
            <div>Bandung, {{ now()->translatedFormat('d F Y') }}</div>
            <div style="font-weight:700;margin-top:4px">Kepala Sekolah</div>
            <div style="margin:40px 0 4px">&nbsp;</div>
            <div style="border-top:1px solid #000;padding-top:4px;font-weight:700">
                {{ $school['principal'] ?? '' }}
            </div>
        </div>
    </div>

    <div class="footer-txt">
        Dicetak oleh LMS / E-Learning pada {{ $generated_at }} | {{ $school['name'] ?? '' }}
    </div>

</body>

</html>