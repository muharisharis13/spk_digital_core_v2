<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GenerateNumber
{
    // public static function generate($repairType, $companyName, $table, $column)
    // {
    //     // Format bulan dan tahun saat ini
    //     $monthYear = date('m/Y');

    //     // Ambil nomor urut terakhir dari tabel
    //     $lastSequence = DB::table($table)->latest()->first();

    //     // Jika tidak ada nomor urut terakhir atau bulan berbeda, mulai dari 1
    //     if (!$lastSequence || strpos($lastSequence->$column, $monthYear) === false) {
    //         $sequence = 1;
    //     } else {
    //         // Jika bulan masih sama, ambil nomor urut terakhir dan tambahkan 1
    //         $parts = explode('/', $lastSequence->$column);
    //         $sequence = intval($parts[0]) + 1;
    //     }

    //     // Format nomor urut
    //     $sequenceString = str_pad($sequence, 4, '0', STR_PAD_LEFT);

    //     // Mengembalikan string dengan format yang diinginkan
    //     $generatedNumber = $sequenceString . '/' . $repairType . '/' . $companyName . '/' . $monthYear;

    //     // Check nomor urut di database
    //     $isUnique = self::check($generatedNumber, $table, $column);

    //     // Jika nomor urut sudah ada di database, coba generate nomor baru
    //     if (!$isUnique) {
    //         return self::generate($repairType, $companyName, $table, $column);
    //     }

    //     return $generatedNumber;
    // }

    public static function generate($repairType, $companyName, $table, $column)
    {
        // Format bulan dan tahun saat ini
        $monthYear = date('m/Y');

        // Ambil nomor urut terakhir dari tabel dengan bulan dan tahun yang sama
        $lastSequence = DB::table($table)->where($column, 'like', '%' . $monthYear)->latest()->first();

        // Jika tidak ada nomor urut terakhir atau bulan atau tahun berbeda, mulai dari 1
        if (!$lastSequence) {
            $sequence = 1;
        } else {
            // Jika ada nomor urut terakhir, ambil nomor urut terakhir dan tambahkan 1
            $parts = explode('/', $lastSequence->$column);
            $sequence = intval($parts[0]) + 1;
        }

        // Format nomor urut
        $sequenceString = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        // Mengembalikan string dengan format yang diinginkan
        $generatedNumber = $sequenceString . '/' . $repairType . '/' . $companyName . '/' . $monthYear;

        // Check nomor urut di database
        $isUnique = self::check(
            $generatedNumber,
            $table,
            $column
        );

        // Jika nomor urut sudah ada di database, coba generate nomor baru
        if (!$isUnique) {
            return self::generate($repairType, $companyName, $table, $column);
        }

        return $generatedNumber;
    }



    public static function check($repairNumber, $table, $column)
    {
        // Pisahkan nomor urut dari string yang diberikan
        $parts = explode('/', $repairNumber);
        $sequenceString = $parts[0];

        // Ambil nomor urut dari database
        $repairNumberRecord = DB::table($table)->where("$column", $sequenceString)->first();

        if ($repairNumberRecord) {
            // Jika nomor urut ditemukan di database, kembalikan false
            return false;
        } else {
            // Jika nomor urut tidak ditemukan di database, kembalikan true
            return true;
        }
    }
}
