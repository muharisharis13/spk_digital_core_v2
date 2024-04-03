<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body style="padding: 0; margin: 0; font-family: Arial, Helvetica, sans-serif;font-size:smaller">
    <div style="clear: both;position:relative;margin-bottom:100px">
        <div style="float: left;width:50%;">
            <div>
                <img src="logo/alfa-scorpii-logo.png" alt="Company Logo" class="logo" width="100">
            </div>
            {{-- {{ $data }} --}}

            <div>
                <div style="font-weight:bold;font-size:18px">{ $data->indent->dealer->dealer_name }</div>
                <div>
                    { $data->indent->dealer->dealer_address }
                </div>
            </div>
        </div>
        <div style="text-align: right">
            <strong style="font-size: 18px">SURAT JALAN</strong>
            <div>No.{ $data->indent_payment_number }</div>
            <div>
                Tgl. 12 Maret 2024
            </div>
        </div>
    </div>
    <div style="clear: both;line-height:25px">
        <div style="float: left;margin-top:50px;width:50%">
            <strong>Detail Pengiriman :</strong>
            <table style="width: 42%">
                <tr>
                    <td>Driver</td>
                    <td>:</td>
                </tr>
                <tr>
                    <td>Mobil</td>
                    <td>:</td>
                </tr>
            </table>
        </div>
        <div style="margin-top:50px;float:right;width:50%">
            <strong>Detail Pembeli :</strong>
            <div>
                Pengantaran Unit
            </div>
            <div>
                0001/SJ/MDN-AR/03/2024
            </div>
            <table style="width: 100%">
                <tr>
                    <td>Tgl. Pengiriman</td>
                    <td>:</td>
                    <td>01 Maret 2024</td>
                </tr>
                <tr>
                    <td>Kelengkapan</td>
                    <td>:</td>
                    <td>Helm</td>
                </tr>
            </table>
        </div>
    </div>
    <div style="clear: both;line-height:25px">
        <div style="margin-top:50px">Penerima :</div>
        <strong>PT Alfa Scorpii</strong>
        <div style="width: 50%">Jln. H Adam malikNo. 34 C Silalas - Medan Barat Medan 20214, Sumatera Utara 0822</div>
    </div>
    <div style="clear: both;line-height:25px;margin-top:50px">
        <table style="width: 100%; border: 1px solid #d4d4d4; background-color: white">
            <thead style="background-color: #f2f2f2">
                <tr>
                    <th style="padding: 8px">Model</th>
                    <th style="padding: 8px">Warna</th>
                    <th style="padding: 8px">Rangka</th>
                    <th style="padding: 8px">Mesin</th>
                </tr>
            </thead>
            <tbody>
                <tr style="text-align: center;text-transform:uppercase">
                    <td>
                        Nmax
                    </td>
                    <td>
                        abu abu
                    </td>
                    <td>
                        adsaasd
                    </td>
                    <td>
                        gasf
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="clear: both;margin-top:50px">
        <table style="width:100%">
            <tr>
                <td>
                    <div style="border: 1px solid #cccc;height:100px;padding:8px">
                        <small>Diserahkan Oleh :</small>
                        <br>
                        <small>Tgl : </small>
                    </div>
                </td>
                <td>
                    <div style="border: 1px solid #cccc;height:100px;padding:8px">
                        <small>Diserahkan Oleh :</small>
                        <br>
                        <small>Tgl : </small>
                    </div>
                </td>
                <td>
                    <div style="border: 1px solid #cccc;height:100px;padding:8px">
                        <small>Diserahkan Oleh :</small>
                        <br>
                        <small>Tgl : </small>
                    </div>
                </td>
            </tr>
        </table>

    </div>
</body>

</html>
