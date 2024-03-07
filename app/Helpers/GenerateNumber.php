<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class GenerateNumber
{
    public static function generate($repairType, $companyName)
    {
        // Mendapatkan nomor urut dari cache, defaultnya 0
        $sequence = Cache::get('sequence', 0);

        // Increment nomor urut
        $sequence++;

        // Jika bulan berbeda dengan bulan sebelumnya, reset nomor urut menjadi 1
        $currentMonth = date('m');
        $lastMonth = Cache::get('last_month');
        if ($currentMonth != $lastMonth) {
            $sequence = 1;
            Cache::put('last_month', $currentMonth);
        }

        // Format nomor urut
        $sequenceString = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        // Format bulan dan tahun
        $monthYear = date('m/Y');

        // Menyimpan nomor urut ke dalam cache
        Cache::put('sequence', $sequence);

        // Mengembalikan string dengan format yang diinginkan
        return $sequenceString . '/' . $repairType . '/' . $companyName . '/' . $monthYear;
    }
}
