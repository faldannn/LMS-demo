<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h2 { color: var(--color-brand-500); margin-bottom: 4px; }
        .meta { color: #666; font-size: 10px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: var(--color-brand-500); color: #fff; padding: 6px 8px; text-align: left; }
        td { border-bottom: 1px solid #e5e7eb; padding: 5px 8px; }
        tr:nth-child(even) td { background: #f9fafb; }
    </style>
</head>
<body>
    <h2>{{ $title }}</h2>
    <div class="meta">Dicetak: {{ now()->format('d/m/Y H:i') }} · Total: {{ $items->count() }} data</div>
    <table>
        <thead>
            <tr>
                @foreach($columns as $col)
                    <th>{{ $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($items as $i => $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
