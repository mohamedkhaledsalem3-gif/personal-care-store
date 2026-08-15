<?php


namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {
    }

    /**
     * عرض السلة.
     */
    public function index(Request $request): View
    {
        $cart = $this->cartService->getCart(
            (int) $request->user()->id
        );

        $cart->load([
            'items.productVariant.product',
            'items.productVariant.inventory',
        ]);

        return view('storefront.cart.index', [
            'cart' => $cart,
        ]);
    }

    /**
     * إضافة Variant إلى السلة.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'variant_id' => [
                'required',
                'integer',
                'exists:product_variants,id',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        try {
            $this->cartService->addItem(
                (int) $request->user()->id,
                (int) $validated['variant_id'],
                (int) $validated['quantity']
            );

            return redirect()
                ->route('storefront.cart.index')
                ->with('success', 'تمت إضافة المنتج إلى السلة بنجاح.');

        } catch (RuntimeException $e) {

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * تحديث كمية عنصر.
     */
    public function update(
        Request $request,
        int $item
    ): RedirectResponse {
        $validated = $request->validate([
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        try {
            $this->cartService->updateItem(
                (int) $request->user()->id,
                $item,
                (int) $validated['quantity']
            );

            return back()
                ->with('success', 'تم تحديث الكمية.');

        } catch (RuntimeException $e) {

            return back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * حذف عنصر.
     */
    public function destroy(
        Request $request,
        int $item
    ): RedirectResponse {
        try {
            $this->cartService->removeItem(
                (int) $request->user()->id,
                $item
            );

            return back()
                ->with('success', 'تم حذف المنتج من السلة.');

        } catch (RuntimeException $e) {

            return back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * تفريغ السلة.
     */
    public function clear(Request $request): RedirectResponse
    {
        $this->cartService->clear(
            (int) $request->user()->id
        );

        return back()
            ->with('success', 'تم تفريغ السلة.');
    }
}
