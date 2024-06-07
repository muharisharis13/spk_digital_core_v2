<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Surat Jalan Repair</title>
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
        <table style="width: 100%">
            <tr>
                <td>
                    <div class="left2" style="height: 180px">
                        <div>
                            <img src="logo/alfa-scorpii-logo.png" alt="Company Logo" class="logo" width="100">
                        </div>

                        <div>
                            <h3>{{ $data->dealer->dealer_name }}</h3>
                            <div class="alamat">
                                {{ $data->dealer->dealer_address }}
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="right2" style="text-align: right;height:180px">
                        <h2>Surat Jalan</h2>
                        <div>No.{{ $data->delivery_number }}</div>
                        <div style="text-align: right;margin-top:20px">
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::size(100)->generate('https://google.com')) !!} ">

                        </div>
                        <div style="font-weight:bold;margin-top:20px">
                            {{ date('d M Y', strtotime($data->created_at)) }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>



    </div>


    {{-- detail surat jalan --}}
    <div>
        <table style="width: 100%">
            <tr>
                <td>
                    <div style="width:100%; height:180px">
                        <h2>DETAIL PENGIRIMAN</h2>
                        <div>
                            <table style="width: 100%;page-break-inside: auto;">

                                <tr>
                                    <td>
                                        Nama Driver
                                    </td>
                                    <td>
                                        : {{ $data->delivery_driver_name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Kendaraan
                                    </td>
                                    <td>
                                        : {{ $data->delivery_vehicle }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Penerima
                                    </td>
                                    <td>
                                        :
                                        {{ $data->delivery_repair->repair->dealer->dealer_name ?? $data->delivery_repair->repair->dealer_neq->dealer_neq_name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Phone
                                    </td>
                                    <td>
                                        : {{ $data->delivery_repair->repair->dealer->dealer_phone_number ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Alamat Kirim
                                    </td>
                                    <td>
                                        :
                                        {{ $data->delivery_repair->repair->dealer->dealer_address ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Kelengkapan
                                    </td>
                                    <td>
                                        : {{ $data->delivery_completeness }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Catatan
                                    </td>
                                    <td>
                                        : {{ $data->delivery_note }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
                <td>
                    <div style="width:100%;height:180px">
                        <h2>DETAIL PENERIMA</h2>
                        <div>
                            <table style="width: 100%;page-break-inside: auto;">
                                <tr>
                                    <td style="width: 30%">
                                        Nomor
                                    </td>
                                    <td>
                                        :
                                        {{ $data->delivery_repair->repair->repair_number }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">
                                        Tanggal
                                    </td>
                                    <td>
                                        : {{ $data->delivery_repair->repair->created_at }}
                                    </td>
                                </tr>

                            </table>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

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
                @foreach ($data->delivery_repair->repair->repair_unit as $item)
                    <tr>
                        <td class="td">{{ $item->unit->motor->motor_name }}</td>
                        <td class="td">{{ $item->unit->color->color_name }}</td>
                        <td class="td">{{ $item->unit->unit_frame }}</td>
                        <td class="td">{{ $item->unit->unit_engine }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>



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
