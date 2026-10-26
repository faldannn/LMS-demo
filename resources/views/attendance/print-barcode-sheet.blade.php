<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Cetak Stiker Barcode - {{ $schoolName }}</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 10mm;
        }
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        }
        body {
            background-color: #f1f5f9;
            margin: 0;
            padding: 20px;
            color: #0f172a;
        }
        .no-print-bar {
            max-width: 210mm;
            margin: 0 auto 20px auto;
            background: #ffffff;
            padding: 14px 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .btn-print {
            background: #059669;
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: 700;
            font-size: 14px;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-print:hover {
            background: #047857;
        }
        .btn-close {
            background: #e2e8f0;
            color: #334155;
            border: none;
            padding: 10px 16px;
            font-weight: 600;
            font-size: 13px;
            border-radius: 8px;
            cursor: pointer;
        }
        .sheet-container {
            width: 190mm;
            margin: 0 auto;
            background: white;
            padding: 5mm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6mm 5mm;
        }
        .label-sticker {
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 8px 10px;
            text-align: center;
            background: white;
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 38mm;
            position: relative;
        }
        .school-header {
            font-size: 7.5pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #1e293b;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 2px;
            margin-bottom: 3px;
        }
        .student-name {
            font-size: 9pt;
            font-weight: 800;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 1px 0;
        }
        .student-sub {
            font-size: 7.5pt;
            color: #64748b;
            font-family: monospace;
            font-weight: 600;
        }
        .barcode-wrap {
            margin: 2px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .barcode-wrap svg {
            max-width: 100%;
            height: 32px;
        }
        .qr-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .sheet-container {
                box-shadow: none;
                padding: 0;
                width: 100%;
            }
            .label-sticker {
                border: 0.5px solid #94a3b8;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div>
            <strong>Lembar Cetak Stiker Barcode (Grid A4)</strong>
            <div style="font-size: 12px; color: #64748b;">Total: {{ count($items) }} Stiker | Siap cetak ke kertas label stiker A4</div>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.close()" class="btn-close">Tutup</button>
            <button onclick="window.print()" class="btn-print">
                <span>🖨️ Cetak Lembar Ini</span>
            </button>
        </div>
    </div>

    <div class="sheet-container">
        @foreach($items as $item)
            <div class="label-sticker">
                <div class="school-header">{{ $schoolName }}</div>
                <div class="student-name">{{ $item['name'] }}</div>
                <div class="student-sub">{{ $item['subtext'] }}</div>

                <div class="barcode-wrap">
                    <svg class="barcode-item" jsbarcode-format="CODE128" jsbarcode-value="{{ $item['code'] }}" jsbarcode-textmargin="0" jsbarcode-fontoptions="bold" jsbarcode-height="30" jsbarcode-width="1.3" jsbarcode-fontsize="10"></svg>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof JsBarcode === 'function') {
                JsBarcode(".barcode-item").init();
            }
        });
    </script>
</body>
</html>
