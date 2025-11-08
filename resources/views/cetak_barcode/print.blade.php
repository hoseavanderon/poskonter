<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Preview Barcode</title>
    <style>
        @page {
            size: 62mm 20mm;
            margin: 0;
        }

        html,
        body {
            width: 62mm;
            height: 20mm;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            -webkit-print-color-adjust: exact !important;
            transform: translateX(-0.6mm);
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
            height: 20mm;
            text-align: center;
            vertical-align: middle;
            padding: 0;
        }

        td:nth-child(1) {
            padding-right: 1mm;
        }

        td:nth-child(2) {
            padding-left: 1mm;
        }

        .name {
            font-size: 5.8px;
            font-weight: 700;
            margin-top: 1px;
            text-transform: uppercase;
            line-height: 1.1;
            font-family: 'DejaVu Sans', 'Arial Black', sans-serif;
        }

        .barcode {
            width: 27mm;
            /* pas di tengah tapi masih bisa discan */
            height: 7.5mm;
            display: block;
            margin: 0.5mm auto 0 auto;
        }

        .barcode svg {
            width: 100% !important;
            height: 100% !important;
        }

        .price {
            font-size: 6px;
            font-weight: 800;
            margin-top: 0.5mm;
            font-family: 'DejaVu Sans', 'Arial Black', sans-serif;
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
                            <div class="name">{{ Str::limit($item->name, 20) }}</div>
                            <div class="barcode">
                                {!! $item->barcode_svg !!}
                            </div>
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
