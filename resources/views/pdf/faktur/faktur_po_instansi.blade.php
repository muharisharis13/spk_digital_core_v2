<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur PO Instansi</title>
    <style>
        body {
            font-size: x-small;
            margin: 0px;
        }

        @page {
            margin: 10px
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

<body>
    <table style="width: 100%">
        <tr>
            <td style="">
                <table>
                    <tr>
                        <td>No. Kontrak</td>
                        <td>: {{ $data->spk_instansi_general->po_no }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>: {{ $data->created_at }}</td>
                    </tr>
                </table>
            </td>
            <td style="text-align: right">
                <h1 style="padding: 0;margin:0">SURAT PEMESANAN KENDARAAN</h1>
                <div>{{ $data->dealer->dealer_name }}</div>
            </td>
        </tr>
    </table>

    <div style="margin-top:10px;width:50%">
        <table class="table">
            <thead>
                <tr>
                    <th class="th">PERUSAHAAN</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">
                        <table style="width: 50%">
                            <tr>
                                <td>Nama Perusahaan</td>
                                <td>: {{ $data->spk_instansi_general->instansi_name }}</td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td>: {{ $data->spk_instansi_general->instansi_address }}</td>
                            </tr>
                            <tr>
                                <td>Kota</td>
                                <td>: {{ $data->spk_instansi_general->city }}</td>
                            </tr>
                            <tr>
                                <td>Telp</td>
                                <td>: {{ $data->spk_instansi_general->no_hp }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="margin-top:10px;width:100%">
        <table class="table">
            <thead>
                <tr>
                    <th class="th">HARGA</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td">
                        <table style="width: 50%">
                            <tr>
                                <td style="width: 20%">Harga Unit</td>
                                <td>: {{ number_format($data->spk_instansi_general->po_values, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>Add. Cost</td>
                                <td>:
                                    {{ number_format(collect($data->spk_instansi_additional)->sum('additional_cost'), 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td>Komisi</td>
                                <td>:
                                    {{ number_format(collect($data->spk_instansi_motor)->sum('commission'), 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td>Diskon</td>
                                <td>:
                                    {{ number_format(collect($data->spk_instansi_motor)->sum('discount'), 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td>Over Diskon</td>
                                <td>:
                                    {{ number_format(collect($data->spk_instansi_motor)->sum('discount_over'), 0, ',', '.') }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>



    <div style=" margin-top:20px">
        <table class="table">
            <thead>
                <tr>
                    <th class="th">Nama Konsumen</th>
                    <th class="th">Type</th>
                    <th class="th">No Rangka</th>
                    <th class="th">No Mesin</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data->spk_instansi_unit as $item)
                    <tr>
                        <td class="td">{{ $data->dealer->dealer_name }}</td>
                        <td class="td">{{ $item->motor->motor_name }}</td>
                        <td class="td">{{ $item->unit->unit_frame }}</td>
                        <td class="td">{{ $item->unit->unit_engine }}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px">
        Dengan menandatangani aplikasi ini saya sebagai pemohon menyatakan bahwa data pribadi yang saya berikan adalah
        yang sebenarnya, untuk itu Yamaha dapat melakukan pemeriksaan terhadap kebenaran data yang saya berikan dalam
        aplikasi ini. Saya memberikan persetujuan kepada Yamaha untuk memberikan dan/atau menyebarluaskan data pribadi
        saya kepada pihak lain diluar Yamaha untuk tujuan komersial dalam rangka pengalihan, penawaran produk/jasa
        layanan kepada pihak ketiga serta memberi persetujuan kepada Yamaha (Dealer) untuk memperoleh keterangan,
        referensi dari sumber manapun dengan cara yang dianggap sah oleh Yamaha. Saya telah memahami penjelasan Yamaha
        (Dealer) mengenai tujuan dan konsekuensi dari pemberian dan/atau penyebarluasan data pribadi saya kepada pihak
        lain diluar Yamaha sebagaimana disebutkan diatas. Dan seluruh dokumen yang telah diserahkan kepada Yamaha
        (Dealer) tidak dapat dikembalikan.
    </div>

    <div style="display: flex;justify-content:space-between;margin-top:20px">
        <table class=" table">
            <tr>
                <td class="td" style="width: 25%">
                    <div style="height: 150px">
                        <div>
                            Konsumen

                        </div>
                        <br>
                        <br>
                        <br>
                        <small>
                            <i>Tanda Tangan</i>
                        </small>
                        <br>
                        <br>
                        <br>
                        <br>
                        <div>
                            (&nbsp;{{ $data->spk_instansi_general->instansi_name }}&nbsp;)
                        </div>
                    </div>
                </td>
                <td class="td" style="width: 25%">
                    <div style="height: 150px">
                        <div>
                            Tenaga Penjual

                        </div>
                        <br>
                        <br>
                        <br>
                        <small>
                            <i>Tanda Tangan & Stempel</i>
                        </small>
                        <br>
                        <br>
                        <br>
                        <br>
                        <div>
                            (&nbsp;{{ $data->spk_instansi_general->sales_name }}&nbsp;)
                        </div>
                    </div>
                </td>
                <td class="td" style="width: 25%">
                    <div style="height: 150px">
                        <div>
                            Koordinator / Supervisor

                        </div>
                        <br>
                        <br>
                        <br>
                        <small>
                            <i>Tanda Tangan & Stempel</i>
                        </small>
                        <br>
                        <br>
                        <br>
                        <br>
                        <div>
                            (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
                        </div>
                    </div>
                </td>
                <td class="td" style="width: 25%">
                    <div style="height: 150px">
                        <div>
                            Kepala Cabang

                        </div>
                        <br>
                        <br>
                        <br>
                        <small>
                            <i>Tanda Tangan & Stempel</i>
                        </small>
                        <br>
                        <br>
                        <br>
                        <br>
                        <div>
                            (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
