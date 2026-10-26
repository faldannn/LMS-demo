<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Pelajar Digital - {{ $schoolName }}</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
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
        .cards-grid {
            width: 190mm;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8mm 6mm;
        }
        .id-card {
            width: 90mm;
            height: 57mm;
            background: #ffffff;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
            page-break-inside: avoid;
        }
        .card-header {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: white;
            padding: 6px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-header-title {
            font-size: 8pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.1;
        }
        .card-header-sub {
            font-size: 6pt;
            opacity: 0.85;
            font-mono;
        }
        .card-body {
            padding: 8px 10px;
            display: flex;
            gap: 10px;
            flex: 1;
        }
        .card-photo-wrap {
            width: 25mm;
            text-align: center;
            flex-shrink: 0;
        }
        .card-photo {
            width: 23mm;
            height: 29mm;
            object-fit: cover;
            border-radius: 6px;
            border: 1.5px solid #2563eb;
        }
        .card-info {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .info-name {
            font-size: 9.5pt;
            font-weight: 900;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
        }
        .info-row {
            font-size: 7pt;
            color: #475569;
            margin: 1px 0;
        }
        .info-label {
            font-weight: 700;
            color: #1e293b;
            display: inline-block;
            width: 14mm;
        }
        .card-barcode-area {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 4px 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .barcode-svg-card {
            height: 22px;
            max-width: 58mm;
        }
        .card-qr-box {
            width: 12mm;
            height: 12mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-qr-box svg {
            width: 100% !important;
            height: 100% !important;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .cards-grid {
                width: 100%;
            }
            .id-card {
                box-shadow: none;
                border: 0.5px solid #64748b;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div>
            <strong>Cetak Kartu Pelajar Digital Resmi</strong>
            <div style="font-size: 12px; color: #64748b;">Total: {{ $students->count() }} Kartu | Format CR80 Standar Pelajar</div>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.close()" class="btn-close">Tutup</button>
            <button onclick="window.print()" class="btn-print">
                <span>🖨️ Cetak Semua Kartu</span>
            </button>
        </div>
    </div>

    <div class="cards-grid">
        @foreach($students as $st)
            <div class="id-card">
                
                {{-- Header --}}
                <div class="card-header">
                    <div>
                        <div class="card-header-title">{{ $schoolName }}</div>
                        <div class="card-header-sub">KARTU TANDA PELAJAR DIGITAL</div>
                    </div>
                    <div style="font-size: 6.5pt; font-weight: bold; background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 4px;">
                        {{ $academicYear }}
                    </div>
                </div>

                {{-- Body --}}
                <div class="card-body">
                    <div class="card-photo-wrap">
                        <img src="{{ $st->photo_url }}" class="card-photo" alt="{{ $st->name }}">
                    </div>
                    <div class="card-info">
                        <div>
                            <div class="info-name">{{ $st->name }}</div>
                            <div class="info-row"><span class="info-label">NIS</span>: {{ $st->nis }}</div>
                            <div class="info-row"><span class="info-label">NISN</span>: {{ $st->nisn ?: '-' }}</div>
                            <div class="info-row"><span class="info-label">Kelas</span>: {{ $st->class?->name ?? 'Tanpa Kelas' }}</div>
                            <div class="info-row"><span class="info-label">Gender</span>: {{ $st->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Bottom Barcode Area --}}
                <div class="card-barcode-area">
                    <div style="display: flex; flex-direction: column;">
                        <svg class="barcode-card" jsbarcode-format="CODE128" jsbarcode-value="LMS-{{ $st->nis }}" jsbarcode-textmargin="0" jsbarcode-fontoptions="bold" jsbarcode-height="20" jsbarcode-width="1.1" jsbarcode-fontsize="8"></svg>
                    </div>
                    <div class="card-qr-box">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(46)->generate('LMS-' . $st->nis) !!}
                    </div>
                </div>

            </div>
        @endforeach
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof JsBarcode === 'function') {
                JsBarcode(".barcode-card").init();
            }
        });
    </script>
</body>
</html>
