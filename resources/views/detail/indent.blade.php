@extends('layouts.app')

@section('title', 'Detail Indent')
@section('content')
    <div class=" max-w-[90vw] mx-auto bg-white p-6 rounded-lg shadow-md">
        <div class="grid grid-cols-12 gap-6">
            <div class="lg:col-span-6 col-span-12">
                <div class="grid grid-cols-12 gap-6">
                    <div class="lg:col-span-12 xsm:col-span-12 col-span-12">
                        <div class=" detail-title">DETAIL INDENT REGULAR</div>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Tipe Motor</p>
                    </div>
                    <div class="col-span-6 lg:col-span-9">
                        <p class="detail-label">: {{ $data->motor->motor_name ?? '-' }} </p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Warna</p>
                    </div>
                    <div class="col-span-6 lg:col-span-9">
                        <p class="detail-label">: {{ $data->color->color_name ?? '-' }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Konsumen</p>
                    </div>
                    <div class="col-span-6 lg:col-span-9">
                        <p class="detail-label">: {{ $data->spk_general->spk->spk_customer->spk_customer_name ?? '-' }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">NIK</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: {{ $data->spk_general->spk->spk_customer->spk_customer_nik ?? '-' }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Whatsapp</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: {{ $data->spk_general->spk->spk_customer->spk_customer_no_wa ?? '-' }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">No. Handphone</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: {{ $data->spk_general->spk->spk_customer->spk_customer_no_phone ?? '-' }}
                        </p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Salesman</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: {{ $data->salesman_name ?? '-' }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Catatan</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: {{ $data->indent_note ?? '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-6 col-span-12">
                <div class="grid grid-cols-12 gap-6">
                    <div class="lg:col-span-12 xsm:col-span-12 col-span-12">
                        <span class="detail-title">
                            <span class="text-slate-700" style="font-weight: 600;">SPK</span>
                        </span>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Tanggal</p>
                    </div>
                    <div class="col-span-6 lg:col-span-9">
                        <p class="detail-label">: {{ $data->spk_general->spk->created_at ?? '-' }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Nomor Indent</p>
                    </div>
                    <div class="col-span-6 lg:col-span-9">
                        <p class="detail-label">: {{ $data->indent_number ?? '-' }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Jenis Transaksi</p>
                    </div>
                    <div class="col-span-6 lg:col-span-9">
                        <p class="detail-label">: {{ $data->indent_type ?? '-' }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label"> Leasing</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: {{ $data->leasing_name ?? '-' }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Amount</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: Rp. {{ number_format($data->amount_total, 0, ',', '.') }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Bayar</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        @php
                            $totalAmount = 0;
                        @endphp

                        @foreach ($data->indent_payment as $payment)
                            @php
                                $totalAmount += $payment->indent_payment_amount;
                            @endphp
                        @endforeach

                        <p class="detail-label">: Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Sisa</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: {{ $data->amount_total - $totalAmount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="text-2xl font-bold mt-8 mb-4">List Detail Pembayaran</h2>
        <div class=" overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr>
                        <th class="py-2 px-4 bg-[#00029D] text-white">NOMOR</th>
                        <th class="py-2 px-4 bg-[#00029D] text-white">TANGGAL BAYAR</th>
                        <th class="py-2 px-4 bg-[#00029D] text-white">NOMINAL</th>
                        <th class="py-2 px-4 bg-[#00029D] text-white">METODE BAYAR</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data->indent_payment as $payment)
                        <tr class="bg-gray-100 border-b">
                            <td class="py-2 px-4 text-center">{{ $payment->indent_payment_number }}</td>
                            <td class="py-2 px-4 text-center">{{ $payment->indent_payment_date }}</td>
                            <td class="py-2 px-4 text-center">Rp
                                {{ number_format($payment->indent_payment_amount, 0, ',', '.') }}</td>
                            <td class="py-2 px-4 text-center text-uppercase">{{ $payment->indent_payment_method }}</td>
                        </tr>
                    @endforeach
                    {{-- <tr class="bg-gray-100 border-b">
                        <td class="py-2 px-4 text-center">1</td>
                        <td class="py-2 px-4 text-center">0025/PAYMENT/PAS-AH/06/2024</td>
                        <td class="py-2 px-4 text-center">20 Jun 2024</td>
                        <td class="py-2 px-4 text-center">Rp 10.000.000</td>
                        <td class="py-2 px-4 text-center">BANK_TRANSFER</td>
                    </tr> --}}
                </tbody>
            </table>
        </div>
    </div>
@endsection
