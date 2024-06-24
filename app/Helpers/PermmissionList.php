<?php

namespace App\Helpers;

class PermmissionList
{
    const permissionDashboard = [
        [
            "name" => "dashboard.data",
            "alias_name" => "dasboard",
            "group_name" => "dashboard"
        ]
    ];

    const permissionInventory = [
        [
            "name" => "inventory.shipping_order.read_data",
            "alias_name" => "Shipping order",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.shipping_order.sync_data",
            "alias_name" => "Shipping order sync data",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.shipping_order.detail",
            "alias_name" => "Shipping order detail",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.shipping_order.detail.terima_unit",
            "alias_name" => "Shipping order detail accept unit",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.stock.data",
            "alias_name" => "stock data",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.stock.detail",
            "alias_name" => "stock detail",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.return.data",
            "alias_name" => "return data",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.return.add_new_return",
            "alias_name" => "return add new return",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.return.detail",
            "alias_name" => "return detail",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.return.detail.edit",
            "alias_name" => "return detail edit",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.return.detail.confirm",
            "alias_name" => "return detail confirm",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.return.detail.delete",
            "alias_name" => "return detail delete",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.repair.data",
            "alias_name" => "repair data",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.repair.add_new_repair",
            "alias_name" => "repair add new repair",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.repair.surat_jalan.data",
            "alias_name" => "repair delivery",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.repair.surat_jalan.detail",
            "alias_name" => "repair surat delivery",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.repair.surat_jalan.detail.edit",
            "alias_name" => "repair surat delivery edit",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.repair.surat_jalan.detail.confirm",
            "alias_name" => "repair surat delivery confirm",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.repair.surat_jalan.detail.delete",
            "alias_name" => "repair surat delivery delete",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.repair.surat_jalan.detail.print",
            "alias_name" => "repair surat delivery print",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.repair.detail",
            "alias_name" => "repair detail",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.repair.detail.edit",
            "alias_name" => "repair detail edit",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.repair.detail.confirm",
            "alias_name" => "repair  detail confirm",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.repair.detail.delete",
            "alias_name" => "repair read detail delete",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.repair.detail.add_new_surat_jalan",
            "alias_name" => "repair detail add new delivery",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.finish_repair.data",
            "alias_name" => "finish repair data",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.finish_repair.add_new_finish",
            "alias_name" => "finish repair add new finish",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.finish_repair.list_surat_jalan",
            "alias_name" => "finish repair list delivery",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.finish_repair.list_surat_jalan.detail",
            "alias_name" => "finish repair list delivery detail",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.finish_repair.list_surat_jalan.detail.edit",
            "alias_name" => "finish repair list delivery detail edit",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.finish_repair.list_surat_jalan.detail.confirm",
            "alias_name" => "finish repair list delivery detail confirm",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.finish_repair.list_surat_jalan.detail.delete",
            "alias_name" => "finish repair list delivery detail delete",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.finish_repair.list_surat_jalan.detail.print",
            "alias_name" => "finish repair list delivery detail print",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.finish_repair.detail",
            "alias_name" => "finish repair detail",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.finish_repair.detail.edit",
            "alias_name" => "finish repair detail edit",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.finish_repair.detail.confirm",
            "alias_name" => "finish repair detail confirm",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.finish_repair.detail.delete",
            "alias_name" => "finish repair detail delete",
            "group_name" => "inventory"
        ],
        [
            "name" => "inventory.finish_repair.detail.add_new_surat_jalan",
            "alias_name" => "finish repair detail add new delivery",
            "group_name" => "inventory"
        ],
    ];

