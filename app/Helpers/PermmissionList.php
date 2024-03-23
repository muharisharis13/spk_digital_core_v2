<?php

namespace App\Helpers;


class PermmissionList
{
    const permissionUnit = [
        "read_unit",
        "read_unit_detail",
        "update_unit_status",
        "read_unit_detail"
    ];


    public static function AllPermission()
    {
        return array_merge(self::permissionUnit);
    }
}
