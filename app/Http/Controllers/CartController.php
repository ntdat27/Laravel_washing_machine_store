<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Services\GHNOrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CartController — Quản lý giỏ hàng và xử lý đặt hàng.
 *
 * Phương thức thanh toán được hỗ trợ:
 *   - COD  : Tạo đơn + trừ kho + tạo GHN ngay → redirect về "Đơn của tôi"
 *   - MoMo : Tạo đơn + trừ kho → redirect sang MomoController@start → MoMo
 *            (nếu TT MoMo thất bại → MomoController hoàn kho + hủy đơn)
 */
class CartController extends Controller
{
    // ==========================================
    // GIỎ HÀNG
    // ==========================================

    public function index(): View
    {
        $cart = session()->get('cart', []);
        return view('user.cart.index', compact('cart'));
    }

    /**
     * Thêm sản phẩm vào giỏ hàng.
     *
     * Luồng:
     * 1. Nhận variant_id từ form (user đã chọn màu trên trang chi tiết).
     * 2. Nếu sản phẩm có variants mà user CHƯA chọn → redirect về trang SP báo lỗi.
     * 3. Kiểm tra tồn kho (variant.stock HOẶC product.stock_quantity) > 0.
     * 4. Kiểm tra không vượt quá tồn kho hiện có trong giỏ.
     * 5. Lưu vào session với key duy nhất "{productId}_v{variantId}" hoặc "{productId}".
     */
    public function add(Request $request, Product $product): RedirectResponse
    {
        $product->load('variants', 'category');

        $variantId      = $request->input('variant_id');
        $variantId      = $variantId ? (int) $variantId : null;
        $variant        = null;
        $availableStock = 0;

        if ($product->variants->isNotEmpty()) {
            // --- Sản phẩm CÓ variant ---
            if (!$variantId) {
                return redirect()->route('user.products.show', $product->id)
                    ->with('error', 'Vui lòng chọn màu sắc trước khi thêm vào giỏ hàng.');
            }

            $variant = $product->variants->firstWhere('id', $variantId);
            if (!$variant) {
                return redirect()->route('user.products.show', $product->id)
                    ->with('error', 'Màu sắc không hợp lệ cho sản phẩm này.');
            }

            if ($variant->stock <= 0) {
                return redirect()->route('user.products.show', $product->id)
                    ->with('error', "Màu <strong>{$variant->color_name}</strong> hiện đã hết hàng.");
            }

            $availableStock = $variant->stock;
        } else {
            // --- Sản phẩm KHÔNG có variant ---
            if ($product->stock_quantity <= 0) {
                return redirect()->route('user.products.show', $product->id)
                    ->with('error', 'Sản phẩm này hiện đã hết hàng.');
            }
            $availableStock = $product->stock_quantity;
            $variantId      = null;
        }

        $cart    = session()->get('cart', []);
        $cartKey = $variantId ? "{$product->id}_v{$variantId}" : (string) $product->id;
        $currentQty = $cart[$cartKey]['quantity'] ?? 0;

        if ($currentQty >= $availableStock) {
            return redirect()->route('user.products.show', $product->id)
                ->with('error', "Giỏ hàng đã có {$currentQty} sp. Tồn kho còn {$availableStock} — không thể thêm.");
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity']++;
        } else {
            $cart[$cartKey] = [
                'product_id'   => $product->id,
                'variant_id'   => $variantId,
                'name'         => $product->name,
                'variant_name' => $variant ? $variant->color_name : null,
                'quantity'     => 1,
                'price'        => $variant ? $variant->price : $product->price,
                'category'     => $product->category->name ?? 'N/A',
                'image'        => ($variant && $variant->image) ? $variant->image : $product->image,
                'stock'        => $availableStock,
            ];
        }

        session()->put('cart', $cart);

        $label = $variant ? " ({$variant->color_name})" : '';
        return redirect()->route('cart.index')
            ->with('success', "Đã thêm <strong>{$product->name}{$label}</strong> vào giỏ hàng!");
    }