    const permissionTransaction = [
        [
            "name" => "transaction.instansi_indent.data",
            "alias_name" => "instansi indent data",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi_indent.add_new_indent",
            "alias_name" => "instansi indent add new indent",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi_indent.detail",
            "alias_name" => "instansi indent detail",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi_indent.detail.edit",
            "alias_name" => "instansi indent detail edit",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi_indent.detail.payment",
            "alias_name" => "instansi indent detail payment",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi_indent.detail.cancel",
            "alias_name" => "instansi indent detail cancel",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi_indent.detail.print",
            "alias_name" => "instansi indent detail print",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi_indent.detail.cashier_approve",
            "alias_name" => "instansi indent detail cashier approve",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi_indent.detail.finance_approve",
            "alias_name" => "instansi indent detail finance approve",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi_indent.detail.refund",
            "alias_name" => "instansi indent detail refund",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi_indent.detail.delete_payment",
            "alias_name" => "instansi indent detail delete payment",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi_indent.detail.print_payment",
            "alias_name" => "instansi indent detail print payment",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.data",
            "alias_name" => "instansi data",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.add_new_po",
            "alias_name" => "instansi add new po",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail",
            "alias_name" => "instansi detail",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.edit_general_info",
            "alias_name" => "instansi detail edit general info",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.edit_legal_info",
            "alias_name" => "instansi detail edit legal info",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.edit_delivery_info",
            "alias_name" => "instansi detail edit delivery info",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.edit_additional_info",
            "alias_name" => "instansi detail edit additional_info",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.delete_po",
            "alias_name" => "instansi detail delete po",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.print",
            "alias_name" => "instansi detail print",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.finance_approve",
            "alias_name" => "instansi detail finance approve",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.void_po",
            "alias_name" => "instansi detail void po",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.shipment",
            "alias_name" => "instansi detail shipment",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.publish_spk",
            "alias_name" => "instansi detail publish spk",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.delivery",
            "alias_name" => "instansi detail delivery",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.add_unit_info",
            "alias_name" => "instansi detail add unit info",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.edit_unit_info",
            "alias_name" => "instansi detail edit unit info",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.delete_unit_info",
            "alias_name" => "instansi detail delete unit info",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.add_frame_unit_info",
            "alias_name" => "instansi detail add frame unit info",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.edit_list_unit",
            "alias_name" => "instansi detail edit list unit",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.delete_list_unit",
            "alias_name" => "instansi detail delete list unit",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.add_legal_info_list_unit",
            "alias_name" => "instansi detail add legal info list unit",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.delivery_partial_list_unit",
            "alias_name" => "instansi detail delivery partial list unit",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.add_list_additional_cost",
            "alias_name" => "instansi detail add list_additional cost",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.edit_list_additional_cost",
            "alias_name" => "instansi detail edit list additional cost",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi.detail.delete_list_additional_cost",
            "alias_name" => "instansi detail delete list additional cost",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi_spk.data",
            "alias_name" => "instansi spk data",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi_spk.detail",
            "alias_name" => "instansi spk detail",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi_spk.detail.print",
            "alias_name" => "instansi spk detail print",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi_spk.list_surat_jalan",
            "alias_name" => "instansi spk list delivery",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi_spk.list_surat_jalan.detail",
            "alias_name" => "instansi spk list delivery detail",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi_spk.list_surat_jalan.detail.edit",
            "alias_name" => "instansi spk list delivery detail edit",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi_spk.list_surat_jalan.detail.confirm",
            "alias_name" => "instansi spk list delivery detail confirm",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi_spk.list_surat_jalan.detail.delete",
            "alias_name" => "instansi spk list delivery detail delete",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.instansi_spk.list_surat_jalan.detail.print",
            "alias_name" => "instansi spk list delivery detail print",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_indent.data",
            "alias_name" => "regular indent data",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_indent.add_new_indent",
            "alias_name" => "regular indent add new indent",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_indent.detail",
            "alias_name" => "regular indent detail",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_indent.detail.edit",
            "alias_name" => "regular indent detail edit",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_indent.detail.payment",
            "alias_name" => "regular indent detail payment",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_indent.detail.cancel",
            "alias_name" => "regular indent detail cancel",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_indent.detail.print",
            "alias_name" => "regular indent detail print",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_indent.detail.cashier_approve",
            "alias_name" => "regular indent detail cashier approve",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_indent.detail.finance_approve",
            "alias_name" => "regular indent detail finance approve",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_indent.detail.refund",
            "alias_name" => "regular indent detail refund",
            "group_name" => "transaction"
        ],
        [
            "name"  => "transaction.regular_indent.detail.delete_list_detail_payment",
            "alias_name" => "regular indent detail delete list detail payment",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_indent.detail.print_list_detail_payment",
            "alias_name" => "regular indent detail print list detail payment",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.data",
            "alias_name" => "regular spk data",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.add_new_spk",
            "alias_name" => "regular spk add new spk",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.list_surat_jalan",
            "alias_name" => "regular spk list delivery",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.list_surat_jalan.detail",
            "alias_name" => "regular spk list delivery detail",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.list_surat_jalan.detail.edit",
            "alias_name" => "regular spk list delivery detail edit",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.list_surat_jalan.detail.confirm",
            "alias_name" => "regular spk list delivery detail confirm",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.list_surat_jalan.detail.delete",
            "alias_name" => "regular spk list delivery detail delete",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.list_surat_jalan.detail.print",
            "alias_name" => "regular spk list delivery detail print",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.detail",
            "alias_name" => "regular spk detail",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.detail.delete_spk",
            "alias_name" => "regular spk detail delete spk",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.detail.print",
            "alias_name" => "regular spk detail print",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.detail.finance_approve",
            "alias_name" => "regular spk detail finance approve",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.detail.shipment",
            "alias_name" => "regular spk detail shipment",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.detail.surat_jalan",
            "alias_name" => "regular spk detail delivery",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.detail.void_spk",
            "alias_name" => "regular spk detail void spk",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.detail.edit_general_info",
            "alias_name" => "regular spk detail edit general info",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.detail.edit_transaction_inof",
            "alias_name" => "regular spk detail edit transaction info",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.detail.edit_unit_info",
            "alias_name" => "regular spk detail edit unit info",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.detail.edit_customer_info",
            "alias_name" => "regular spk detail edit customer info",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.detail.edit_legal_info",
            "alias_name" => "regular spk detail edit legal info",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.detail.edit_additional_info",
            "alias_name" => "regular spk detail edit additional info",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.detail.edit_pricing_info",
            "alias_name" => "regular spk detail edit pricing info",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.detail.edit_pricing_info.edit_off_the_road",
            "alias_name" => "regular spk detail edit pricing info off the road",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.detail.edit_pricing_info.edit_bbn",
            "alias_name" => "regular spk detail edit pricing info bbn",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.detail.edit_delivery_info",
            "alias_name" => "regular spk detail edit delivery info",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.detail.create_po_leasing",
            "alias_name" => "regular spk detail create po leasing",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.detail.reset_po_leasing",
            "alias_name" => "regular spk detail reset po leasing",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.regular_spk.detail.actual_tac",
            "alias_name" => "regular spk detail actual_tac",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.cro.data",
            "alias_name" => "cro data",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.cro.detail",
            "alias_name" => "cro detail",
            "group_name" => "transaction"
        ],
        [
            "name" => "transaction.cro.detail.cro_check",
            "alias_name" => "cro detail cro check",
            "group_name" => "transaction"
        ],
    ];

