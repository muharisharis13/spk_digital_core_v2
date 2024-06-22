@extends('layouts.app')

@section('title', 'Instansi Payment')
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
                                <p class="detail-label">: COMPANY</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Jenis</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">: CASH</p>
                            </div>
                            <div class="col-span-12 lg:col-span-12 mt-8">
                                <p class="detail-title">DETAIL PO</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">No. PO</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">: 0898989898</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Nama Instansi</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">: PT. CREATE HAPUS CREATE</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Nama STNK</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">: PT. CREATE HAPUS CREATE</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">No. Telp</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">: -</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">No. Ponsel</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">: 081276567890</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 lg:col-span-1 mt-8 lg:mt-0">
                        <div class="grid grid-cols-12 gap-6">
                            <div class="col-span-12 lg:col-span-12 flex justify-between">
                                <p class="detail-title">DETAIL PEMBAYARAN</p>
                                <p class="detail-title">finance_check</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label font-semibold">Total Pembayaran</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label font-semibold">: Rp 19.431.500</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Pembayaran</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">: Rp 19.431.500</p>
                            </div>
                            <div class="lg:col-span-6 col-span-6">
                                <p class="detail-label">Sisa</p>
                            </div>
                            <div class="col-span-6 lg:col-span-6">
                                <p class="detail-label">: 0</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <div class="grid grid-cols-1 lg:flex flex-wrap gap-5"><button
                                class="btn-back w-auto justify-center flex items-center"
                                style="display: flex; justify-content: center; align-items: center;">BACK</button><button
                                class="btn-edit w-auto justify-center flex items-center"
                                style="display: flex; justify-content: center; align-items: center;">PRINT</button><button
                                class="btn-request w-full lg:w-auto justify-center flex items-center"
                                style="display: flex; justify-content: center; align-items: center;">REFUND</button></div>
                    </div>
                    <div class="col-span-2 mt-5">
                        <p class="detail-title">List Detail Pembayaran</p>
                    </div>
                    <div class="col-span-2">
                        <div
                            class="relative w-full flex flex-col rounded-lg overflow-auto border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark min-h-90 undefined">
                            <table class="border-collapse w-auto table-responsive whitespace-nowrap dark:border-strokedark">
                                <thead>
                                    <tr class="border-t bg-primary-color border-stroke py-4.5 px-4 md:px-6 2xl:px-7.5">
                                        <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">NO.</th>
                                        <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">NOMOR</th>
                                        <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">TANGGAL
                                            BAYAR</th>
                                        <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">NOMINAL
                                        </th>
                                        <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">METODE
                                            BAYAR</th>
                                        <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">BANK</th>
                                        <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">BUKTI BAYAR
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-t border-stroke py-4.5 px-4 md:px-6 2xl:px-7.5 items-center">
                                        <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                            <div class=" items-center text-center"><span>1</span></div>
                                        </td>
                                        <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                            <div class=" items-center text-center">
                                                <span>0017/SPK-INST/CASH/PAS-AH/06/2024</span>
                                            </div>
                                        </td>
                                        <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                            <div class=" items-center text-center"><span>21 Jun 2024</span></div>
                                        </td>
                                        <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                            <div class=" items-center text-center"><span class="whitespace-nowrap">Rp
                                                    19.431.500</span></div>
                                        </td>
                                        <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                            <div class=" items-center text-center"><span>cash</span></div>
                                        </td>
                                        <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                            <div class=" items-center text-center"><span></span></div>
                                        </td>
                                        <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                            <div class=" items-center text-center">-</div>
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
                            <table class="border-collapse w-auto table-responsive whitespace-nowrap dark:border-strokedark">
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
