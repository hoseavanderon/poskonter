<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Preview Barcode</title>

    <style>
        @page {
            size: 100mm 15mm;
            margin: 0;
        }

        html,
        body {
            width: 100mm;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        table {
            width: 100mm;
            border-collapse: collapse;
            table-layout: fixed;
        }

        tr {
            height: 15mm;
        }

        td {
            width: 33mm;
            padding: 0;
            vertical-align: top;
            text-align: center;
        }

        .label-wrapper {
            padding-top: 2mm;
            /* ⬅️ NAIKIN JARAK DARI ATAS */
            display: block;
        }

        .name {
            font-size: 6px;
            font-weight: 700;
            margin: 0 0 1px 0;
            text-align: center;
            white-space: nowrap;
        }

        .barcode {
            width: 28mm;
            margin: 0 auto;
            display: block;
        }

        .price {
            font-size: 6px;
            font-weight: 800;
            margin-top: 1px;
            text-align: center;
        }
    </style>




</head>

<body>
    @foreach ($chunks as $row)
        <table>
            <tr>
                @foreach ($row as $item)
                    <td>
                        <div class="label-wrapper">
                            <div class="name">{{ Str::limit($item->name, 20) }}</div>
                            <div class="barcode">{!! $item->barcode_svg !!}</div>
                            <div class="price">Rp {{ number_format($item->jual, 0, ',', '.') }}</div>
                        </div>
                    </td>
                @endforeach

                @for ($i = count($row); $i < 3; $i++)
                    <td></td>
                @endfor
            </tr>
        </table>
    @endforeach
</body>



</html>
