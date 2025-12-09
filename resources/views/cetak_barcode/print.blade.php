<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Preview Barcode</title>

    <style>
        /* Page dimensions (mm) - dikontrol dari controller via $pageWidth & $pageHeight */
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

        .page {
            box-sizing: border-box;
            width: {{ $pageWidth ?? 100 }}mm;
            height: {{ $pageHeight ?? 15 }}mm;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 2mm;
            /* <--- TAMBAHKAN INI */
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

        /* Lebar cell dihitung otomatis berdasarkan kolom */
        .cell {
            width: calc({{ $pageWidth ?? 100 }}mm / {{ $columns ?? 3 }});
            height: {{ $pageHeight ?? 15 }}mm;
            padding: 0 2mm;
            box-sizing: border-box;
            text-align: center;
            vertical-align: top;
            display: inline-block;
        }

        .label-wrapper {
            display: block;
        }

        .name {
            font-size: 6px;
            font-weight: 1000;
            margin: 0 0 1px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-transform: uppercase;
        }

        /* pastikan container barcode menggunakan box-sizing */
        .barcode {
            width: calc(100% + 4mm);
            padding: 0;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* override atribut width di SVG (paksa responsive) */
        .barcode svg {
            width: 100% !important;
            /* paksa svg mengikuti container */
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
            font-weight: 1000;
            text-align: center;
        }

        /* Print adjustments */
        @media print {

            html,
            body {
                width: {{ $pageWidth ?? 100 }}mm;
                height: {{ $pageHeight ?? 15 }}mm;
            }

            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>
    {{-- Safety: pastikan $chunks valid dan berisi array of rows --}}
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
                    @for ($i = count($row); $i < ($columns ?? 3); $i++)
                        <div class="cell"></div>
                    @endfor
                </div>
            @endif
        @endforeach
    @else
        {{-- Jika kosong, tampilkan pesan ringan --}}
        <div style="padding:12px; font-family: Arial, sans-serif;">
            <strong>Tidak ada label untuk ditampilkan.</strong>
        </div>
    @endif
</body>

</html>
