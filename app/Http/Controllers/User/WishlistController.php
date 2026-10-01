<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class WishlistController extends Controller
{
    /**
     * Hiển thị danh sách yêu thích
     */
    public function index()
    {
        $wishlists = Wishlist::with('product')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('user.wishlist.index', compact('wishlists'));
    }

    /**
     * Thêm/Xoá sản phẩm khỏi wishlist (dùng AJAX)
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $userId = Auth::id();
        $productId = $request->product_id;

        $wishlist = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($wishlist) {
            // Đã thích -> Bỏ thích
            $wishlist->delete();
            return response()->json([
                'status' => 'removed',
                'message' => 'Đã bỏ sản phẩm khỏi danh sách yêu thích.'
            ]);
        } else {
            // Chưa thích -> Thêm vào
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId
            ]);
            return response()->json([
                'status' => 'added',
                'message' => 'Đã thêm sản phẩm vào danh sách yêu thích.'
            ]);
        }
    }
}
