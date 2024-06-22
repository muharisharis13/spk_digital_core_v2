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
                        <p class="detail-label">: 21 Jun 2024</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Nama Instansi</p>
                    </div>
                    <div class="col-span-6 lg:col-span-9">
                        <p class="detail-label">: PT. CREATE HAPUS CREATE</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Alamat</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: JL. Hapus</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Provinsi</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: SUMATERA SELATAN</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Kota/Kabupaten</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: KOTA PALEMBANG</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Kecamatan</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: Jakabaring</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Kelurahan</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: Silaberanti</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Kode Pos</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: 898989</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">No. Telp</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: 081276567890</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">No. Ponsel</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: 081276567890</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">E-mail</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: hapus@gmail.com</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Catatan</p>
                    </div>
                    <div class="lg:col-span-9 col-span-6">
                        <p class="detail-label">: Pembelian Onsite</p>
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
                        <p class="detail-label">: 21 Jun 2024</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Nomor PO</p>
                    </div>
                    <div class="col-span-6 lg:col-span-9">
                        <p class="detail-label">: 0898989898</p>
                    </div>
                    <div class="lg:col-span-3 col-span-6">
                        <p class="detail-label">Tipe Motor</p>
                    </div>
                    <div class="col-span-6 lg:col-span-9">
                        <p class="detail-label">: ALL NEW VIXION</p>
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
            <div class="col-span-12 ">
                <div class="grid grid-cols-12 lg:flex items-center gap-x-4 gap-y-3">
                    <div class="col-span-12 lg:col-span-2 2xl:col-span-2 3xl:col-span-1"><button
                            class="btn-back w-full lg:w-auto justify-center flex items-center"
                            style="display: flex; justify-content: center; align-items: center;">BACK</button></div>
                    <div class="col-span-12 lg:col-span-2 2xl:col-span-2 3xl:col-span-1"><a
                            class="btn-edit w-full lg:w-auto justify-center flex items-center"
                            href="/transaction/indent-instance/form?id=9c56041a-ed66-470d-b007-06c439e820f5"
                            style="display: flex; justify-content: center; align-items: center;">EDIT</a></div>
                    <div class="col-span-12 lg:col-span-2 2xl:col-span-2"><button
                            class="btn-primary justify-center flex items-center"
                            style="display: flex; justify-content: center; align-items: center;">PRINT</button></div>
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
                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">NO.</th>
                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">NOMOR</th>
                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">TANGGAL BAYAR</th>
                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">NOMINAL</th>
                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">METODE BAYAR</th>
                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">BANK</th>
                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">BUKTI BAYAR</th>
                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t border-stroke py-4.5 px-4 md:px-6 2xl:px-7.5 items-center">
                                <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                    <div class=" items-center text-center"><span>1</span></div>
                                </td>
                                <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                    <div class=" items-center text-center"><span>0016/PAYMENT/PAS-AH/06/2024</span></div>
                                </td>
                                <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                    <div class=" items-center text-center"><span>21 Jun 2024</span></div>
                                </td>
                                <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                    <div class=" items-center text-center"><span>Rp 10.000.000</span></div>
                                </td>
                                <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                    <div class=" items-center text-center"><span>CASH</span></div>
                                </td>
                                <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                    <div class=" items-center text-center"><span>-</span></div>
                                </td>
                                <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                    <div class=" items-center text-center">-</div>
                                </td>
                                <td class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                    <div class=" items-center text-center">
                                        <div class="gap-2 flex justify-center "><button
                                                class="btn-primary w-auto justify-center flex items-center"
                                                style="display: flex; justify-content: center; align-items: center;">
                                                <div class=""><svg xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 16 16" width="18" height="18"
                                                        fill="currentColor">
                                                        <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"></path>
                                                        <path
                                                            d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z">
                                                        </path>
                                                    </svg></div>
                                            </button></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
