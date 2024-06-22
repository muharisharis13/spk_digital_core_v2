@extends('layouts.app')

@section('title', 'Detail Indent Instansi')

@section('content')
    <main class="flex-1 ">
        <div class="m-2 max-w-screen p-4 md:p-6 2xl:p-2">
            <div>
                <div class="bg-white py-6 px-5 rounded-md undefined">
                    <div>
                        <div class="grid grid-cols-12 mb-3 ">
                            <div class="col-span-8"></div>
                            <div class="col-span-6 lg:col-span-2">
                                <p class="detail-title">Status PO</p>
                            </div>
                            <div class="col-span-6 lg:col-span-2 flex items-center gap-x-2">
                                <p class="hidden lg:block detail-title">:</p>
                                <p class="detail-title">publish</p>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-5">
                        <div class="col-span-2 lg:col-span-1">
                            <div class="grid grid-cols-12 gap-x-6 ">
                                <div class="col-span-12 flex justify-between items-center">
                                    <div class="detail-title">General Info</div>
                                </div>
                                <div class="col-span-12 mt-2 ">
                                    <hr>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                    <p class="detail-label">Tanggal</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3 flex items-center gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label">21 Jun 2024</p>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                    <p class="detail-label">Salesman</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3 flex items-center gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label">Alyson Christiansen MD</p>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3 flex  items-start">
                                    <p class="detail-label">No. Indent</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3  justify-start flex gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label ">0019/INDENT-INSTANSI/PAS-AH/06/2024 <br>(Nominal Rp 10.000.000)
                                    </p>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                    <p class="detail-label">No. Instansi</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3  flex items-center gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label">0026/PO-INST/PAS-AH/06/2024</p>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                    <p class="detail-label">No. PO</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3  flex items-center gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label">0898989898</p>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                    <p class="detail-label">Tanggal PO</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3  flex items-center gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label">21 Jun 2024</p>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                    <p class="detail-label">Nama Instansi</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3  flex items-center gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label">PT. CREATE HAPUS CREATE</p>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                    <p class="detail-label">Alamat</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3  flex items-center gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label">JL. Hapus</p>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                    <p class="detail-label">Provinsi</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3  flex items-center gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label">SUMATERA SELATAN</p>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                    <p class="detail-label">Kota/Kabupaten</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3  flex items-center gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label">KOTA PALEMBANG</p>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                    <p class="detail-label">Kecamatan</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3  flex items-center gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label">Jakabaring</p>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                    <p class="detail-label">Kelurahan</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3  flex items-center gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label">Silaberanti</p>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                    <p class="detail-label">Kode Pos</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3  flex items-center gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label"></p>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                    <p class="detail-label">No. Telp</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3  flex items-center gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label"></p>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                    <p class="detail-label">No. Ponsel</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3  flex items-center gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label">081276567890</p>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                    <p class="detail-label">E-mail</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3  flex items-center gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-2 lg:col-span-1">
                            <div class="grid grid-cols-12 gap-x-6 mb-10">
                                <div class="col-span-12 flex justify-between items-center">
                                    <div class="detail-title">Legal Info</div>
                                </div>
                                <div class="col-span-12 mt-2">
                                    <hr>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                    <p class="detail-label">Nama Instansi</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3 flex items-center gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label">PT. CREATE HAPUS CREATE</p>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3 flex items-start">
                                    <p class="detail-label">Alamat Lengkap</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3 lg:my-3 flex justify-start gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label flex items-start">JL. Hapus<br>KOTA PALEMBANG,
                                        Jakabaring,<br>Kel. Silaberanti, null, SUMATERA SELATAN</p>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3  flex items-center">
                                    <p class="detail-label">No. Telp</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3  flex items-center gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label"></p>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3  flex items-center">
                                    <p class="detail-label">No. Ponsel</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3  flex items-center gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label">081276567890</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-6 mt-8">
                                <div class="col-span-12 lg:col-span-6">
                                    <div class="grid grid-cols-12 gap-x-6">
                                        <div class="col-span-12 flex justify-between items-center">
                                            <div class="detail-title">Delivery Info</div>
                                        </div>
                                        <div class="col-span-12 mt-2">
                                            <hr>
                                        </div>
                                        <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                            <p class="detail-label">Opsi Pengantaran</p>
                                        </div>
                                        <div class="col-span-12 lg:col-span-8 my-3 flex items-center gap-2">
                                            <p class="hidden lg:block">:</p>
                                            <p class="detail-label">Self Pick-Up Dealer</p>
                                        </div>
                                        <div class="col-span-12">
                                            <hr>
                                        </div>
                                        <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                            <p class="detail-label">Nama</p>
                                        </div>
                                        <div class="col-span-12 lg:col-span-8 my-3 flex items-center gap-2">
                                            <p class="hidden lg:block">:</p>
                                            <p class="detail-label">ptpt</p>
                                        </div>
                                        <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                            <p class="detail-label">No. Ponsel</p>
                                        </div>
                                        <div class="col-span-12 lg:col-span-8 my-3 flex items-center gap-2">
                                            <p class="hidden lg:block">:</p>
                                            <p class="detail-label">081276567890</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-12 col-span-12 gap-x-6 mt-8">
                                <div class="col-span-12 lg:col-span-12 flex justify-between items-center">
                                    <div class="detail-title">Additional Info</div>
                                </div>
                                <div class="col-span-12 mt-2">
                                    <hr>
                                </div>
                                <div class="col-span-12 flex items-start w-full my-6 ">
                                    <p class="detail-label">Dokumen Tambahan</p>
                                </div>
                                <div class="col-span-4">
                                    <div class="relative group">
                                        <div
                                            class="relative inset-0 opacity-100 hover:opacity-50 transition-opacity cursor-pointer  flex justify-center">
                                            <img src="http://103.165.240.34:9001/storage/spk_instansi/H8kiZMtrO2ayRQhzmz2VtAsN2YZCNSrvaeZAkXih.png"
                                                alt="Image 0" class="max-h-48 object-contain">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-2 lg:col-span-1">
                            <div class="grid grid-cols-12 gap-x-6 mb-10">
                                <div class="col-span-12 flex justify-between items-center">
                                    <div class="detail-title">Contract Info</div>
                                </div>
                                <div class="col-span-12 mt-2">
                                    <hr>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                    <p class="detail-label">No Kontrak</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3 flex items-center gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label">0026/PO-INST/PAS-AH/06/2024</p>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                    <p class="detail-label">No Pembayaran</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3 flex items-center gap-2">
                                    <p class="hidden lg:block">:</p><a class="link"
                                        href="/payment/payment-instance/9c5620ca-90ca-4704-b5f9-66c24321b33b">
                                        <p class="link">0012/SPK-INSTANSI-PAYMENT/PAS-AH/06/2024</p>
                                    </a>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3 flex items-center">
                                    <p class="detail-label font-semibold">Nominal Pembayaran</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3 flex items-center gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label font-semibold">Rp 19.431.500</p>
                                </div>
                                <div class="col-span-12 lg:col-span-4 my-3  flex items-center">
                                    <p class="detail-label">Verifikasi</p>
                                </div>
                                <div class="col-span-12 lg:col-span-8 my-3  flex items-center gap-2">
                                    <p class="hidden lg:block">:</p>
                                    <p class="detail-label"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white py-6 px-5 rounded-md mt-5">
                    <div class="col-span-2">
                        <div class="grid grid-cols-12 gap-5 mt-8">
                            <div class="col-span-12 flex items-center gap-x-5">
                                <div>
                                    <p class="detail-title">Unit Info</p>
                                </div>
                            </div>
                            <div class="col-span-12">
                                <div
                                    class="relative w-full flex flex-col rounded-lg overflow-auto border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark min-h-90 min-h-25">
                                    <table
                                        class="border-collapse w-auto table-responsive whitespace-nowrap dark:border-strokedark">
                                        <thead>
                                            <tr
                                                class="border-t bg-primary-color border-stroke py-4.5 px-4 md:px-6 2xl:px-7.5">
                                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">NO
                                                </th>
                                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">
                                                    Tipe Motor</th>
                                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">
                                                    Warna</th>
                                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">
                                                    Jumlah Unit
                                                </th>
                                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">
                                                    OFF THE ROAD
                                                </th>
                                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">
                                                    BBN</th>
                                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">ON
                                                    THE ROAD
                                                </th>
                                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">
                                                    KOMISI</th>
                                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">
                                                    BOOSTER</th>
                                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">
                                                    OVER DISKON
                                                </th>
                                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">
                                                    DISC</th>
                                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">
                                                    ADDITIONAL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="border-t border-stroke py-4.5 px-4 md:px-6 2xl:px-7.5 items-center">
                                                <td
                                                    class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                                    <div class="flex items-center text-center">
                                                        <div class="mr-2 cursor-pointer"><svg
                                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"
                                                                width="18" height="18" fill="currentColor">
                                                                <path fill-rule="evenodd"
                                                                    d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z">
                                                                </path>
                                                            </svg></div><span>1</span>
                                                    </div>
                                                </td>
                                                <td
                                                    class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                                    <div class="flex items-center text-center">ALL NEW VIXION</div>
                                                </td>
                                                <td
                                                    class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                                    <div class="flex items-center text-center"><span>MERAH</span></div>
                                                </td>
                                                <td
                                                    class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                                    <div class="flex items-center text-center"><span>1</span></div>
                                                </td>
                                                <td
                                                    class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                                    <div class="flex items-center text-center"><span>Rp 24.825.000</span>
                                                    </div>
                                                </td>
                                                <td
                                                    class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                                    <div class="flex items-center text-center"><span>Rp 6.106.500</span>
                                                    </div>
                                                </td>
                                                <td
                                                    class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                                    <div class="flex items-center text-center"><span>Rp 30.931.500</span>
                                                    </div>
                                                </td>
                                                <td
                                                    class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                                    <div class="flex items-center text-center"><span>Rp 200.000</span>
                                                    </div>
                                                </td>
                                                <td
                                                    class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                                    <div class="flex items-center text-center"><span>Rp 200.000</span>
                                                    </div>
                                                </td>
                                                <td
                                                    class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                                    <div class="flex items-center text-center"><span>Rp 500.000</span>
                                                    </div>
                                                </td>
                                                <td
                                                    class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                                    <div class="flex items-center text-center"><span>Rp 1.000.000</span>
                                                    </div>
                                                </td>
                                                <td
                                                    class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                                    <div class="flex items-center text-center"><span>0</span></div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 mb-2">
                        <div class="grid grid-cols-12 gap-5 mt-8">
                            <div class="col-span-12">
                                <p class="detail-title">List Unit</p>
                            </div>
                            <div class="col-span-12">
                                <div
                                    class="relative w-full flex flex-col rounded-lg overflow-auto border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark min-h-90 min-h-25">
                                    <table
                                        class="border-collapse w-auto table-responsive whitespace-nowrap dark:border-strokedark">
                                        <thead>
                                            <tr
                                                class="border-t bg-primary-color border-stroke py-4.5 px-4 md:px-6 2xl:px-7.5">

                                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">
                                                    Tipe Motor</th>
                                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">
                                                    Warna</th>
                                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">
                                                    No. Rangka</th>
                                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">
                                                    No. Engine</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="border-t border-stroke py-4.5 px-4 md:px-6 2xl:px-7.5 items-center">

                                                <td
                                                    class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                                    <div class=" items-center text-center">ALL NEW VIXION</div>
                                                </td>
                                                <td
                                                    class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                                    <div class=" items-center text-center"><span>MERAH</span></div>
                                                </td>
                                                <td
                                                    class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                                    <div class=" items-center text-center">MH3RG4610RK153739</div>
                                                </td>
                                                <td
                                                    class="text-sm px-5 items-center text-black text-center py-2 capitalize">
                                                    <div class=" items-center text-center">G3E7E-0531420</div>
                                                </td>

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 lg:flex">
                        <div class="grid grid-cols-1 lg:flex gap-y-5 gap-x-4 "><button
                                class="btn-request w-auto cursor-not-allowed opacity-50 pointer-events-none justify-center flex items-center"
                                disabled=""
                                style="display: flex; justify-content: center; align-items: center;">DELIVERY
                                PARTIAL</button></div>
                    </div>
                    <div class="col-span-2">
                        <div class="grid grid-cols-12 gap-5 mt-8">
                            <div class="col-span-12 flex gap-x-5 items-center">
                                <div>
                                    <p class="detail-title">List Detail Additional</p>
                                </div>
                            </div>
                            <div class="col-span-12">
                                <div
                                    class="relative w-full flex flex-col rounded-lg overflow-auto border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark min-h-90 min-h-25">
                                    <table
                                        class="border-collapse w-auto table-responsive whitespace-nowrap dark:border-strokedark">
                                        <thead>
                                            <tr
                                                class="border-t bg-primary-color border-stroke py-4.5 px-4 md:px-6 2xl:px-7.5">
                                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">NO
                                                </th>
                                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">
                                                    ADDITIONAL</th>
                                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">
                                                    KET. ADDITIONAL
                                                </th>
                                                <th class="text-sm text-center font-medium text-white py-3 bg-[#00029D]">
                                                    AKSI</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="h-25 justify-center items-center">
                                                <td colspan="4" class="text-center text-black">Tidak ada data tersedia
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
