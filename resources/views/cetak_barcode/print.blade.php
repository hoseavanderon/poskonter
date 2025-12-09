<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Preview Barcode</title>

    <style>
        /* PAGE SIZE DIKONTROL DARI CONTROLLER via $pageWidth & $pageHeight */
        @page {
            size: {{ $pageWidth ?? 100 }}mm {{ $pageHeight ?? 15 }}mm;
            margin: 0;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: {{ $pageWidth ?? 100 }}mm;
            height: {{ $pageHeight ?? 15 }}mm;
            font-family: Arial, sans-serif;
            -webkit-print-color-adjust: exact;
        }

        /* ========== CONFIG (ubah angka di bawah jika perlu) ========== */
        /* gap antar kolom dalam mm */
        /* padding horizontal per cell dalam mm (left+right = 2 * padMm) */
    </style>

    {{-- Hitung nilai CSS dinamis dengan PHP supaya total width selalu pas --}}
    @php
        $pw = $pageWidth ?? 100; // page width mm
        $ph = $pageHeight ?? 15; // page height mm
        $cols = $columns ?? 3; // jumlah kolom
        $gapMm = 2; // gap antar cell (mm) - tweak di sini
        $padMm = 2; // padding kiri & kanan per cell (mm) - tweak di sini

        // Total gap dan available width untuk semua cell
        $totalGap = ($cols - 1) * $gapMm; // mm
        $availableForCells = max(0, $pw - $totalGap); // mm
        // width per cell termasuk padding
        // kita akan menetapkan width cell = availableForCells/cols
        // isi internal (content) = cellWidth - 2*padMm
        $cellWidthMm = $availableForCells / $cols;
        $cellContentWidthMm = max(0, $cellWidthMm - 2 * $padMm);
    @endphp

    <style>
        /* ========== LAYOUT ========== */
        .page {
            box-sizing: border-box;
            width: {{ $pw }}mm;
            height: {{ $ph }}mm;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            /* center group of cells supaya kiri/kanan simetris */
            gap: {{ $gapMm }}mm;
            /* gap antar kolom */
            page-break-after: always;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        /* Hindari pemecahan block saat print */
        .page,
        .cell {
            -webkit-page-break-inside: avoid;
            page-break-inside: avoid;
        }

        /* Lebar cell sudah dihitung agar jumlah total = pageWidth */
        .cell {
            width: calc(({{ $pw }}mm - {{ $totalGap }}mm) / {{ $cols }});
            height: {{ $ph }}mm;
            padding: 0 {{ $padMm }}mm;
            /* ruang kiri/kanan di dalam cell */
            box-sizing: border-box;
            text-align: center;
            vertical-align: top;
            display: inline-block;
        }

        .cell:last-child .barcode {
            transform: translateX(4mm);
        }

        .label-wrapper {
            display: block;
            padding-top: 1.5mm;
        }

        .name {
            font-size: 6px;
            font-weight: 700;
            margin: 0 0 1px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-transform: uppercase;
        }

        /* Barcode container: isi sesuai lebar konten cell (tidak melebihi cell) */
        .barcode {
            width: 100%;
            max-width: {{ $cellContentWidthMm }}mm;
            /* pastikan barcode tidak melewati content area */
            box-sizing: border-box;
            padding: 0;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Paksa SVG responsif supaya ikut menyesuaikan ukuran .barcode */
        .barcode svg {
            width: 100% !important;
            height: auto !important;
            max-width: 100% !important;
            display: block;
            margin: 0 auto;
            shape-rendering: crispEdges;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: -moz-crisp-edges;
            image-rendering: pixelated;
        }

        .price {
            font-size: 6px;
            font-weight: 700;
            text-align: center;
            margin-top: 1px;
        }

        /* Print adjustments */
        @media print {

            html,
            body {
                width: {{ $pw }}mm;
                height: {{ $ph }}mm;
            }

            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>
    {{-- Pastikan $chunks valid & berisi array of rows --}}
    @if (!empty($chunks) && is_array($chunks))
        @foreach ($chunks as $row)
            @if (is_array($row) && count($row) > 0)
                <div class="page">
                    @foreach ($row as $item)
                        <div class="cell">
                            <div class="label-wrapper">
                                <div class="name">{{ Str::limit($item->name ?? '', 40) }}</div>
                                <div class="barcode">{!! $item->barcode_svg ?? '' !!}</div>
                                <div class="price">Rp {{ number_format($item->jual ?? 0, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Fill empty cells supaya layout tetap rapi --}}
                    @for ($i = count($row); $i < ($cols ?? 3); $i++)
                        <div class="cell"></div>
                    @endfor
                </div>
            @endif
        @endforeach
    @else
        <div style="padding:12px; font-family: Arial, sans-serif;">
            <strong>Tidak ada label untuk ditampilkan.</strong>
        </div>
    @endif
</body>

</html>
