@extends('layouts.app')

@section('title', 'Detail Indent Instansi')

@section('content')
    <div class="bg-white shadow-md rounded-lg p-8">
        <div class="grid grid-cols-12 gap-6">
            <div class="lg:col-span-6 col-span-12">
                <div class="grid grid-cols-12 gap-6">
                    <div class="lg:col-span-12 xsm:col-span-12 col-span-12"><span class="detail-title">DETAIL INDENT
                            INSTANSI</span></div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Tanggal Indent</p>
                    </div>
                    <div class="col-span-6 lg:col-span-9">
                        <p class="detail-label">: {{ $data->indent_instansi_po_date }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Nama Instansi</p>
                    </div>
                    <div class="col-span-6 lg:col-span-9">
                        <p class="detail-label">: {{ $data->indent_instansi_name }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Alamat</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: {{ $data->indent_instansi_address }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Provinsi</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: {{ $data->province_name }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Kota/Kabupaten</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: {{ $data->city_name }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Kecamatan</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: {{ $data->district_name }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Kelurahan</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: {{ $data->sub_district_name }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Kode Pos</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: {{ $data->indent_instansi_postal_code ?? '-' }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">No. Telp</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: {{ $data->indent_instansi_no_telp }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">No. Ponsel</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: {{ $data->indent_instansi_no_hp }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">E-mail</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: {{ $data->indent_instansi_email }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Catatan</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: {{ $data->indent_instansi_note ?? '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-6 col-span-12">
                <div class="grid grid-cols-12 gap-6">
                    <div class="lg:col-span-12 xsm:col-span-12 col-span-12"><span class="detail-title"><span
                                class="text-slate-700" style="font-weight: 600;">SPK</span></span></div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Tanggal PO</p>
                    </div>
                    <div class="col-span-6 lg:col-span-9">
                        <p class="detail-label">: {{ $data->indent_instansi_po_date }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Nomor PO</p>
                    </div>
                    <div class="col-span-6 lg:col-span-9">
                        <p class="detail-label">: {{ $data->indent_instansi_number }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Tipe Motor</p>
                    </div>
                    <div class="col-span-6 lg:col-span-9">
                        <p class="detail-label">: {{ $data->motor->motor_name ?? '-' }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Amount</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: Rp {{ number_format($data->indent_instansi_nominal, 0, ',', '.') }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Bayar</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        @php
                            $totalAmount = 0;
                        @endphp

                        @foreach ($data->indent_instansi_payments as $payment)
                            @php
                                $totalAmount += $payment->indent_instansi_payment_amount;
                            @endphp
                        @endforeach
                        <p class="detail-label">: Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Sisa</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: {{ $data->indent_instansi_nominal - $totalAmount }}</p>
                    </div>
                </div>
            </div>

            <div class="col-span-12 mt-5">
                <p class="detail-title">List Detail Pembayaran</p>
            </div>
            <div class="col-span-12">
                <div
                    class="relative w-full flex flex-col rounded-lg overflow-auto border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark min-h-90 undefined">
                    <table class="border-collapse w-auto table-responsive whitespace-nowrap dark:border-strokedark">
                        <thead>
                            <tr class="border-t bg-primary-color border-stroke py-4.5 px-4 md:px-6 2xl:px-7.5">
                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">NOMOR</th>
                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">TANGGAL BAYAR</th>
                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">NOMINAL</th>
                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">METODE BAYAR</th>
                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">BANK</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data->indent_instansi_payments as $payment)
                                <tr class="border-t border-stroke py-4.5 px-4 md:px-6 2xl:px-7.5 items-center">

                                    <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                        <div class=" items-center text-center">
                                            <span>{{ $payment->indent_instansi_payment_number }}</span></div>
                                    </td>
                                    <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                        <div class=" items-center text-center">
                                            <span>{{ $payment->indent_instansi_payment_date }}</span></div>
                                    </td>
                                    <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                        <div class=" items-center text-center"><span>Rp
                                                {{ number_format($payment->indent_instansi_payment_amount, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                        <div class=" items-center text-center">
                                            <span>{{ $payment->indent_instansi_payment_method }}</span></div>
                                    </td>
                                    <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                        <div class=" items-center text-center"><span>{{ $payment->bank ?? '-' }}</span>
                                        </div>
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
