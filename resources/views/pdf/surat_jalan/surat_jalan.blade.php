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
                <img src="{{ $logo }}" alt="Company Logo" class="logo" width="100">
            </div>

            <div>
                <div style="font-weight:bold;font-size:18px">{{ $data->dealer->dealer_name }}</div>
                <div>
                    {{ $data->dealer->dealer_address }}
                </div>
            </div>
        </div>
        <div style="text-align: right">
            <strong style="font-size: 18px">SURAT JALAN</strong>
            <div>No.{{ $data->delivery_number }}</div>
            <div>

                Tgl. {{ strftime('%d %B %Y', strtotime($data->created_at)) }}
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
                    <td>
                        {{ $data->delivery_driver_name }}
                    </td>
                </tr>
                <tr>
                    <td>Mobil</td>
                    <td>:</td>
                    <td>
                        {{ $data->delivery_vehicle }}
                    </td>
                </tr>
            </table>
        </div>
        <div style="margin-top:50px;float:right;width:50%">
            <strong>Detail Pembeli :</strong>
            <div>
                Pengantaran Unit
            </div>
            <div>
                {{ $data->delivery_repair->repair->repair_number }}
            </div>
            <table style="width: 100%">
                <tr>
                    <td>Tgl. Pengiriman</td>
                    <td>:</td>
                    <td>{{ strftime('%d %B %Y', strtotime($data->created_at)) }}</td>
                </tr>
                <tr>
                    <td>Kelengkapan</td>
                    <td>:</td>
                    <td>{{ $data->delivery_completeness }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div style="clear: both;line-height:25px">
        <div style="margin-top:50px">Penerima :</div>
        <strong>{{ $data->delivery_repair->repair->main_dealer_name }}</strong>
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
                @foreach ($data->delivery_repair->repair->repair_unit as $item)
                    <tr style="text-align: center;text-transform:uppercase">
                        <td>
                            {{ $item->unit->motor->motor_name }}
                        </td>
                        <td>
                            {{ $item->unit->unit_color ? $item->unit->unit_color : $item->unit->color->color_name }}
                        </td>
                        <td>
                            {{ $item->unit->unit_frame }}
                        </td>
                        <td>
                            {{ $item->unit->unit_engine }}
                        </td>
                    </tr>
                @endforeach
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
