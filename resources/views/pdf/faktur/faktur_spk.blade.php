<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Kwitansi SPK</title>
    <style>
        body {
            font-size: x-small;
            margin: 0px
        }

        @page {
            margin-top: 1cm;
            margin-bottom: 1cm;
            margin-left: 1cm;
            margin-right: 1cm;
        }

        .row {
            overflow: auto;
        }

        .left {
            float: left;
            width: 50%;
        }

        .right {
            float: right;
            width: 50%;
        }

        .page-break {
            page-break-after: always;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            /* margin-bottom: 20px; */
        }

        .table,
        .th,
        .td {
            border: 1px solid #d4d4d4;
        }

        .th,
        .td {
            padding: 8px;
            text-align: center;
        }

        .th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body style="font-family: Arial, Helvetica, sans-serif;">
    <div>
        <h1>SURAT PEMESANAN KENDARAAN</h1>


    </div>


    {{-- detail surat jalan --}}
    <div></div>
    {{-- 
    <div>
        <table class="table" style="width:100%">
            <thead>
                <tr>
                    <th class="th">Model</th>
                    <th class="th">Warna</th>
                    <th class="th">Rangka</th>
                    <th class="th">No. Mesin</th>
                </tr>

            </thead>
            <tbody>
                @foreach ($data->delivery_event_return->event_return->event_return_unit as $item)
                    <tr>
                        <td class="td">{{ $item->event_list_unit->unit->motor->motor_name }}</td>
                        <td class="td">{{ $item->event_list_unit->unit->color->color_name }}</td>
                        <td class="td">{{ $item->event_list_unit->unit->unit_frame }}</td>
                        <td class="td">{{ $item->event_list_unit->unit->unit_engine }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div> --}}



    <div style="display: flex;justify-content:space-between;margin-top:20px">
        <table class=" table">
            <tr>
                <td class="td">
                    <div style="height: 150px">
                        Diserahkan Oleh:
                    </div>
                </td>
                <td class="td">
                    <div style="height: 150px">
                        Diserahkan Oleh:
                    </div>
                </td>
                <td class="td">
                    <div style="height: 150px">
                        Diserahkan Oleh:
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
