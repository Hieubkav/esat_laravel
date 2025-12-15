<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerOrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng của khách hàng
     */
    public function index(Request $request)
    {
        $customer = Auth::guard('mshopkeeper_customer')->user();

        if (!$customer) {
            return redirect('/')->with('error', 'Vui lòng đăng nhập để xem đơn hàng.');
        }

        // Query đơn hàng của khách hàng - sử dụng mshopkeeper_customer_id
        $query = Order::where('mshopkeeper_customer_id', $customer->id)
            ->with(['items.product.productImages', 'items.mshopkeeperProduct'])
            ->orderBy('created_at', 'desc');

        // Filter theo status nếu có
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Phân trang
        $orders = $query->paginate(10);

        // Thống kê đơn hàng
        $orderStats = [
            'total' => Order::where('mshopkeeper_customer_id', $customer->id)->count(),
            'pending' => Order::where('mshopkeeper_customer_id', $customer->id)->where('status', 'pending')->count(),
            'processing' => Order::where('mshopkeeper_customer_id', $customer->id)->whereIn('status', ['confirmed', 'processing', 'shipping'])->count(),
            'completed' => Order::where('mshopkeeper_customer_id', $customer->id)->where('status', 'delivered')->count(),
            'cancelled' => Order::where('mshopkeeper_customer_id', $customer->id)->whereIn('status', ['cancelled', 'refunded'])->count(),
        ];

        return view('customer.orders.index', compact('orders', 'orderStats', 'customer'));
    }

    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function show($orderNumber)
    {
        $customer = Auth::guard('mshopkeeper_customer')->user();

        if (!$customer) {
            return redirect('/')->with('error', 'Vui lòng đăng nhập để xem đơn hàng.');
        }

        $order = Order::where('order_number', $orderNumber)
            ->where('mshopkeeper_customer_id', $customer->id)
            ->with(['items.product.productImages', 'items.mshopkeeperProduct', 'customer'])
            ->firstOrFail();

        return view('customer.orders.show', compact('order', 'customer'));
    }

    /**
     * Hủy đơn hàng (chỉ cho phép hủy đơn hàng pending)
     */
    public function cancel($orderNumber)
    {
        $customer = Auth::guard('mshopkeeper_customer')->user();

        if (!$customer) {
            return redirect('/')->with('error', 'Vui lòng đăng nhập.');
        }

        $order = Order::where('order_number', $orderNumber)
            ->where('mshopkeeper_customer_id', $customer->id)
            ->firstOrFail();

        // Chỉ cho phép hủy đơn hàng pending
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Không thể hủy đơn hàng này.');
        }

        $order->update(['status' => 'cancelled']);

        return redirect()->route('customer.orders.index')->with('success', 'Đã hủy đơn hàng thành công.');
    }

    /**
     * Get status label và color
     */
    public static function getStatusConfig($status)
    {
        $configs = [
            'pending' => [
                'label' => 'Chờ xử lý',
                'color' => 'bg-yellow-100 text-yellow-800',
                'icon' => 'fas fa-clock'
            ],
            'confirmed' => [
                'label' => 'Đã xác nhận',
                'color' => 'bg-blue-100 text-blue-800',
                'icon' => 'fas fa-check'
            ],
            'processing' => [
                'label' => 'Đang xử lý',
                'color' => 'bg-blue-100 text-blue-800',
                'icon' => 'fas fa-cog'
            ],
            'shipping' => [
                'label' => 'Đang giao hàng',
                'color' => 'bg-purple-100 text-purple-800',
                'icon' => 'fas fa-truck'
            ],
            'delivered' => [
                'label' => 'Đã giao hàng',
                'color' => 'bg-green-100 text-green-800',
                'icon' => 'fas fa-check-circle'
            ],
            'cancelled' => [
                'label' => 'Đã hủy',
                'color' => 'bg-red-100 text-red-800',
                'icon' => 'fas fa-times-circle'
            ],
            'refunded' => [
                'label' => 'Đã hoàn tiền',
                'color' => 'bg-gray-100 text-gray-800',
                'icon' => 'fas fa-undo'
            ]
        ];

        return $configs[$status] ?? [
            'label' => ucfirst($status),
            'color' => 'bg-gray-100 text-gray-800',
            'icon' => 'fas fa-question-circle'
        ];
    }

    /**
     * Get payment status config
     */
    public static function getPaymentStatusConfig($paymentStatus)
    {
        $configs = [
            'pending' => [
                'label' => 'Chưa thanh toán',
                'color' => 'bg-yellow-100 text-yellow-800'
            ],
            'paid' => [
                'label' => 'Đã thanh toán',
                'color' => 'bg-green-100 text-green-800'
            ],
            'failed' => [
                'label' => 'Thanh toán thất bại',
                'color' => 'bg-red-100 text-red-800'
            ]
        ];

        return $configs[$paymentStatus] ?? [
            'label' => ucfirst($paymentStatus),
            'color' => 'bg-gray-100 text-gray-800'
        ];
    }
}
