<?php

namespace App\Helpers;

use Carbon\Carbon;

class FormatDate
{
    public static function formatDateYYYMMDD($date)
    {
        $date = Carbon::createFromFormat('Ymd', $date);
        $formattedDate = $date->format('Y-m-d'); // Menghasilkan: 2024-02-15

        return $formattedDate;
    }
}
