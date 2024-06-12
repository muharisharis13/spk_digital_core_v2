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
        "read_retur_unit_detail",
        "put_retur_unit",
        "delete_retur_unit",
        "put_confirm_return_unit",
        "delete_unit_list"
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
        "read_event_master",
        "get_sales_master",
        "get_microfinance_master",
        "get_leasing_master",
        "get_color_master",
        "get_bank_master",
        "post_bank_master",
        "delete_bank_master",
        "put_status_bank_master",
        "put_bank_master",
        "get_marital_master",
        "get_hobby_master",
        "get_tenor_master",
        "get_motor_brand_master",
        "get_motor_detail_master"
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
        "post_event",
        "put_event",
        "put_status_event",
        "delete_unit_event",
        "delete_event"
    ];

    const permissionEventReturn = [
        "post_return_event",
        "get_return_event",
        "get_detail_return_event",
        "put_status_return_event",
        "put_return_event",
        "delete_return_event",
        "delete_unit_return_event",
        "get_unit_return_event"
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
        "post_neq",
        "put_status_neq",
        "put_neq",
        "delete_neq",
        "delete_unit_neq"
    ];

    const permissionNeqReturn = [
        "read_neq_return",
        "read_neq_detail_return",
        "post_neq_return",
        "put_neq_return",
        "delete_neq_return",
        "put_status_neq_return",
        "delete_unit_neq_return",
        "get_unit_neq_return"
    ];

    const permissionIndent = [
        "read_indent",
        "read_indent_detail",
        "post_indent",
        "put_indent",
        "post_payment_indent",
        "pit_status_indent",
        "delete_payment_indent",
        "put_refund_all_payment_indent",
        "put_cancel_payment_indent"
    ];

    const permissionSPK = [
        "read_spk",
        "read_spk_detail",
        "post_spk",
        "put_spk",
        "delete_spk",
        "put_status_spk",
        "post_shipment_spk",
        "post_cro_spk",
        "post_purchase_order_spk",
        "put_act_tac_purchase_order_spk",
        "delete_purchase_order",
        "delete_dcmt_another",
        "delete_dcmt_file_sk",
        "delete_price_accessories",
        "get_excess_payment",
        "get_detail_excess_payment",
        "get_payment_spk",
        "get_detail_payment_spk",
        "post_payment_spk",
        "delete_payment_spk",
        "refund_payment_spk",
        "put_status_payment_spk",
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

    const permissionDelivery = [
        "post_delivery",
        "get_delivery",
        "get_detail_delivery",
        "put_status_delivery",
        "put_delivery",
        "delete_delivery"
    ];

    const permissionIndentInstansi = [
        "get_indent_inst",
        "post_indent_inst",
        "put_indent_inst",
        "put_status_indent_inst",
        "get_detail_indent_inst",
        "post_payment_indent_inst",
        "put_cancel_payment_indent_inst",
        "delete_payment_indent_inst",
        "put_refund_all_payment_indent_inst",
        "add_cro",
        "add_cro_inst"
    ];

    const permissionPoInstansi = [
        "get_po_inst",
        "get_detail_po_inst",
        "post_po_inst",
        "put_po_inst",
        "post_add_motor_po_inst",
        "put_motor_po_inst",
        "delete_motor_po_inst",
        "post_add_unit_po_inst",
        "put_unit_po_inst",
        "delete_unit_po_inst",
        "post_additional_po_inst",
        "put_additional_po_inst",
        "delete_additional_po_inst",
        "delete_additional_file_po_inst",
        "post_status_po_inst",
        "post_publish_po_inst",
        "cancel_po_inst",
        "delete_po_inst",
        "delete_delivery_file_po_inst",
        "delete_delivery_uni_file_po_inst",
        "post_payment_po_inst",
        "delete_payment_po_inst",
        "get_detail_payment_po_inst",
        "refund_payment_po_inst",
        "get_payment_po_inst",
        "post_add_legal_po_inst",
        "post_delivery_po_inst",
        "get_spk_inst",
        "get_detail_spk_inst",
        "update_status_payment_spk_inst"
    ];

    const permissionUser = [
        "post_assign_permission_user",
        "get_permission_user",
        "get_current_dealer_user",
        "get_role_user",
        "put_status_user",
        "get_user",
        "get_detail_user",
        "post_user",
        "put_user",
        "remove_permission_user"
    ];

    const permissionPricelist = [
        "post_pricelist",
        "get_pricelist",
        "get_detail_pricelist",
        "post_clone_pricelist",
        "put_pricelist"
    ];





    public static function AllPermission()
    {
        return array_merge(self::permissionUnit, self::permissionShippingOrder, self::permissionMaster, self::permissionRepair, self::permissionReturUnit, self::permissionRepairReturn, self::permissionEvent, self::permissionTfEvent, self::permissionTfEventReturn, self::permissionNeq, self::permissionNeqReturn, self::permissionIndent, self::permissionSPK, self::permissionSPKPayment, self::permissionExcessSPK, self::permissionDelivery, self::permissionEventReturn, self::permissionIndentInstansi, self::permissionPoInstansi, self::permissionIndentInstansi, self::permissionPricelist, self::permissionUser);
    }
}
