<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $namaLaporan }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #000;
            margin: 0;
            padding: 20mm;
        }

        .report-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .report-period {
            font-size: 12px;
            color: #666;
        }

        .report-content {
            margin-top: 20px;
        }

        .placeholder-text {
            font-style: italic;
            color: #999;
            text-align: center;
            padding: 60px;
            border: 2px dashed #ccc;
            margin-top: 40px;
        }

        @media print {
            body {
                font-size: 11px;
                padding: 10mm;
            }

            .no-print {
                display: none !important;
            }

            .report-header {
                margin-bottom: 20px;
                padding-bottom: 15px;
            }
        }

        .print-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            z-index: 9999;
        }

        .print-button:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="report-header">
        <div class="company-name">{{ $namaPerusahaan }}</div>
        <div class="report-title">{{ $namaLaporan }}</div>
        <div class="report-period">Periode: {{ $tanggalMulaiFormatted }} s/d {{ $tanggalAkhirFormatted }}</div>
    </div>

    <div class="report-content">
        <div class="placeholder-text">
            [ISI LAPORAN AKAN DITAMBAHKAN KEMUDIAN]
        </div>
    </div>

    <button class="print-button no-print" onclick="window.print();">
        Print / Save as PDF
    </button>
</body>
</html>