    const permissionPayment = [
        [
            "name" => "payment.over_payment.data",
            "alias_name" => "over payment data",
            "group_name" => "payment"
        ],
        [
            "name" => "payment.over_payment.detail",
            "alias_name" => "over payment detail",
            "group_name" => "payment"
        ],
        [
            "name" => "payment.over_payment.detail.print",
            "alias_name" => "over payment detail print",
            "group_name" => "payment"
        ],
        [
            "name" => "payment.instansi_payment.data",
            "alias_name" => "instansi payment data",
            "group_name" => "payment"
        ],
        [
            "name" => "payment.instansi_payment.detail",
            "alias_name" => "instansi payment detail",
            "group_name" => "payment"
        ],
        [
            "name" => "payment.instansi_payment.detail.print",
            "alias_name" => "instansi payment detail print",
            "group_name" => "payment"
        ],
        [
            "name" => "payment.instansi_payment.detail.payment",
            "alias_name" => "instansi payment detail payment",
            "group_name" => "payment"
        ],
        [
            "name" => "payment.instansi_payment.detail.cashier_approve",
            "alias_name" => "instansi payment detail cashier approve",
            "group_name" => "payment"
        ],
        [
            "name" => "payment.instansi_payment.detail.finance_approve",
            "alias_name" => "instansi payment detail finance approve",
            "group_name" => "payment"
        ],
        [
            "name" => "payment.instansi_payment.detail.refund",
            "alias_name" => "instansi payment detail refund",
            "group_name" => "payment"
        ],
        [
            "name" => "payment.instansi_payment.detail.delete_list_detail_payment",
            "alias_name" => "instansi payment detail delete list detail payment",
            "group_name" => "payment"
        ],
        [
            "name" => "payment.instansi_payment.detail.print_list_detail_payment",
            "alias_name" => "instansi payment detail print list detail payment",
            "group_name" => "payment"
        ],


        [
            "name" => "payment.regular_payment.data",
            "alias_name" => "regular payment data",
            "group_name" => "payment"
        ],
        [
            "name" => "payment.regular_payment.detail",
            "alias_name" => "regular payment detail",
            "group_name" => "payment"
        ],
        [
            "name" => "payment.regular_payment.detail.print",
            "alias_name" => "regular payment detail print",
            "group_name" => "payment"
        ],
        [
            "name" => "payment.regular_payment.detail.payment",
            "alias_name" => "regular payment detail payment",
            "group_name" => "payment"
        ],
        [
            "name" => "payment.regular_payment.detail.cashier_approve",
            "alias_name" => "regular payment detail cashier approve",
            "group_name" => "payment"
        ],
        [
            "name" => "payment.regular_payment.detail.finance_approve",
            "alias_name" => "regular payment detail finance approve",
            "group_name" => "payment"
        ],
        [
            "name" => "payment.regular_payment.detail.refund",
            "alias_name" => "regular payment detail refund",
            "group_name" => "payment"
        ],
        [
            "name" => "payment.regular_payment.detail.delete_list_detail_payment",
            "alias_name" => "regular payment detail delete list detail payment",
            "group_name" => "payment"
        ],
        [
            "name" => "payment.regular_payment.detail.print_list_detail_payment",
            "alias_name" => "regular payment detail print list detail payment",
            "group_name" => "payment"
        ],
    ];

