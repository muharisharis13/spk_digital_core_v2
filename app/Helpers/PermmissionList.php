<?php

namespace App\Helpers;

class PermmissionList
{
    const permissionDashboard = [
        "dashboard.view_dashboard_data",
    ];

    const permissionInventory = [
        "inventory.shipping_order.sync_data",
        "inventory.shipping_order.read_data",
        "inventory.stock.read_stock_detail",
        "inventory.return.add_new_return",
        "inventory.return.read_return_detail",
        "inventory.return.edit_return",
        "inventory.return.confirm_return",
        "inventory.return.delete_return",
        "inventory.repair.add_new_repair",
        "inventory.repair.read_surat_jalan",
        "inventory.repair.read_repair_detail",
        "inventory.repair.edit_repair",
        "inventory.repair.confirm_repair",
        "inventory.repair.delete_repair",
        "inventory.repair.add_new_surat_jalan",
        "inventory.repair_return.add_new_finish",
        "inventory.repair_return.read_surat_jalan",
        "inventory.repair_return.read_repair_return_detail",
        "inventory.repair_return.edit_repair_return",
        "inventory.repair_return.confirm_repair_return",
        "inventory.repair_return.delete_repair_return",
        "inventory.repair_return.print_repair_return"
    ];

    const permissionEvent = [
        "event.list_event",
        "event.add_new_payment",
        "event.status_event",
        "event.read_event_detail",
        "event.edit_event",
        "event.transfer_event",
        "event.transfer_event.read_surat_jalan",
        "event.transfer_event.read_transfer_detail",
        "event.transfer_event.edit_transfer",
        "event.transfer_event.confirm_transfer",
        "event.transfer_event.delete_transfer",
        "event.transfer_event.print_transfer",
        "event.return_event",
        "event.return_event.read_surat_jalan",
        "event.return_event.read_return_detail",
        "event.return_event.edit_return",
        "event.return_event.confirm_return",
        "event.return_event.delete_return",
        "event.return_event.print_return"
    ];

    const permissionNeq = [
        "neq.transfer_neq",
        "neq.transfer_neq.read_surat_jalan",
        "neq.transfer_neq.read_transfer_detail",
        "neq.transfer_neq.edit_transfer",
        "neq.transfer_neq.confirm_transfer",
        "neq.transfer_neq.delete_transfer",
        "neq.transfer_neq.return_neq",
        "neq.transfer_neq.return_neq.read_surat_jalan",
        "neq.transfer_neq.return_neq.read_return_detail",
        "neq.transfer_neq.return_neq.edit_return",
        "neq.transfer_neq.return_neq.confirm_return",
        "neq.transfer_neq.return_neq.delete_return"
    ];

    const permissionTransaction = [
        "transaction.instance_indent.add_new_indent",
        "transaction.instance_indent.read_instance_detail",
        "transaction.instance_indent.edit_instance",
        "transaction.instance_indent.payment_instance",
        "transaction.instance_indent.cancel_instance",
        "transaction.instance_indent.print_instance",
        "transaction.instance_indent.cashier_approve_instance",
        "transaction.instance_indent.finance_approve_instance",
        "transaction.instance_indent.refund_instance",
        "transaction.instance_indent.delete_payment",
        "transaction.instance_indent.print_payment",
        "transaction.instance.add_new_po",
        "transaction.instance.read_instance_detail",
        "transaction.instance.edit_general_info",
        "transaction.instance.edit_legal_info",
        "transaction.instance.edit_delivery_info",
        "transaction.instance.edit_additional_info",
        "transaction.instance.edit_po",
        "transaction.instance.finance_approve_po",
        "transaction.instance.void_po",
        "transaction.instance.publish_sph",
        "transaction.unit_info.read_unit",
        "transaction.unit_info.add_unit",
        "transaction.unit_info.edit_unit",
        "transaction.unit_info.delete_unit",
        "transaction.unit_info.add_frame",
        "transaction.unit_list.read_unit_list",
        "transaction.unit_list.add_legal_info",
        "transaction.unit_list.delivery_per_unit",
        "transaction.unit_list.edit_additional_info",
        "transaction.regular_indent.add_new_indent",
        "transaction.regular_indent.read_surat_jalan",
        "transaction.regular_indent.read_indent_detail",
        "transaction.regular_indent.edit_indent",
        "transaction.regular_indent.confirm_indent",
        "transaction.regular_indent.delete_indent",
        "transaction.regular_indent.print_indent",
        "transaction.regular_spk.add_new_spk",
        "transaction.regular_spk.read_surat_jalan",
        "transaction.regular_spk.read_spk_detail",
        "transaction.regular_spk.edit_spk",
        "transaction.regular_spk.confirm_spk",
        "transaction.regular_spk.delete_spk",
        "transaction.regular_spk.print_spk",
        "transaction.spk.read_spk",
        "transaction.spk.edit_spk",
        "transaction.spk.finance_approve_spk",
        "transaction.spk.shipment",
        "transaction.spk.surat_jalan",
        "transaction.spk.void_spk"
    ];

