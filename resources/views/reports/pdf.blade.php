<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan {{ $typeConfig['title'] }} — {{ $filter->periodLabel() }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1e293b; }
        h1   { font-size: 16px; margin-bottom: 4px; }
        p    { margin: 2px 0; color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th { background: #f1f5f9; text-align: left; padding: 6px 8px; border: 1px solid #e2e8f0; font-size: 11px; }
        td { padding: 6px 8px; border: 1px solid #e2e8f0; }
        tr:nth-child(even) td { background: #f8fafc; }
    </style>
</head>
<body>
    <h1>Laporan: {{ $typeConfig['title'] }}</h1>
    <p>Periode: {{ $filter->periodLabel() }}</p>
    <p>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }}</p>

    @if($data['rows']->isEmpty())
        <p style="margin-top:24px;color:#94a3b8">Tidak ada data untuk periode ini.</p>
    @else
        <table>
            <thead>
                <tr>
                    @foreach(array_keys($data['rows']->first()) as $col)
                        <th>{{ ucwords(str_replace('_', ' ', $col)) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($data['rows'] as $row)
                    <tr>
                        @foreach($row as $val)
                            <td>{{ $val }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
