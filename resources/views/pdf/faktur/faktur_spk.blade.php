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
    <table style=" width:100%">
        <tbody>
            <tr>
                <td>
                    <div>
                        <table>
                            <tbody>
                                <tr>
                                    <td>Nomor SPK</td>
                                    <td>: {{ $data->spk_number }}</td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>: {{ $data->created_at }}</td>
                                </tr>
                                <tr>
                                    <td>Tipe Penjualan</td>
                                    <td style="text-transform:uppercase">:
                                        {{ $data->spk_transaction->spk_transaction_method_payment }}</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </td>
                <td style="display: flex">
                    <div style="display: flex;flex-direction:column;align-items:baseline">
                        <h1 style="text-align: center;margin:0;padding:0;text-align:right">SURAT PEMESANAN KENDARAAN</h1>
                        <h2
                            style=" text-align:center;padding:0 !important;margin:0 !important;text-align:right;text-transform:uppercase">
                            {{ $data->spk_general->dealer->dealer_name ?? ($data->spk_general->dealer_neq->dealer_neq_name ?? '-') }}
                        </h2>
                    </div>
                </td>

            </tr>
        </tbody>
    </table>





    {{-- detail surat jalan --}}
    <div style="text-transform: uppercase">
        <table style="width: 100%">
            <tr>
                <td style="width:50%">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="th">IDENTITAS PEMBELI / PEMOHON</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="td">
                                    <table>
                                        <tr>
                                            <td>Nama</td>
                                            <td>: {{ $data->spk_customer->spk_customer_name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Alamat</td>
                                            <td>: {{ $data->spk_customer->spk_customer_address }}</td>
                                        </tr>
                                        <tr>
                                            <td>Kota</td>
                                            <td>: {{ $data->spk_customer->province }}, {{ $data->spk_customer->city }},
                                                {{ $data->spk_customer->district }},
                                                {{ $data->spk_customer->sub_district }},
                                                {{ $data->spk_customer->spk_customer_postal_code }}</td>
                                        </tr>
                                        <tr>
                                            <td>No. HP</td>
                                            <td>: {{ $data->spk_customer->spk_customer_no_phone }}</td>
                                        </tr>
                                        <tr>
                                            <td>No. ID</td>
                                            <td>: {{ $data->spk_customer->spk_customer_nik }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="th">
                                    <div>NAMA STNK, BPKB</div>
                                    <small>(Jika ada perbedaan antara pembeli dan pemakai)</small>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="td">
                                    <table>
                                        <tr>
                                            <td>Nama</td>
                                            <td>: {{ $data->spk_legal->spk_customer_name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Alamat</td>
                                            <td>: {{ $data->spk_legal->spk_customer_address }}</td>
                                        </tr>
                                        <tr>
                                            <td>Kota</td>
                                            <td>: {{ $data->spk_legal->province }}, {{ $data->spk_legal->city }},
                                                {{ $data->spk_legal->district }},
                                                {{ $data->spk_legal->sub_district }},
                                                {{ $data->spk_legal->spk_customer_postal_code }}</td>
                                        </tr>
                                        <tr>
                                            <td>No. HP</td>
                                            <td>: {{ $data->spk_legal->spk_customer_no_phone }}</td>
                                        </tr>
                                        <tr>
                                            <td>No. ID</td>
                                            <td>: {{ $data->spk_legal->spk_customer_nik }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table" style=" margin-top:10px">
                        <tr>
                            <td class="td" style="text-align: left">
                                Promo Khusus (jika ada) :
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="display:flex">
                    <div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="th">
                                        <div>SEPEDA MOTOR</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="td">
                                        <table>
                                            <tr>
                                                <td>Tipe</td>
                                                <td>: {{ $data->spk_unit->motor->motor_name }}</td>
                                            </tr>
                                            <tr>
                                                <td>Warna</td>
                                                <td>: {{ $data->spk_unit->color->color_name }}</td>
                                            </tr>
                                            <tr>
                                                <td>No. Rangka</td>
                                                <td>: {{ $data->spk_unit->unit->unit_frame }}</td>
                                            </tr>
                                            <tr>
                                                <td>No. Mesin</td>
                                                <td>: {{ $data->spk_unit->unit->unit_engine }}</td>
                                            </tr>
                                            <tr>
                                                <td>Harga OTR</td>
                                                <td>: {{ $data->spk_pricing->spk_pricing_off_the_road }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="table" style=" margin-top:10px">
                            <tbody>
                                <tr>
                                    <td class="td">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>Diskon</td>
                                                    <td>: {{ $data->spk_pricing->spk_pricing_discount }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Over Diskon</td>
                                                    <td>: {{ $data->spk_pricing->spk_pricing_over_discount }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Subsidi</td>
                                                    <td>: {{ $data->spk_pricing->spk_pricing_subsidi }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Fee Sales</td>
                                                    <td>: {{ $data->spk_pricing->spk_pricing_commission ?? 0 }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Fee Broker</td>
                                                    <td>: {{ $data->spk_pricing->spk_pricing_broker_commission ?? 0 }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Broker</td>
                                                    <td>: {{ $data->spk_pricing->spk_pricing_broker_name ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Salesman</td>
                                                    <td>: {{ $data->spk_general->sales_name ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Panjar</td>
                                                    <td>: {{ $data->spk_general->indent->amount_total ?? 0 }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Booster</td>
                                                    <td>: {{ $data->spk_pricing->spk_pricing_booster ?? 0 }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div style=" margin-top:10px">
        Dengan menandatangani aplikasi ini saya sebagai pemohon menyatakan bahwa data pribadi yang saya berikan adalah
        yang sebenar-benarnya, untuk itu Yamaha dapat melakukan pemeriksaan terhadap kebenaran data yang saya berikan
        dalam aplikasi ini. Saya memberikan persetujuan kepada Yamaha
        untuk memberikan dan/atau menvebarluaskan data pribadi sava kepada bihak lain diluar Yamaha untuk tuiuan
        komersial dalam raneka pengalihan. Denagihan. Denawaran produk/iasa lavanan kepada Dihak ketiga serta memberi
        Dersetuiuan kepada Yamaha (Dealer) untuk memberoleh keterangan
        referensi dari sumber manapun dengan cara vang dianggap sah oleh Yamaha. Sava telah memahami penielasan Yamaha
        (Dealer) mengenai tujuan dan konsekuensi dari pemberian dan/atau penvebarluasan data pribadi sava kepada pihak
        lain diluar Yamaha sebagaimana disebutkan diatas. Dan seluruh
        dokumen yang telan diserankan kepada Yamana (Dealer) tidak dapat dikembalikan.
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
                            (&nbsp;{{ $data->spk_customer->spk_customer_name }}&nbsp;)
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
                            (&nbsp;{{ $data->spk_general->sales_name }}&nbsp;)
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
