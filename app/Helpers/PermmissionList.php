<?php

namespace App\Helpers;


class PermmissionList
{
    const permissionUnit = [
        "read_unit",
        "read_unit_detail",
        "update_unit_status"
    ];

    const permissionShippingOrder = [
        "post_sync_data",
        "read_shipping_order",
        "read_shipping_order_detail",
    ];

    const permissionReturUnit = [
        "read_retur_unit",
        "post_retur_unit",
        "read_retur_unit_detail"
    ];


    const permissionMaster = [
        "read_motor_master",
        "read_dealer_neq_master",
        "read_mds_master",
        "read_location_current_master",
        "read_main_dealer_master",
        "post_event_create_master",
        "read_event_detail_master",
        "update_event_master",
        "update_event_status_master",
        "read_event_master"
    ];

    const permissionRepair = [
        "post_repair_create",
        "update_repair",
        "update_repair_status",
        "delete_reapair",
        "delete_repair_unit",
        "read_repair_detail",
        "read_repair",
        "post_repair_return_create",
        "update_repair_return",
        "update_repair_return_status",
        "delete_repair_return_unit",
        "delete_repair_return",
        "read_repair_return_unit"
    ];

    const permissionRepairReturn = [
        "read_repair_return",
        "post_repair_return",
        "read_repair_return_detail",
    ];

    const permissionEvent = [
        "read_event",
        "read_event_detail",
        "post_event"
    ];

    const permissionTfEvent = [
        "read_tf_event",
        "read_tf_event_detail",
        "post_tf_event"
    ];

    const permissionTfEventReturn = [
        "read_tf_event_return",
        "read_tf_event_detail_return",
        "post_tf_event_return"
    ];

    const permissionNeq = [
        "read_neq",
        "read_neq_detail",
        "post_neq"
    ];

    const permissionNeqReturn = [
        "read_neq_return",
        "read_neq_detail_return",
        "post_neq_return"
    ];

    const permissionIndent = [
        "read_indent",
        "read_indent_detail",
        "post_indent"
    ];

    const permissionSPK = [
        "read_spk",
        "read_spk_detail",
        "post_spk"
    ];

    const permissionSPKPayment = [
        "read_spk_payment",
        "read_spk_paymentd_detail",
        "post_spk_paymentd",
    ];

    const permissionExcessSPK = [
        "read_excess_spk",
        "read_excess_spk_detail",
    ];


    public static function AllPermission()
    {
        return array_merge(self::permissionUnit, self::permissionShippingOrder, self::permissionMaster, self::permissionRepair, self::permissionReturUnit, self::permissionRepairReturn, self::permissionEvent, self::permissionTfEvent, self::permissionTfEventReturn, self::permissionNeq, self::permissionNeqReturn, self::permissionIndent, self::permissionSPK, self::permissionSPKPayment, self::permissionExcessSPK);
    }
}
