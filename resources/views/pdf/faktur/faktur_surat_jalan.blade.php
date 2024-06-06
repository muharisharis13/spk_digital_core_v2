<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Faktur</title>
    <style>
        body {
            font-size: x-small;
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

        @page {
            size: auto;
            /* auto is the initial value */

            /* this affects the margin in the printer settings */
            margin: 25mm 25mm 25mm 25mm;
        }

        body {
            /* this affects the margin on the content before sending to printer */
            margin: 0px;
        }
    </style>
</head>

<body style="font-family: Arial, Helvetica, sans-serif;">
    <div>
        <table style="width: 100%">
            <tr>
                <td>
                    <div class="left2" style="height: 200px">
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
                    <div class="right2" style="text-align: right;height:200px">
                        <h2>Surat Jalan</h2>
                        <div>No.{{ $data->delivery_number }}</div>
                        <div style="text-align: right;">
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::size(100)->generate('https://google.com')) !!} ">

                        </div>
                        <div style="font-weight:bold">
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
                    <div style="width:100%; height:250px">
                        <h2>DETAIL PENGIRIMAN</h2>
                        <div>
                            <table style="width: 100%;page-break-inside: auto;">

                                <tr>
                                    <td>
                                        Nama Driver
                                    </td>
                                    <td>
                                        : 0007/DELIVERY/S/06/2024
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Kendaraan
                                    </td>
                                    <td>
                                        : 0007/DELIVERY/S/06/2024
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Penerima
                                    </td>
                                    <td>
                                        : 0007/DELIVERY/S/06/2024
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Phone
                                    </td>
                                    <td>
                                        : 0007/DELIVERY/S/06/2024
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Alamat Kirim
                                    </td>
                                    <td>
                                        : 0007/DELIVERY/S/06/2024
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Kelengkapan
                                    </td>
                                    <td>
                                        : MANUAL BOOK, TOOLKIT, ACU, MIRROR, HELM, JAKET
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Catatan
                                    </td>
                                    <td>
                                        : MANUAL BOOK, TOOLKIT, ACU, MIRROR, HELM, JAKET
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
                <td>
                    <div style="width:100%;height:250px">
                        <h2>DETAIL PEMBELI</h2>
                        <div>
                            <table style="width: 100%;page-break-inside: auto;">
                                <tr>
                                    <td style="width: 30%">
                                        No. SPK
                                    </td>
                                    <td>
                                        : 0007/DELIVERY/S/06/2024
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">
                                        Jenis Transaksi
                                    </td>
                                    <td>
                                        : 0007/DELIVERY/S/06/2024
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">
                                        Tgl. Spk
                                    </td>
                                    <td>
                                        : 0007/DELIVERY/S/06/2024
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">
                                        Pembeli
                                    </td>
                                    <td>
                                        : 0007/DELIVERY/S/06/2024
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">
                                        Nama Stnk
                                    </td>
                                    <td>
                                        : 0007/DELIVERY/S/06/2024
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">
                                        Alamat Pembeli
                                    </td>
                                    <td>
                                        : 0007/DELIVERY/S/06/2024
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
                <tr>
                    <td class="td">All new</td>
                    <td class="td">All new</td>
                    <td class="td">All new</td>
                    <td class="td">All new</td>
                </tr>
            </tbody>
        </table>
    </div>



    <div style="display: flex;justify-content:space-between">
        <table class=" table">
            <tr>
                <td class="td">
                    <div style="height: 180px">
                        Diserahkan Oleh:
                    </div>
                </td>
                <td class="td">
                    <div style="height: 180px">
                        Diserahkan Oleh:
                    </div>
                </td>
                <td class="td">
                    <div style="height: 180px">
                        Diserahkan Oleh:
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
