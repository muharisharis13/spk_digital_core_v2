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
                        <p class="detail-label">: ALL NEW NMAX 155 </p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Warna</p>
                    </div>
                    <div class="col-span-6 lg:col-span-9">
                        <p class="detail-label">: HITAM</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Konsumen</p>
                    </div>
                    <div class="col-span-6 lg:col-span-9">
                        <p class="detail-label">: Kusnandar</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">NIK</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: 1271878987987654</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Whatsapp</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: 082167874567</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">No. Handphone</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: 082167874567</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Salesman</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: Alyson Christiansen MD</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Catatan</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: ini pembelian NMAX HITAM</p>
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
                        <p class="detail-label">: 20 Jun 2024</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Nomor Indent</p>
                    </div>
                    <div class="col-span-6 lg:col-span-9">
                        <p class="detail-label">: 0020/INDENT/PAS-AH/06/2024</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Jenis Transaksi</p>
                    </div>
                    <div class="col-span-6 lg:col-span-9">
                        <p class="detail-label">: CREDIT</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label"> Leasing</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: FIF</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Amount</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: Rp 10.000.000</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Bayar</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: Rp 10.000.000</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Sisa</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: 0</p>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="text-2xl font-bold mt-8 mb-4">List Detail Pembayaran</h2>
        <div class=" overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr>
                        <th class="py-2 px-4 bg-[#00029D] text-white">NO.</th>
                        <th class="py-2 px-4 bg-[#00029D] text-white">NOMOR</th>
                        <th class="py-2 px-4 bg-[#00029D] text-white">TANGGAL BAYAR</th>
                        <th class="py-2 px-4 bg-[#00029D] text-white">NOMINAL</th>
                        <th class="py-2 px-4 bg-[#00029D] text-white">METODE BAYAR</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-gray-100 border-b">
                        <td class="py-2 px-4 text-center">1</td>
                        <td class="py-2 px-4 text-center">0025/PAYMENT/PAS-AH/06/2024</td>
                        <td class="py-2 px-4 text-center">20 Jun 2024</td>
                        <td class="py-2 px-4 text-center">Rp 10.000.000</td>
                        <td class="py-2 px-4 text-center">BANK_TRANSFER</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