    const permissionEvent = [
        [
            "name" => "event.list.data",
            "alias_name" => "list data",
            "group_name" => "event"
        ],
        [
            "name" => "event.list.add_new_event",
            "alias_name" => "list add new event",
            "group_name" => "event"
        ],
        [
            "name" => "event.list.status_event",
            "alias_name" => "list status event",
            "group_name" => "event"
        ],
        [
            "name" => "event.list.detail",
            "alias_name" => "list detail",
            "group_name" => "event"
        ],
        [
            "name" => "event.list.detail.edit",
            "alias_name" => "list detail edit",
            "group_name" => "event"
        ],
        [
            "name" => "event.transfer.data",
            "alias_name" => "transfer data",
            "group_name" => "event"
        ],
        [
            "name" => "event.transfer.transfer_event",
            "alias_name" => "transfer event",
            "group_name" => "event"
        ],
        [
            "name" => "event.transfer.list_surat_jalan",
            "alias_name" => "transfer list delivery",
            "group_name" => "event"
        ],
        [
            "name" => "event.transfer.list_surat_jalan.detail",
            "alias_name" => "transfer list delivery detail",
            "group_name" => "event"
        ],
        [
            "name" => "event.transfer.list_surat_jalan.detail.edit",
            "alias_name" => "transfer list delivery detail edit",
            "group_name" => "event"
        ],
        [
            "name" => "event.transfer.list_surat_jalan.detail.confirm",
            "alias_name" => "transfer list delivery detail confirm",
            "group_name" => "event"
        ],
        [
            "name" => "event.transfer.list_surat_jalan.detail.delete",
            "alias_name" => "transfer list delivery detail delete",
            "group_name" => "event"
        ],
        [
            "name" => "event.transfer.list_surat_jalan.detail.print",
            "alias_name" => "transfer list delivery detail print",
            "group_name" => "event"
        ],
        [
            "name" => "event.transfer.detail",
            "alias_name" => "transfer detail",
            "group_name" => "event"
        ],
        [
            "name" => "event.transfer.detail.edit",
            "alias_name" => "transfer detail edit",
            "group_name" => "event"
        ],
        [
            "name" => "event.transfer.detail.confirm",
            "alias_name" => "transfer detail confirm",
            "group_name" => "event"
        ],
        [
            "name" => "event.transfer.detail.delete",
            "alias_name" => "transfer detail delete",
            "group_name" => "event"
        ],
        [
            "name" => "event.transfer.detail.surat_jalan",
            "alias_name" => "transfer detail delivery",
            "group_name" => "event"
        ],
        [
            "name" => "event.return.data",
            "alias_name" => "return data",
            "group_name" => "event"
        ],
        [
            "name" => "event.return.kembali_event",
            "alias_name" => "return event",
            "group_name" => "event"
        ],
        [
            "name" => "event.return.list_surat_jalan",
            "alias_name" => "return list delivery",
            "group_name" => "event"
        ],
        [
            "name" => "event.return.list_surat_jalan.detail",
            "alias_name" => "return list delivery detail",
            "group_name" => "event"
        ],
        [
            "name" => "event.return.list_surat_jalan.detail.edit",
            "alias_name" => "return list delivery detail edit",
            "group_name" => "event"
        ],
        [
            "name" => "event.return.list_surat_jalan.detail.confirm",
            "alias_name" => "return list delivery detail confirm",
            "group_name" => "event"
        ],
        [
            "name" => "event.return.list_surat_jalan.detail.delete",
            "alias_name" => "return list delivery detail delete",
            "group_name" => "event"
        ],
        [
            "name" => "event.return.list_surat_jalan.detail.print",
            "alias_name" => "return list delivery detail print",
            "group_name" => "event"
        ],
        [
            "name" => "event.return.detail",
            "alias_name" => "return detail",
            "group_name" => "event"
        ],
        [
            "name" => "event.return.detail.edit",
            "alias_name" => "return detail edit",
            "group_name" => "event"
        ],
        [
            "name" => "event.return.detail.confirm",
            "alias_name" => "return detail confirm",
            "group_name" => "event"
        ],
        [
            "name" => "event.return.detail.delete",
            "alias_name" => "return detail delete",
            "group_name" => "event"
        ],
        [
            "name" => "event.return.detail.surat_jalan",
            "alias_name" => "return detail delivery",
            "group_name" => "event"
        ],
    ];

