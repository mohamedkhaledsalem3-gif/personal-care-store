<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {
    }

    /**
     * عرض طلبات العميل الحالي.
     */
    public function index(): View
    {
        $orders = Order::query()
            ->where('user_id', Auth::id())
            ->withCount('items')
            ->latest('placed_at')
            ->latest('id')
            ->paginate(10);

        return view('storefront.orders.index', [
            'orders' => $orders,
        ]);
    }

    /**
     * عرض تفاصيل طلب للعميل الحالي.
     *
     * يتم التحقق من ملكية الطلب بواسطة OrderPolicy
     * من خلال Route::can().
     */
    public function show(Order $order): View
    {
        $order->load([
            'items',
            'payments',
            'user',
        ]);

        return view('storefront.orders.show', [
            'order' => $order,
        ]);
    }

    /**
     * إلغاء الطلب.
     *
     * يتم التحقق من الملكية وحالة الطلب بواسطة OrderPolicy
     * من خلال Route::can().
     *
     * تنفيذ الإلغاء وتحرير المخزون بالكامل موجود داخل OrderService.
     */
    public function cancel(Order $order): RedirectResponse
    {
        $order = $this->orderService->cancel(
            $order,
            request()->input('reason')
        );

        return redirect()
            ->route('storefront.orders.show', $order)
            ->with('success', 'تم إلغاء الطلب بنجاح.');
    }
}