    const permissionPayment = [
        "payment.overpayment.read_payment",
        "payment.overpayment.print_payment",
        "payment.instance_payment.read_instance_payment",
        "payment.instance_payment.edit_instance_payment",
        "payment.instance_payment.payment_instance_payment",
        "payment.instance_payment.cashier_approve_instance_payment",
        "payment.instance_payment.finance_approve_instance_payment",
        "payment.instance_payment.refund_instance_payment",
        "payment.instance_payment.delete_payment",
        "payment.instance_payment.print_payment",
        "payment.regular_payment.read_regular_payment",
        "payment.regular_payment.edit_regular_payment",
        "payment.regular_payment.payment_regular_payment",
        "payment.regular_payment.cashier_approve_regular_payment",
        "payment.regular_payment.finance_approve_regular_payment",
        "payment.regular_payment.refund_regular_payment",
        "payment.regular_payment.delete_payment",
        "payment.regular_payment.print_payment"
    ];

    const permissionCustomer = [
        "customer.edit_general_info",
        "customer.edit_transaction_info",
        "customer.edit_unit_info"
    ];

    const permissionPricing = [
        "pricing.edit_pricing_info",
        "pricing.edit_off_the_road",
        "pricing.edit_bbn"
    ];

    const permissionDelivery = [
        "delivery.edit_delivery_info",
    ];

    const permissionPoLeasing = [
        "po_leasing.create_po",
        "po_leasing.reset_po",
        "po_leasing.actual_tac_po"
    ];

    const permissionCRO = [
        "cro.check_cro",
        "cro.detail_cro"
    ];

    const permissionMaster = [
        "master.dealer_list.read_dealer",
        "master.dealer_list.sync_dealer",
        "master.neq_list.read_neq_list",
        "master.role_list.read_role_list",
        "master.role_list.add_new_role",
        "master.bank_list.read_bank_list",
        "master.bank_list.add_new_bank",
        "master.bank_list.edit_bank_detail",
        "master.pricelist.clone_pricelist",
        "master.pricelist.read_pricelist",
        "master.pricelist.edit_pricelist_detail"
    ];

    const permissionUser = [
        "user_list.add_new_user",
        "user_list.read_user_detail",
        "user_list.deactivate_user",
        "user_list.reset_password",
        "user_list.edit_user",
        "user_list.read_permission_user"
    ];

    public static function AllPermission()
    {
        return array_merge(
            self::permissionDashboard,
            self::permissionInventory,
            self::permissionEvent,
            self::permissionNeq,
            self::permissionTransaction,
            self::permissionPayment,
            self::permissionCustomer,
            self::permissionPricing,
            self::permissionDelivery,
            self::permissionPoLeasing,
            self::permissionCRO,
            self::permissionMaster,
            self::permissionUser
        );
    }
}