    const permissionNeq = [
        [
            "name" => "neq.transfer.data",
            "alias_name" => "transfer data",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.transfer.transfer_neq",
            "alias_name" => "transfer neq",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.transfer.list_surat_jalan",
            "alias_name" => "transfer delivery",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.transfer.list_surat_jalan.detail",
            "alias_name" => "transfer delivery detail",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.transfer.list_surat_jalan.detail.edit",
            "alias_name" => "transfer delivery detail edit",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.transfer.list_surat_jalan.detail.confirm",
            "alias_name" => "transfer delivery detail confirm",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.transfer.list_surat_jalan.detail.delete",
            "alias_name" => "transfer delivery detail delete",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.transfer.list_surat_jalan.detail.print",
            "alias_name" => "transfer delivery detail print",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.transfer.detail",
            "alias_name" => "transfer detail",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.transfer.detail.edit",
            "alias_name" => "transfer detail edit",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.transfer.detail.confirm",
            "alias_name" => "transfer detail confirm",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.transfer.detail.delete",
            "alias_name" => "transfer detail delete",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.transfer.detail.surat_jalan",
            "alias_name" => "transfer detail delivery",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.return.data",
            "alias_name" => "return data",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.return.kembali_neq",
            "alias_name" => "return neq",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.return.list_surat_jalan",
            "alias_name" => "return delivery",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.return.list_surat_jalan.detail",
            "alias_name" => "return delivery detail",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.return.list_surat_jalan.detail.edit",
            "alias_name" => "return delivery detail edit",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.return.list_surat_jalan.detail.confirm",
            "alias_name" => "return delivery detail confirm",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.return.list_surat_jalan.detail.delete",
            "alias_name" => "return delivery detail delete",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.return.list_surat_jalan.detail.print",
            "alias_name" => "return delivery detail print",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.return.detail",
            "alias_name" => "return detail",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.return.detail.edit",
            "alias_name" => "return detail edit",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.return.detail.confirm",
            "alias_name" => "return detail confirm",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.return.detail.delete",
            "alias_name" => "return detail delete",
            "group_name" => "neq"
        ],
        [
            "name" => "neq.return.detail.surat_jalan",
            "alias_name" => "return detail delivery",
            "group_name" => "neq"
        ],
    ];

