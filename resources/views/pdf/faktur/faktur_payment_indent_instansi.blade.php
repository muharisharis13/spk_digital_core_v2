<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Payment Indent Instansi</title>
    <style>
        body {
            font-size: x-small;
            margin: 0px
        }


        th,
        td {
            text-align: left;
            vertical-align: top;
            /* Atur sel rata atas */
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

        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }

        .alamat {
            max-width: 500px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body style="padding: 0; margin: 0; font-family: Arial, Helvetica, sans-serif;">
    <div class="row" style="height: 250px;">
        <div class="left">
            <div>
                <img src="{{ $logo }}" alt="Company Logo" class="logo" width="100">
            </div>
            {{-- {{ $data }} --}}

            <div>
                <h3>{{ $data->indent_instansi->dealer->dealer_name }}</h3>
                <div class="alamat">
                    {{ $data->indent_instansi->dealer->dealer_address }}
                </div>
            </div>
        </div>

        <div class="right" style="text-align: right">
            <h2>KWITANSI PANJAR</h2>
            <div>No.{{ $data->indent_instansi_payment_number }}</div>
            <div style="text-align: right;margin-top:50px">
                <img src="data:image/png;base64, {!! base64_encode(QrCode::size(100)->generate('https://google.com')) !!} ">
                {{-- {!!  !!} --}}

            </div>
            <div style="margin-top: 50px;font-weight:bold">
                {{ date('d-M-Y', strtotime($data->created_at)) }}
            </div>
        </div>
    </div>


    <table style="margin-top: 50px;width:100%">
        <tr>
            <th style="space-white:nowrap;padding-bottom:10px">
                <div style="white-space: nowrap">Sudah diterima dari</div>
            </th>
            <td>:</td>
            <td>{{ $data->indent_instansi->indent_instansi_name }}</td>
        </tr>
        <tr>
            <th style="padding-bottom:10px">Banyaknya Uang</th>
            <td>:</td>
            <td> Rp {{ number_format($data->indent_instansi_payment_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th style="padding-bottom:10px">
                <div style="white-space: nowrap">Untuk Pembayaran</div>
            </th>
            <td>:</td>
            <td>Panjar 1 (satu) unit sepeda motor YAMAHA</td>
        </tr>
        <tr>
            <th style="padding-bottom:10px">Type</th>
            <td>:</td>
            <td>{{ $data->indent_instansi->motor->motor_name }}</td>
        </tr>
        <tr>
            <th>Keterangan</th>
            <td>:</td>
            <td>Untuk indent no. {{ $data->indent_instansi_payment_number }}
                tgl.{{ date('d-M-Y', strtotime($data->created_at)) }} dengan total panjar
                Rp.{{ number_format($data->indent_instansi_payment_amount, 0, ',', '.') }}
                Pembayaran
                {{ ($data->indent_instansi_payment_method == 'cash' ? 'CASH' : $data->indent_instansi_payment_method == 'bank_transfer') ? 'BANK' : '-' }}
                via KASIR
            </td>
        </tr>
    </table>
    <div class="container-total-amount" style="
    margin-top:40px;margin-bottom:40px">
        <div style="width:100%;">
            <div
                style="float:left; text-align:center; border:1px solid black; width:50%; padding:10px; border-radius:5px;">
                <strong>Jumlah Rp. {{ number_format($data->indent_instansi_payment_amount, 0, ',', '.') }}</strong>
            </div>
            <div style="padding:10px;text-align:center;white-space:nowrap">
                Lembar 1 : Konsumen
            </div>
        </div>
    </div>
    <small style="">
        NB. Pembayaran dengan Cheque/Bilyet Giro dianggap sah setelah Cheque/Bilyet Giro tersebut dapat
        diuangkan (Clearing).
    </small>
    <br>
    <hr>
    <small style="">
        Kwitansi ini Tidak Berlaku Apabila Unit Sudah Diterima. HARGA TIDAK MENGIKAT DAPAT BERUBAH SEWAKTU2
        SESUAI KETENTUAN
    </small>
    <br>
    <hr>
    <small style="color: red;font-weight:bold;">
        Scan QR disamping dengan aplikasi ALFA SCORPII untuk detail dan cek keaslian.
    </small>
    <div class="page-break"></div>
    <div>
        <div class="row" style="height: 250px;">
            <div class="left">
                <div>
                    <img src="{{ $logo }}" alt="Company Logo" class="logo" width="100">
                </div>
                {{-- {{ $data }} --}}

                <div>
                    <h3>{{ $data->indent_instansi->dealer->dealer_name }}</h3>
                    <div class="alamat">
                        {{ $data->indent_instansi->dealer->dealer_address }}
                    </div>
                </div>
            </div>

            <div class="right" style="text-align: right">
                <h2>KWITANSI PANJAR</h2>
                <div>No.{{ $data->indent_instansi_payment_number }}</div>
                <div style="text-align: right;margin-top:50px">
                    <img src="data:image/png;base64, {!! base64_encode(QrCode::size(100)->generate('https://google.com')) !!} ">
                    {{-- {!!  !!} --}}

                </div>
                <div style="margin-top: 50px;font-weight:bold">
                    {{ date('d-M-Y', strtotime($data->created_at)) }}
                </div>
            </div>
        </div>


        <table style="margin-top: 50px;width:100%">
            <tr>
                <th style="space-white:nowrap;padding-bottom:10px">
                    <div style="white-space: nowrap">Sudah diterima dari</div>
                </th>
                <td>:</td>
                <td>{{ $data->indent_instansi->indent_instansi_name }}</td>
            </tr>
            <tr>
                <th style="padding-bottom:10px">Banyaknya Uang</th>
                <td>:</td>
                <td> Rp {{ number_format($data->indent_instansi_payment_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th style="padding-bottom:10px">
                    <div style="white-space: nowrap">Untuk Pembayaran</div>
                </th>
                <td>:</td>
                <td>Panjar 1 (satu) unit sepeda motor YAMAHA</td>
            </tr>
            <tr>
                <th style="padding-bottom:10px">Type</th>
                <td>:</td>
                <td>{{ $data->indent_instansi->motor->motor_name }}</td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td>:</td>
                <td>Untuk indent no. {{ $data->indent_instansi_payment_number }}
                    tgl.{{ date('d-M-Y', strtotime($data->created_at)) }} dengan total panjar
                    Rp.{{ number_format($data->indent_instansi_payment_amount, 0, ',', '.') }}
                    Pembayaran
                    {{ ($data->indent_instansi_payment_method == 'cash' ? 'CASH' : $data->indent_instansi_payment_method == 'bank_transfer') ? 'BANK' : '-' }}
                    via KASIR
                </td>
            </tr>
        </table>
        <div class="container-total-amount" style="
    margin-top:40px;margin-bottom:40px">
            <div style="width:100%;">
                <div
                    style="float:left; text-align:center; border:1px solid black; width:50%; padding:10px; border-radius:5px;">
                    <strong>Jumlah Rp. {{ number_format($data->indent_instansi_payment_amount, 0, ',', '.') }}</strong>
                </div>
                <div style="padding:10px;text-align:center;white-space:nowrap">
                    Lembar 1 : Poskas
                </div>
            </div>
        </div>
        <small style="">
            NB. Pembayaran dengan Cheque/Bilyet Giro dianggap sah setelah Cheque/Bilyet Giro tersebut dapat
            diuangkan (Clearing).
        </small>
        <br>
        <hr>
        <small style="">
            Kwitansi ini Tidak Berlaku Apabila Unit Sudah Diterima. HARGA TIDAK MENGIKAT DAPAT BERUBAH SEWAKTU2
            SESUAI KETENTUAN
        </small>
        <br>
        <hr>
        <small style="color: red;font-weight:bold;">
            Scan QR disamping dengan aplikasi ALFA SCORPII untuk detail dan cek keaslian.
        </small>
    </div>
</body>

</html>