    /**
     * Cập nhật số lượng — kiểm tra không vượt kho thực tế từ DB.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $newQty = (int) $request->input('quantity', 0);

        if ($newQty <= 0) {
            return redirect()->route('cart.index')->with('error', 'Số lượng phải lớn hơn 0!');
        }

        $cart = session()->get('cart', []);

        if (!isset($cart[$id])) {
            return redirect()->route('cart.index')->with('error', 'Sản phẩm không tồn tại trong giỏ!');
        }

        $availableStock = $this->getAvailableStock($cart[$id]);

        if ($newQty > $availableStock) {
            return redirect()->route('cart.index')
                ->with('error', "Số lượng yêu cầu ({$newQty}) vượt quá tồn kho hiện có ({$availableStock}).");
        }

        $cart[$id]['quantity'] = $newQty;
        $cart[$id]['stock']    = $availableStock;
        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Đã cập nhật số lượng!');
    }

    public function remove($id): RedirectResponse
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return redirect()->route('cart.index')
            ->with('success', 'Đã xoá sản phẩm khỏi giỏ hàng.');
    }

    // ==========================================
    // COUPON
    // ==========================================

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric'
        ]);

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon) {
            return response()->json(['status' => 'error', 'message' => 'Mã giảm giá không tồn tại!']);
        }

        if (!$coupon->isValid($request->subtotal)) {
            return response()->json(['status' => 'error', 'message' => 'Mã giảm giá đã hết hạn, hết lượt dùng hoặc chưa đạt giá trị đơn hàng tối thiểu!']);
        }

        $discountAmount = $coupon->discount_type === 'percent'
            ? ($request->subtotal * $coupon->discount_value / 100)
            : $coupon->discount_value;

        if ($discountAmount > $request->subtotal) {
            $discountAmount = $request->subtotal;
        }

        session()->put('coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'discount_amount' => $discountAmount
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Áp dụng mã giảm giá thành công!',
            'discount_amount' => $discountAmount
        ]);
    }

    public function removeCoupon()
    {
        session()->forget('coupon');
        return response()->json([
            'status' => 'success',
            'message' => 'Đã gỡ mã giảm giá.'
        ]);
    }

    // ==========================================
    // CHECKOUT
    // ==========================================

    /**
     * GET  → Hiển thị trang Checkout.
     * POST → Validate → Kiểm tra kho (lock) → Tạo đơn + Trừ kho → Phân luồng TT.
     *
     * Response: JSON (fetch từ JS)
     *   success + redirect_url  → redirect
     *   success + is_momo=true  → thông báo "đang chuyển hướng sang MoMo"
     *   error + message         → hiện lỗi, không reload
     */
    public function checkout(Request $request, GHNOrderService $ghnOrderService)
    {
        if ($request->isMethod('post')) {

            // BƯỚC 0: Validate
            $validated = $request->validate([
                'name'           => 'required|string|max:255',
                'phone'          => 'required|string|max:20',
                'address'        => 'required|string|max:500',
                'to_district_id' => 'required|integer',
                'to_ward_code'   => 'required|string',
                'total_price'    => 'required|numeric|min:0',
                'payment_method' => 'required|in:cod,momo',
            ]);

            $cart = session()->get('cart', []);
            if (empty($cart)) {
                return response()->json(['status' => 'error', 'message' => 'Giỏ hàng đang trống!']);
            }

            // BƯỚC A–B: Transaction — kiểm tra kho, tạo đơn, trừ kho
            try {
                $order = DB::transaction(function () use ($cart, $validated) {

                    // A1: Kiểm tra kho lần cuối (lockForUpdate chống race condition)
                    foreach ($cart as $details) {
                        $productId = $details['product_id'];
                        $variantId = $details['variant_id'] ?? null;
                        $qty       = (int) $details['quantity'];
                        $name      = $details['name'];

                        if ($variantId) {
                            $variant   = ProductVariant::lockForUpdate()->find($variantId);
                            $available = $variant?->stock ?? 0;
                            if (!$variant || $available < $qty) {
                                $colorName = $details['variant_name'] ?? '';
                                throw new \Exception(
                                    "Sản phẩm \"{$name}\" (màu {$colorName}) chỉ còn {$available} chiếc trong kho (bạn đặt {$qty})."
                                );
                            }
                        } else {
                            $product   = Product::lockForUpdate()->find($productId);
                            $available = $product?->stock_quantity ?? 0;
                            if (!$product || $available < $qty) {
                                throw new \Exception(
                                    "Sản phẩm \"{$name}\" chỉ còn {$available} chiếc trong kho (bạn đặt {$qty})."
                                );
                            }
                        }
                    }

                    // Tính subtotal từ cart
                    $subtotal = 0;
                    foreach ($cart as $details) {
                        $subtotal += $details['price'] * $details['quantity'];
                    }

                    // Tính toán Coupon từ session
                    $couponSession = session()->get('coupon');
                    $couponId = null;
                    $discountAmount = 0;

                    if ($couponSession) {
                        $couponId = $couponSession['id'];
                        $discountAmount = $couponSession['discount_amount'];
                    }

                    // Đảm bảo $finalTotal đã trừ discount_amount (frontend gửi lên total_price đã trừ, nhưng ta gán ra biến $finalTotal cho rõ ràng)
                    $finalTotal = $validated['total_price'];

                    // A2: Tạo Order
                    $order = Order::create([
                        'user_id'         => Auth::id(),
                        'name'            => $validated['name'],
                        'phone'           => $validated['phone'],
                        'address'         => $validated['address'],
                        'to_district_id'  => $validated['to_district_id'],
                        'to_ward_code'    => $validated['to_ward_code'],
                        'total_price'     => $finalTotal,
                        'coupon_id'       => $couponId,
                        'discount_amount' => $discountAmount,
                        'status'          => 'pending',
                        'shipping_status' => 'pending',
                    ]);

                    // Xử lý tăng lượt sử dụng coupon sau khi chốt đơn thành công
                    if ($couponId) {
                        Coupon::where('id', $couponId)->increment('used_count');
                    }

                    // B: Tạo OrderItem và TRỪ KHO
                    foreach ($cart as $details) {
                        $productId = $details['product_id'];
                        $variantId = $details['variant_id'] ?? null;
                        $qty       = (int) $details['quantity'];

                        OrderItem::create([
                            'order_id'   => $order->id,
                            'product_id' => $productId,
                            'variant_id' => $variantId,
                            'quantity'   => $qty,
                            'price'      => $details['price'],
                        ]);

                        if ($variantId) {
                            ProductVariant::where('id', $variantId)->decrement('stock', $qty);
                        }
                        Product::where('id', $productId)->decrement('stock_quantity', $qty);
                    }

                    return $order;
                });
            } catch (\Exception $e) {
                Log::warning('[Checkout] Lỗi tạo đơn hàng: ' . $e->getMessage());
                return response()->json([
                    'status'  => 'error',
                    'message' => $e->getMessage(),
                ]);
            }

            // BƯỚC C: Phân luồng thanh toán

            // ── MoMo ──────────────────────────────────────────────────────────
            if ($validated['payment_method'] === 'momo') {
                // Xóa giỏ hàng và coupon TRƯỚC khi redirect
                session()->forget('cart');
                session()->forget('coupon');

                // Tạo transaction sơ bộ (MomoController sẽ điền gateway_order_id)
                PaymentTransaction::create([
                    'order_id' => $order->id,
                    'gateway'  => 'momo',
                    'amount'   => $order->total_price,
                    'status'   => 'pending',
                ]);

                // Redirect JS sang MomoController@start
                return response()->json([
                    'status'       => 'success',
                    'message'      => 'Đang chuyển hướng sang cổng thanh toán MoMo...',
                    'redirect_url' => route('user.orders.momo.start', $order->id),
                    'is_momo'      => true,
                ]);
            }

            // ── COD ───────────────────────────────────────────────────────────
            PaymentTransaction::create([
                'order_id' => $order->id,
                'gateway'  => 'cod',
                'amount'   => $order->total_price,
                'status'   => 'pending',
                'message'  => 'Thanh toán khi nhận hàng',
            ]);

            // Tạo vận đơn GHN ngay
            try {
                $ghnResponse = $ghnOrderService->create($order);
                if (isset($ghnResponse['code']) && $ghnResponse['code'] == 200) {
                    $order->update([
                        'status'          => 'cod_ordered',
                        'shipping_status' => 'ready_to_pick',
                        'ghn_order_code'  => $ghnResponse['data']['order_code'] ?? null,
                        'ghn_total_fee'   => $ghnResponse['data']['total_fee']  ?? 0,
                    ]);
                } else {
                    $order->update(['status' => 'cod_ordered']);
                }
            } catch (\Exception $e) {
                Log::error('[GHN] Lỗi tạo vận đơn COD: ' . $e->getMessage());
                $order->update(['status' => 'cod_ordered']);
            }

            // Gửi Email Xác Nhận cho đơn COD (nếu kết nối SMTP hoạt động)
            if ($this->canSendSmtp()) {
                try {
                    \Illuminate\Support\Facades\Mail::to(Auth::user()->email)->send(new \App\Mail\OrderConfirmation($order));
                } catch (\Exception $e) {
                    Log::error('[Email] Lỗi gửi email xác nhận đơn hàng: ' . $e->getMessage());
                }
            }

            session()->forget('cart');
            session()->forget('coupon');

            return response()->json([
                'status'       => 'success',
                'message'      => 'Đặt hàng COD thành công! Chúng tôi sẽ liên hệ xác nhận sớm nhất.',
                'redirect_url' => route('user.orders.index'),
                'is_momo'      => false,
            ]);
        }

        // GET — Hiển thị trang Checkout
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }
        return view('user.checkout.index', compact('cart'));
    }

    // ==========================================
    // PRIVATE HELPERS
    // ==========================================

    /** Lấy tồn kho thực tế từ DB cho 1 cart item. */
    private function getAvailableStock(array $cartItem): int
    {
        $variantId = $cartItem['variant_id'] ?? null;
        if ($variantId) {
            return (int) (ProductVariant::find($variantId)?->stock ?? 0);
        }
        return (int) (Product::find($cartItem['product_id'])?->stock_quantity ?? 0);
    }
}