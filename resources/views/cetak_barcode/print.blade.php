<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Preview Barcode</title>

    <style>
        /* Page size: tiap page = 100mm x 15mm */
        @page {
            size: 100mm 15mm;
            margin: 0;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100mm;
            height: 15mm;
            font-family: Arial, sans-serif;
            -webkit-print-color-adjust: exact;
        }

        /* Each row becomes a page */
        .page {
            box-sizing: border-box;
            width: 100mm;
            height: 15mm;
            display: flex;
            align-items: flex-start;
            /* align top inside the 15mm */
            justify-content: space-between;
            page-break-after: always;
            /* force page break after each row */
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        /* Prevent browsers from splitting this block across pages */
        .page,
        .cell {
            -webkit-page-break-inside: avoid;
            page-break-inside: avoid;
        }

        .cell {
            width: 33mm;
            height: 15mm;
            padding: 0;
            box-sizing: border-box;
            text-align: center;
            vertical-align: top;
            display: inline-block;
        }

        .label-wrapper {
            padding-top: 2mm;
            /* jarak dari atas */
            display: block;
        }

        .name {
            font-size: 6px;
            font-weight: 700;
            margin: 0 0 1px 0;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .barcode {
            width: 28mm;
            margin: 0 auto;
            display: block;
            line-height: 1;
        }

        .price {
            font-size: 6px;
            font-weight: 800;
            margin-top: 1px;
            text-align: center;
        }

        /* Print-specific hints */
        @media print {

            html,
            body {
                width: 100mm;
                height: 15mm;
            }

            /* Remove any default margins from printing */
            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>
    @foreach ($chunks as $row)
        <div class="page">
            @foreach ($row as $item)
                <div class="cell">
                    <div class="label-wrapper">
                        <div class="name">{{ Str::limit($item->name, 20) }}</div>
                        <div class="barcode">{!! $item->barcode_svg !!}</div>
                        <div class="price">Rp {{ number_format($item->jual, 0, ',', '.') }}</div>
                    </div>
                </div>
            @endforeach

            {{-- fill empty cells so layout tetap 3 kolom --}}
            @for ($i = count($row); $i < 3; $i++)
                <div class="cell"></div>
            @endfor
        </div>
    @endforeach
</body>

</html>
