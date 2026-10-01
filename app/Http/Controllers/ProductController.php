<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    // ==========================================
    // ADMIN — Quản lý sản phẩm
    // ==========================================

    public function index(Request $request): View
    {
        $search = $request->input('search');

        $products = Product::with('category')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('brand', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        $products->appends(['search' => $search]);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'                      => 'required|string|max:255',
            'category_id'               => 'required|exists:categories,id',
            'brand'                     => 'required|string',
            'price'                     => 'required|numeric|min:0',
            'variants'                  => 'required|array|min:1',
            'variants.*.color_name'     => 'required|string',
            'variants.*.sku'            => 'required|string|unique:product_variants,sku',
            'variants.*.price'          => 'required|numeric|min:0',
            'variants.*.stock'          => 'required|integer|min:0',
            'image'                     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $productData = $request->except(['variants', 'image']);

        if ($request->hasFile('image')) {
            $productData['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($productData);

        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                $product->variants()->create($variant);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Thêm máy giặt và các phân màu thành công!');
    }

    public function show(Product $product): View
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:categories,id',
            'brand'          => 'required|string',
            'capacity_kg'    => 'required|numeric|min:0',
            'price'          => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->except(['image']);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Cập nhật máy giặt thành công!');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Xóa máy giặt thành công!');
    }

    // ==========================================
    // USER — Xem sản phẩm (có bộ lọc)
    // ==========================================

    /**
     * Danh sách sản phẩm cho user với bộ lọc:
     * - Tìm kiếm theo tên / thương hiệu
     * - Lọc theo danh mục
     * - Lọc theo khoảng giá
     * - Sắp xếp (mới nhất / giá tăng / giá giảm)
     */
    public function index_normal(Request $request): View
    {
        $search      = $request->input('search');
        $categoryId  = $request->input('category_id');
        $priceMin    = $request->input('price_min');
        $priceMax    = $request->input('price_max');
        $sort        = $request->input('sort', 'latest');

        $query = Product::with('category');

        // Lọc theo từ khóa
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Lọc theo danh mục
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Lọc theo khoảng giá
        if ($priceMin !== null && $priceMin !== '') {
            $query->where('price', '>=', (int) $priceMin);
        }
        if ($priceMax !== null && $priceMax !== '') {
            $query->where('price', '<=', (int) $priceMax);
        }

        // Chỉ hiển thị sản phẩm còn hàng
        $query->where('stock_quantity', '>', 0);

        // Sắp xếp
        match ($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc'   => $query->orderBy('name', 'asc'),
            default      => $query->latest(),
        };

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('user.products.index', compact('products', 'categories', 'search', 'categoryId', 'priceMin', 'priceMax', 'sort'));
    }

    /**
     * Chi tiết 1 sản phẩm cho user.
     * Load đầy đủ variants để hiển thị selector màu sắc.
     */
    public function show_normal(Product $product): View
    {
        $product->load(['variants' => function ($q) {
            $q->orderBy('color_name');
        }, 'category', 'reviews.user']);

        $totalReview = $product->reviews->count();
        $avgRating   = $product->avgRating();

        return view('user.products.show', compact('product', 'totalReview', 'avgRating'));
    }
}