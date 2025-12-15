<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mshopkeeper_invoices', function (Blueprint $table) {
            // MShopKeeper Invoice fields
            $table->string('mshopkeeper_invoice_id')->unique()->after('id')->comment('InvoiceId từ MShopKeeper API');
            $table->string('invoice_number')->index()->after('mshopkeeper_invoice_id')->comment('InvoiceNumber - Số hóa đơn');
            $table->integer('invoice_type')->nullable()->after('invoice_number')->comment('InvoiceType - Loại hóa đơn');
            $table->timestamp('invoice_date')->nullable()->after('invoice_type')->comment('InvoiceDate - Ngày tạo hóa đơn');
            $table->timestamp('invoice_time')->nullable()->after('invoice_date')->comment('InvoiceTime - Giờ tạo hóa đơn');

            // Branch information
            $table->string('branch_id')->nullable()->after('invoice_time')->comment('BranchId - ID chi nhánh');
            $table->string('branch_name')->nullable()->after('branch_id')->comment('BranchName - Tên chi nhánh');

            // Financial information
            $table->decimal('total_amount', 15, 2)->default(0)->after('branch_name')->comment('TotalAmount - Tổng tiền');
            $table->decimal('cost_amount', 15, 2)->default(0)->after('total_amount')->comment('CostAmount - Tiền phí');
            $table->decimal('tax_amount', 15, 2)->default(0)->after('cost_amount')->comment('TaxAmount - Tiền thuế');
            $table->decimal('total_item_amount', 15, 2)->default(0)->after('tax_amount')->comment('TotalItemAmount - Tổng tiền hàng hóa');
            $table->decimal('vat_amount', 15, 2)->default(0)->after('total_item_amount')->comment('VATAmount - Tiền thuế VAT');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('vat_amount')->comment('DiscountAmount - Tiền giảm giá');
            $table->decimal('cash_amount', 15, 2)->default(0)->after('discount_amount')->comment('CashAmount - Tiền mặt');
            $table->decimal('card_amount', 15, 2)->default(0)->after('cash_amount')->comment('CardAmount - Tiền thẻ');
            $table->decimal('voucher_amount', 15, 2)->default(0)->after('card_amount')->comment('VoucherAmount - Tiền voucher');
            $table->decimal('debit_amount', 15, 2)->default(0)->after('voucher_amount')->comment('DebitAmount - Tiền nợ');
            $table->decimal('actual_amount', 15, 2)->default(0)->after('debit_amount')->comment('ActualAmount - Tiền thực thu');

            // Customer information
            $table->string('customer_name')->nullable()->after('actual_amount')->comment('CustomerName - Tên khách hàng');
            $table->string('customer_tel', 50)->nullable()->after('customer_name')->comment('Tel - Số điện thoại khách hàng');
            $table->text('customer_address')->nullable()->after('customer_tel')->comment('Address - Địa chỉ khách hàng');
            $table->string('member_level_name')->nullable()->after('customer_address')->comment('MemberLevelName - Hạng thẻ');

            // Staff information
            $table->string('cashier')->nullable()->after('member_level_name')->comment('Cashier - Thu ngân');
            $table->string('sale_staff')->nullable()->after('cashier')->comment('SaleStaff - Nhân viên bán hàng');

            // Status and delivery
            $table->integer('payment_status')->nullable()->after('sale_staff')->comment('PaymentStatus - Trạng thái thanh toán');
            $table->boolean('is_cod')->default(false)->after('payment_status')->comment('IsCOD - Thu tiền khi giao hàng');
            $table->integer('addition_bill_type')->nullable()->after('is_cod')->comment('AdditionBillType - Loại hóa đơn nhập bù');
            $table->string('sale_channel_name')->nullable()->after('addition_bill_type')->comment('SaleChannelName - Kênh bán hàng');
            $table->string('return_exchange_ref_no')->nullable()->after('sale_channel_name')->comment('ReturnExchangeRefNo - Số hóa đơn đổi trả');

            // Shipping information
            $table->boolean('has_connected_shipping_partner')->default(false)->after('return_exchange_ref_no')->comment('HasConnectedShippingPartner');
            $table->string('delivery_code')->nullable()->after('has_connected_shipping_partner')->comment('DeliveryCode - Mã vận đơn');
            $table->string('shipping_partner_name')->nullable()->after('delivery_code')->comment('ShippingPartnerName - Đối tác vận chuyển');
            $table->integer('partner_status')->nullable()->after('shipping_partner_name')->comment('PartnerStatus - Trạng thái đối tác');
            $table->timestamp('delivery_date')->nullable()->after('partner_status')->comment('DeliveryDate - Ngày giao hàng');
            $table->decimal('point', 8, 2)->default(0)->after('delivery_date')->comment('Point - Điểm tích lũy');

            // Additional fields
            $table->string('barcode')->nullable()->after('point')->comment('Barcode - Mã vạch');
            $table->text('note')->nullable()->after('barcode')->comment('Note - Ghi chú');

            // Sync tracking fields
            $table->timestamp('last_synced_at')->nullable()->after('note')->comment('Lần sync cuối cùng');
            $table->enum('sync_status', ['synced', 'error', 'pending'])->default('pending')->after('last_synced_at')->comment('Trạng thái sync');
            $table->text('sync_error')->nullable()->after('sync_status')->comment('Lỗi sync (nếu có)');
            $table->json('raw_data')->nullable()->after('sync_error')->comment('Dữ liệu thô từ API');

            // Indexes for performance
            $table->index(['invoice_date', 'sync_status']);
            $table->index(['customer_name', 'customer_tel']);
            $table->index(['sale_channel_name', 'payment_status']);
            $table->index(['branch_id', 'invoice_date']);
            $table->index('last_synced_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mshopkeeper_invoices', function (Blueprint $table) {
            $table->dropIndex(['mshopkeeper_invoices_invoice_date_sync_status_index']);
            $table->dropIndex(['mshopkeeper_invoices_customer_name_customer_tel_index']);
            $table->dropIndex(['mshopkeeper_invoices_sale_channel_name_payment_status_index']);
            $table->dropIndex(['mshopkeeper_invoices_branch_id_invoice_date_index']);
            $table->dropIndex(['mshopkeeper_invoices_last_synced_at_index']);
            $table->dropIndex(['mshopkeeper_invoices_mshopkeeper_invoice_id_unique']);
            $table->dropIndex(['mshopkeeper_invoices_invoice_number_index']);

            $table->dropColumn([
                'mshopkeeper_invoice_id',
                'invoice_number',
                'invoice_type',
                'invoice_date',
                'invoice_time',
                'branch_id',
                'branch_name',
                'total_amount',
                'cost_amount',
                'tax_amount',
                'total_item_amount',
                'vat_amount',
                'discount_amount',
                'cash_amount',
                'card_amount',
                'voucher_amount',
                'debit_amount',
                'actual_amount',
                'customer_name',
                'customer_tel',
                'customer_address',
                'member_level_name',
                'cashier',
                'sale_staff',
                'payment_status',
                'is_cod',
                'addition_bill_type',
                'sale_channel_name',
                'return_exchange_ref_no',
                'has_connected_shipping_partner',
                'delivery_code',
                'shipping_partner_name',
                'partner_status',
                'delivery_date',
                'point',
                'barcode',
                'note',
                'last_synced_at',
                'sync_status',
                'sync_error',
                'raw_data',
            ]);
        });
    }
};