    const permissionMaster = [
        [
            "name" => "master.dealer_list.data",
            "alias_name" => "dealer list data",
            "group_name" => "master"
        ],
        [
            "name" => "master.dealer_list.sync_dealer",
            "alias_name" => "dealer list sync dealer",
            "group_name" => "master"
        ],
        [
            "name" => "master.neq_list.data",
            "alias_name" => "neq list data",
            "group_name" => "master"
        ],
        [
            "name" => "master.role_list.data",
            "alias_name" => "role list data",
            "group_name" => "master"
        ],
        [
            "name" => "master.role_list.add_new_role",
            "alias_name" => "role list add new role",
            "group_name" => "master"
        ],
        [
            "name" => "master.bank_list.add_new_bank",
            "alias_name" => "bank list data",
            "group_name" => "master"
        ],
        [
            "name" => "master.bank_list.data",
            "alias_name" => "bank list data",
            "group_name" => "master"
        ],
        [
            "name" => "master.bank_list.detail",
            "alias_name" => "bank list detail",
            "group_name" => "master"
        ],
        [
            "name" => "master.bank_list.detail.edit",
            "alias_name" => "bank list detail edit",
            "group_name" => "master"
        ],
        [
            "name" => "master.price_list.data",
            "alias_name" => "price list data",
            "group_name" => "master"
        ],
        [
            "name" => "master.price_list.clone",
            "alias_name" => "price list clone",
            "group_name" => "master"
        ],
        [
            "name" => "master.price_list.edit",
            "alias_name" => "price list edit",
            "group_name" => "master"
        ],
        [
            "name" => "master.price_list.detail",
            "alias_name" => "price list detail",
            "group_name" => "master"
        ],
    ];

    const permissionUser = [
        [
            "name" => "user.data",
            "alias_name" => "user data",
            "group_name" => "user"
        ],
        [
            "name" => "user.add_new_user",
            "alias_name" => "add new user",
            "group_name" => "user"
        ],
        [
            "name" => "user.detail",
            "alias_name" => "user detail",
            "group_name" => "user"
        ],
        [
            "name" => "user.detail.deactive_user",
            "alias_name" => "user detail deactive user",
            "group_name" => "user"
        ],
        [
            "name" => "user.detail.reset_password",
            "alias_name" => "user detail reset password",
            "group_name" => "user"
        ],
        [
            "name" => "user.detail.edit_user",
            "alias_name" => "user detail edit user",
            "group_name" => "user"
        ],
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
            self::permissionMaster,
            self::permissionUser
        );
    }
}
