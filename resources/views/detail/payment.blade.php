@extends('layouts.app')

@section('title', 'SPK Payment')
@section('content')
    <main class="flex-1 ">
        <div class="m-2 max-w-screen p-4 md:p-6 2xl:p-2">
            <div class="bg-white py-6 px-5 rounded-md undefined">
                <div class="grid grid-cols-2 gap-6">
                    <div class="col-span-2 lg:col-span-1">
                        <div class="grid grid-cols-12  gap-6">
                            <div class="col-span-12 lg:col-span-12">
                                <p class="detail-title">DETAIL PEMBAYARAN</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Pembayar</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label text-uppercase">: {{ $data->spk_payment_for ?? '-' }}</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Jenis</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label text-uppercase">: {{ $data->spk_payment_type ?? '-' }}</p>
                            </div>
                            <div class="col-span-12 lg:col-span-12 mt-8">
                                <p class="detail-title">DETAIL SPK</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">No. SPK</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">: {{ $data->spk->spk_number ?? '-' }}</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Metode Pembayaran</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">:
                                    {{ $data->spk->spk_transaction->spk_transaction_method_payment ?? '-' }}</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Tipe Motor</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">: {{ $data->spk->spk_unit->motor->motor_name ?? '-' }}</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Warna</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">: {{ $data->spk->spk_unit->color->color_name ?? '-' }}</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">No. Rangka</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">: {{ $data->spk->spk_unit->unit->unit_frame }}</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">No. Mesin</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">: {{ $data->spk->spk_unit->unit->unit_engine }}</p>
                            </div>

                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Konsumen</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">: {{ $data->spk->spk_customer->spk_customer_name ?? '-' }}</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Nama STNK</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">: {{ $data->spk->spk_legal->spk_legal_name ?? '-' }}</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">No. Telp</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">: {{ $data->spk->spk_customer->spk_customer_telp ?? '-' }}</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">No. Ponsel</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">: {{ $data->spk->spk_customer->spk_customer_no_phone ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 lg:col-span-1 mt-8 lg:mt-0">
                        <div class="grid grid-cols-12 gap-6">
                            <div class="col-span-12 lg:col-span-12 flex justify-between">
                                <p class="detail-title">DETAIL HARGA</p>
                                <p class="detail-title">finance_check</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Off The Road</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">: Rp
                                    {{ number_format($data->spk->spk_pricing->spk_pricing_off_the_road, 0, ',', '.') }}</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">BBN</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">: Rp
                                    {{ number_format($data->spk->spk_pricing->spk_pricing_bbn, 0, ',', '.') }}</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">On The Road</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">: Rp
                                    {{ number_format($data->spk->spk_pricing->spk_pricing_on_the_road, 0, ',', '.') }}</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Indent</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">: Rp
                                    {{ number_format($data->spk->spk_pricing->spk_pricing_indent_nominal, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Discount</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">:
                                    {{ number_format($data->spk->spk_pricing->spk_pricing_discount, 0, ',', '.') }}</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Over Discount</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">:
                                    {{ number_format($data->spk->spk_pricing->spk_pricing_over_discount, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Subsidi</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">:
                                    {{ number_format($data->spk->spk_pricing->spk_pricing_subsidi, 0, ',', '.') }}</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Booster</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">:
                                    {{ number_format($data->spk->spk_pricing->spk_pricing_booster, 0, ',', '.') }}</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Komisi</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">:
                                    {{ number_format($data->spk->spk_pricing->spk_pricing_commission, 0, ',', '.') }}</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Komisi Broker</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">:
                                    {{ number_format($data->spk->spk_pricing->spk_pricing_broker_commission, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Biaya Pengantaran</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">:
                                    {{ number_format($data->spk->spk_pricing->spk_pricing_delivery_cost, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Total Aksesoris</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                @php
                                    $totalAmount = 0;
                                @endphp

                                @foreach ($data->spk->spk_pricing->spk_pricing_accecories as $payment)
                                    @php
                                        $totalAmount += $payment->spk_pricing_accecories_price;
                                    @endphp
                                @endforeach
                                <p class="detail-label">: {{ number_format($totalAmount, 0, ',', '.') }}</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label font-semibold">Nominal</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label font-semibold">: Rp
                                    {{ number_format($data->spk_payment_amount_total, 0, ',', '.') }}</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Pembayaran</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                @php
                                    $totalPayment = 0;
                                @endphp

                                @foreach ($data->spk->spk_payment->spk_payment_list as $payment)
                                    @php
                                        $totalPayment += $payment->spk_payment_list_amount;
                                    @endphp
                                @endforeach
                                <p class="detail-label">: Rp {{ number_format($totalPayment, 0, ',', '.') }}</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Sisa</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">:
                                    {{ number_format($data->spk_payment_amount_total - $totalPayment, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2 mt-5">
                        <p class="detail-title">List Detail Pembayaran</p>
                    </div>
                    <div class="col-span-2">
                        <div
                            class="relative w-full flex flex-col rounded-lg overflow-auto border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark min-h-90 undefined">
                            <table
                                class="border-collapse w-auto table-responsive whitespace-nowrap dark:border-strokedark">
                                <thead>
                                    <tr class="border-t bg-primary-color border-stroke py-4.5 px-4 md:px-6 2xl:px-7.5">

                                        <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">NOMOR</th>
                                        <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">TANGGAL
                                            BAYAR</th>
                                        <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">NOMINAL
                                        </th>
                                        <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">METODE
                                            BAYAR</th>
                                        <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">BANK</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data->spk->spk_payment->spk_payment_list as $payment)
                                        <tr class="border-t border-stroke py-4.5 px-4 md:px-6 2xl:px-7.5 items-center">

                                            <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                                <div class=" items-center text-center">
                                                    <span>{{ $payment->spk_payment_list_number }}</span>
                                                </div>
                                            </td>
                                            <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                                <div class=" items-center text-center">
                                                    <span>{{ $payment->spk_payment_list_date }}</span>
                                                </div>
                                            </td>
                                            <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                                <div class=" items-center text-center"><span class="whitespace-nowrap">Rp
                                                        {{ number_format($payment->spk_payment_list_amount, 0, ',', '.') }}</span>
                                                </div>
                                            </td>
                                            <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                                <div class=" items-center text-center text-uppercase">
                                                    <span>{{ $payment->spk_payment_list_method }}</span>
                                                </div>
                                            </td>
                                            <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                                <div class=" items-center text-center">
                                                    <span>{{ $payment->bank->bank_name ?? '-' }}</span>
                                                </div>
                                            </td>


                                        </tr>
                                    @endforeach
                                    <tr class="border-t border-stroke py-4.5 px-4 md:px-6 2xl:px-7.5 items-center">

                                        <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                            <div class=" items-center text-center">
                                                <span>0018/SPK-CASH-PAYMENT/PAS-AH/06/2024</span>
                                            </div>
                                        </td>
                                        <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                            <div class=" items-center text-center"><span>22 Jun 2024</span></div>
                                        </td>
                                        <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                            <div class=" items-center text-center"><span class="whitespace-nowrap">Rp
                                                    24.000.000</span></div>
                                        </td>
                                        <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                            <div class=" items-center text-center"><span>cash</span></div>
                                        </td>
                                        <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                            <div class=" items-center text-center"><span></span></div>
                                        </td>


                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-span-2 mt-5">
                        <p class="detail-title">List Refund</p>
                    </div>
                    <div class="col-span-2">
                        <div
                            class="relative w-full flex flex-col rounded-lg overflow-auto border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark min-h-90 undefined">
                            <table
                                class="border-collapse w-auto table-responsive whitespace-nowrap dark:border-strokedark">
                                <thead>
                                    <tr class="border-t bg-primary-color border-stroke py-4.5 px-4 md:px-6 2xl:px-7.5">
                                        <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">NO.</th>
                                        <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">NOMOR</th>
                                        <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">TANGGAL
                                            REFUND</th>
                                        <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">NOMINAL
                                        </th>
                                        <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">ALASAN
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="h-25 justify-center items-center">
                                        <td colspan="5" class="text-center text-black">Tidak ada data tersedia</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>
@endsection
