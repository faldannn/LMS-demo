<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1e293b; margin: 0; }
        .header { text-align: center; border-bottom: 2px solid var(--color-brand-500); padding-bottom: 10px; margin-bottom: 14px; }
        .school-name { font-size: 15px; font-weight: 700; color: var(--color-brand-500); }
        .school-info { font-size: 9px; color: #64748b; margin-top: 2px; }
        .doc-title { font-size: 12px; font-weight: 700; margin-top: 6px; text-transform: uppercase; }
        .doc-period { font-size: 10px; color: #64748b; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: var(--color-brand-600); color: #fff; padding: 6px 4px; font-size: 9px; text-align: left; }
        td { border-bottom: 1px solid #e2e8f0; padding: 5px 4px; font-size: 9px; }
        tr:nth-child(even) td { background: #f8fafc; }
        .footer { margin-top: 14px; font-size: 8px; color: #94a3b8; text-align: right; }
        .summary { margin: 10px 0; font-size: 9px; }
        .summary span { margin-right: 16px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">{{ $school['name'] ?? 'SMA Islam Assyafiiyah 02 Bekasi' }}</div>
        <div class="school-info">{{ $school['address'] ?? '' }}</div>
        <div class="doc-title">{{ $data['title'] }}</div>
        <div class="doc-period">{{ $filter->periodLabel() }}</div>
    </div>

    @if(!empty($data['summary']))
        <div class="summary">
            @foreach($data['summary'] as $label => $value)
                <span><strong>{{ ucwords(str_replace('_', ' ', $label)) }}:</strong>
                    {{ is_float($value) ? number_format($value, 1) : $value }}</span>
            @endforeach
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width:28px">#</th>
                @foreach($data['headings'] as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($data['rows'] as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    @foreach($data['keys'] as $key)
                        <td>{{ $row[$key] ?? '-' }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($data['headings']) + 1 }}" style="text-align:center;padding:20px;color:#94a3b8">
                        Tidak ada data
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dicetak: {{ $generated_at }}</div>
</body>
</html>
