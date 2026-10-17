<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ 'Laporan-Keuangan-' . $params['jenis'] . '-' . str_replace('-', '', $params['tanggal_mulai']) . '-' . str_replace('-', '', $params['tanggal_akhir']) }}</title>
    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        @page {
            size: A4 {{ $orientasiHalaman }};
            margin: 12mm;
        }

        body {
            margin: 0;
            padding: 16px;
            background: #f1f5f9;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }

        .laporan {
            width: 100%;
            max-width: 100%;
            margin: 0 auto 24px;
            padding: 12mm;
            background: #fff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.15);
        }

        /* Baris 1-3 sheet Excel: nama perusahaan, judul, periode */
        .header-laporan {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 2px solid #333;
        }

        .nama-perusahaan {
            font-size: 16px;
            font-weight: bold;
        }

        .judul-laporan {
            margin-top: 4px;
            font-size: 14px;
            font-weight: bold;
        }

        .periode-laporan {
            margin-top: 4px;
            font-size: 11px;
            font-style: italic;
            color: #595959;
        }

        .tabel-laporan {
            width: 100%;
            margin-bottom: 16px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .tabel-laporan th,
        .tabel-laporan td {
            padding: 4px 8px;
            border: none;
            vertical-align: top;
            word-wrap: break-word;
        }

        /* Judul kolom: tebal, background #44546A, border tipis (sama seperti Excel) */
        .tabel-laporan thead th {
            padding: 6px 8px;
            border: 1px solid #808080;
            background: #44546A;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }

        .kolom-uraian {
            width: 70%;
            white-space: pre-wrap;
        }

        .kolom-nilai {
            width: 20%;
            text-align: right;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }

        .kolom-persen {
            width: 10%;
            text-align: right;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }

        /* Section (ASET, PENDAPATAN, dst.): tebal + background #D9E2F3 */
        .baris-section td {
            border: 1px solid #BFBFBF;
            background: #D9E2F3;
            font-weight: bold;
        }

        /* Baris subtotal/total: tebal + border atas */
        .baris-total td {
            font-weight: bold;
            border-top: 1px solid #404040;
        }

        /* Catatan keterangan: italic kecil abu-abu tanpa border */
        .baris-note td {
            padding-left: 0;
            font-size: 9px;
            font-style: italic;
            color: #7F7F7F;
        }

        /* Jarak antar bagian, sama seperti baris kosong pada sheet Excel */
        .baris-kosong td {
            height: 14px;
        }

        .toolbar {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 9999;
        }

        .toolbar button {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            background: #2563eb;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .toolbar button:hover {
            background: #1d4ed8;
        }

        @media print {
            body {
                padding: 0;
                background: #fff;
                font-size: 11px;
            }

            .no-print {
                display: none !important;
            }

            .laporan {
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            .laporan + .laporan {
                page-break-before: always;
            }

            /* Judul kolom diulang pada tiap halaman cetak */
            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .tabel-laporan {
                page-break-inside: auto;
            }
        }
    </style>
</head>
<body>
    @foreach ($laporan as $item)
        <section class="laporan" data-jenis="{{ $item['jenis'] }}">
            <header class="header-laporan">
                <div class="nama-perusahaan">{{ $item['nama_perusahaan'] }}</div>
                <div class="judul-laporan">{{ $item['judul'] }}</div>
                <div class="periode-laporan">{{ $item['periode'] }}</div>
            </header>

            @foreach ($item['tabel'] as $tabel)
                <table class="tabel-laporan">
                    @if ($tabel['header'] !== [])
                        <thead>
                            <tr>
                                @foreach ($tabel['header'] as $kepalaKolom)
                                    <th>{{ $kepalaKolom }}</th>
                                @endforeach
                                @for ($sisa = count($tabel['header']); $sisa < $item['kolom']; $sisa++)
                                    <th></th>
                                @endfor
                            </tr>
                        </thead>
                    @endif
                    <tbody>
                        @foreach ($tabel['baris'] as $baris)
                            @switch($baris['tipe'])
                                @case('kosong')
                                    <tr class="baris-kosong">
                                        <td colspan="{{ $item['kolom'] }}"></td>
                                    </tr>
                                    @break

                                @case('section')
                                    <tr class="baris-section">
                                        <td colspan="{{ $item['kolom'] }}"><strong>{{ $baris['teks'] }}</strong></td>
                                    </tr>
                                    @break

                                @case('note')
                                    <tr class="baris-note">
                                        <td colspan="{{ $item['kolom'] }}"><em>{{ $baris['teks'] }}</em></td>
                                    </tr>
                                    @break

                                @default
                                    <tr @class(['baris-total' => $baris['total'] ?? false])>
                                        <td class="kolom-uraian">@if ($baris['tebal'] ?? false)<strong>{{ $baris['label'] ?? '' }}</strong>@else{{ $baris['label'] ?? '' }}@endif</td>
                                        <td class="kolom-nilai">{{ format_nilai_laporan($baris['nilai'] ?? null) }}</td>
                                        @if ($item['kolom'] >= 3)
                                            <td class="kolom-persen">{{ format_persen_laporan($baris['persen'] ?? null) }}</td>
                                        @endif
                                    </tr>
                            @endswitch
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        </section>
    @endforeach

    <div class="toolbar no-print">
        <button type="button" onclick="window.print();">Print / Simpan PDF</button>
    </div>
</body>
</html>
