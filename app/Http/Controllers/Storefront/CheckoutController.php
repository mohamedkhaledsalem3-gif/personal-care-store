<?php


namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly OrderService $orderService
    ) {
    }

    /**
     * عرض صفحة Checkout.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $userId = (int) $request->user()->id;

        $cart = $this->cartService->getCart($userId);

        /*
         * توحيد العلاقة على variant
         * لأنها العلاقة الأساسية في CartItem.
         */
        $cart->load([
            'items.variant.product',
            'items.variant.inventory',
        ]);

        /*
         * منع الوصول إلى Checkout إذا كانت السلة فارغة.
         */
        if ($cart->items->isEmpty()) {
            return redirect()
                ->route('storefront.cart.index')
                ->with(
                    'error',
                    'السلة فارغة. أضف منتجات أولاً.'
                );
        }

        return view('storefront.checkout.index', [
            'cart' => $cart,
        ]);
    }

    /**
     * إنشاء الطلب من السلة.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => [
                'required',
                'string',
                'max:255',
            ],

            'customer_phone' => [
                'required',
                'string',
                'max:30',
            ],

            'shipping_address' => [
                'required',
                'string',
                'max:1000',
            ],

            'shipping_city' => [
                'required',
                'string',
                'max:100',
            ],

            'shipping_area' => [
                'required',
                'string',
                'max:100',
            ],

            'shipping_postal_code' => [
                'nullable',
                'string',
                'max:20',
            ],

            'customer_note' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'payment_method' => [
                'required',
                'string',
                'in:cod',
            ],
        ]);

        try {
            $order = $this->orderService->createFromCart(
                userId: (int) $request->user()->id,

                customerData: [
                    'customer_name' =>
                        $validated['customer_name'],

                    'customer_phone' =>
                        $validated['customer_phone'],

                    'shipping_address' =>
                        $validated['shipping_address'],

                    'shipping_city' =>
                        $validated['shipping_city'],

                    'shipping_area' =>
                        $validated['shipping_area'],

                    'shipping_postal_code' =>
                        $validated['shipping_postal_code'] ?? null,

                    'customer_note' =>
                        $validated['customer_note'] ?? null,
                ],

                paymentMethod:
                    $validated['payment_method'],

                shippingFee: 0,

                discount: 0,
            );

            return redirect()
                ->route(
                    'storefront.orders.show',
                    $order
                )
                ->with(
                    'success',
                    'تم إنشاء الطلب بنجاح.'
                );

        } catch (ValidationException $e) {

            throw $e;

        } catch (RuntimeException $e) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }
}
