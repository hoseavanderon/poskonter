<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Preview Barcode</title>
    <style>
        @page {
            size: 62mm 15mm;
            margin: 0;
        }

        html,
        body {
            width: 62mm;
            height: 15mm;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            -webkit-print-color-adjust: exact !important;
            transform: translateX(-0.8mm);
            /* ⬅️ geser semua konten sedikit ke kiri */
        }

        table {
            width: 62mm;
            border-collapse: collapse;
            table-layout: fixed;
            margin: 0;
            padding: 0;
        }

        td {
            width: 30mm;
            height: 15mm;
            text-align: center;
            vertical-align: middle;
            padding: 0;
        }

        /* simulasi gap tengah (2mm total, masing2 1mm kiri kanan) */
        td:nth-child(1) {
            padding-right: 1mm;
        }

        td:nth-child(2) {
            padding-left: 1mm;
        }

        .name {
            font-size: 6px;
            font-weight: 700;
            margin-top: 2px;
            font-family: 'DejaVu Sans', 'Arial Black', 'Liberation Sans', sans-serif;
            text-transform: uppercase;
            -webkit-font-smoothing: none;
            text-rendering: geometricPrecision;
            image-rendering: pixelated;
        }

        .barcode {
            width: 25mm;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .price {
            font-size: 6px;
            font-weight: 800;
            margin: 0;
            font-family: 'DejaVu Sans', 'Arial Black', 'Liberation Sans', sans-serif;
            -webkit-font-smoothing: none;
            text-rendering: geometricPrecision;
            image-rendering: pixelated;
        }
    </style>

</head>

<body>
    @foreach ($chunks as $pair)
        <div class="page">
            <table>
                <tr>
                    @foreach ($pair as $item)
                        <td>
                            <div class="name">{{ $item->name }}</div>
                            <div class="barcode">{!! $item->barcode_svg !!}</div>
                            <div class="price">Rp {{ number_format($item->jual, 0, ',', '.') }}</div>
                        </td>
                    @endforeach
                    @if (count($pair) < 2)
                        <td></td>
                    @endif
                </tr>
            </table>
        </div>
    @endforeach
</body>

</html>
