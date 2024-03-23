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

    const permissionShippingOrder = [
        "post_sync_data",
        "read_shipping_order",
        "read_shipping_order_detail"
    ];


    const permissionMaster = [
        "read_motor",
        "read_dealer_neq",
        "read_mds",
        "read_location_current",
        "read_main_dealer",
        "post_event_create",
        "read_event_detail",
        "update_event",
        "update_event_status",
        "read_event"
    ];

    const permissionRepair = [
        "post_repair_create",
        "update_repair",
        "update_repair_status",
        "delete_reapair",
        "delete_repair_unit",
        "read_repair_detail",
        "read_repair",
        "read_repair_return",
        "post_repair_return_create",
        "update_repair_return",
        "update_repair_return_status",
        "read_repair_return_detail",
        "delete_repair_return_unit",
        "delete_repair_return",
        "read_repair_return_unit"
    ];


    public static function AllPermission()
    {
        return array_merge(self::permissionUnit);
    }
}
