@extends('admin.layout')

@section('content')

<style>
    /* ── SHARED ADMIN TABLE STYLES ── */
    .admin-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
    }
    .admin-card-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;
        background: #fff;
    }
    .admin-card-title {
        font-size: 1rem; font-weight: 700; color: #0f172a;
        display: flex; align-items: center; gap: .5rem; margin: 0;
    }
    .btn-add-new {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .55rem 1.1rem;
        border-radius: 50px;
        font-size: .85rem; font-weight: 600;
        background: #2563eb; color: #fff; border: none;
        text-decoration: none;
        transition: all .2s;
        box-shadow: 0 2px 8px rgba(37,99,235,.2);
    }
    .btn-add-new:hover { background: #1d4ed8; color: #fff; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(37,99,235,.3); }

    /* Search bar */
    .admin-search-group {
        display: flex;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
        transition: border-color .2s;
    }
    .admin-search-group:focus-within { border-color: #2563eb; }
    .admin-search-group .search-icon {
        padding: 0 .85rem;
        display: flex; align-items: center;
        color: #94a3b8;
        background: transparent;
        border: none;
    }
    .admin-search-group input {
        border: none; outline: none; flex: 1;
        padding: .65rem .5rem;
        font-size: .9rem; color: #0f172a;
        background: transparent;
    }
    .admin-search-group input::placeholder { color: #94a3b8; }
    .admin-search-group .search-btn {
        background: #2563eb; color: #fff; border: none;
        padding: .65rem 1.25rem;
        font-size: .88rem; font-weight: 600;
        cursor: pointer; transition: background .2s;
    }
    .admin-search-group .search-btn:hover { background: #1d4ed8; }
    .btn-reset {
        background: #f1f5f9; border: none;
        padding: .65rem .9rem;
        cursor: pointer; transition: background .2s;
        color: #64748b;
    }
    .btn-reset:hover { background: #e2e8f0; }

    /* Table */
    .admin-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .admin-table thead th {
        padding: .7rem 1rem;
        font-size: .72rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;
        color: #94a3b8; background: #f8faff;
        border-bottom: 1px solid #e2e8f0;
    }
    .admin-table thead th:first-child { border-radius: 0; }
    .admin-table tbody td {
        padding: .9rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: .9rem;
        background: #fff;
    }
    .admin-table tbody tr:last-child td { border-bottom: none; }
    .admin-table tbody tr:hover td { background: #f8faff; }

    /* Product thumbnail */
    .prod-thumb {
        width: 52px; height: 52px; border-radius: 10px;
        object-fit: contain;
        background: linear-gradient(135deg,#f8faff,#eff6ff);
        border: 1px solid #e2e8f0;
        padding: 4px;
    }
    .prod-thumb-placeholder {
        width: 52px; height: 52px; border-radius: 10px;
        background: linear-gradient(135deg,#f8faff,#eff6ff);
        border: 1px solid #e2e8f0;
        display: flex; align-items: center; justify-content: center;
    }

    /* Action buttons */
    .btn-action {
        width: 32px; height: 32px;
        border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: .8rem;
        text-decoration: none;
        border: none; cursor: pointer;
        transition: all .2s;
    }
    .btn-action.edit { background: #fef9c3; color: #92400e; border: 1px solid #fde68a; }
    .btn-action.edit:hover { background: #f59e0b; color: #fff; border-color: #f59e0b; }
    .btn-action.delete { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .btn-action.delete:hover { background: #ef4444; color: #fff; border-color: #ef4444; }

    /* Badges */
    .stock-badge {
        display: inline-flex; align-items: center; gap: .25rem;
        padding: .25rem .65rem;
        border-radius: 50px;
        font-size: .72rem; font-weight: 700;
    }
    .stock-badge.in { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .stock-badge.out { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .cat-badge {
        display: inline-flex; align-items: center;
        padding: .25rem .7rem;
        border-radius: 50px;
        font-size: .72rem; font-weight: 600;
        background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;
    }
</style>

<div class="admin-card shadow-sm">
    <div class="admin-card-header">
        <h5 class="admin-card-title">
            <i class="fa-solid fa-washing-machine text-primary"></i> Danh Sách Sản Phẩm
            <span style="font-size:.78rem;font-weight:500;color:#94a3b8;margin-left:.25rem;">/ Máy giặt</span>
        </h5>
        <a href="{{ route('admin.products.create') }}" class="btn-add-new">
            <i class="fa-solid fa-plus fa-xs"></i> Thêm Sản Phẩm
        </a>
    </div>

    <div class="p-4">
        @if(session('success'))
            <div class="d-flex align-items-center gap-3 p-3 mb-4 rounded-3" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                <i class="fa-solid fa-circle-check text-success"></i>
                <span class="fw-600 text-success" style="font-weight:600;">{{ session('success') }}</span>
                <button type="button" class="ms-auto btn p-0 border-0 text-success opacity-50" onclick="this.parentElement.remove()"><i class="fa-solid fa-xmark fa-xs"></i></button>
            </div>
        @endif

        {{-- Search --}}
        <form action="{{ route('admin.products.index') }}" method="GET" class="mb-4">
            <div class="admin-search-group">
                <span class="search-icon"><i class="fa-solid fa-magnifying-glass fa-sm"></i></span>
                <input type="text" name="search"
                       placeholder="Tìm theo tên sản phẩm hoặc thương hiệu..."
                       value="{{ request('search') }}">
                <button type="submit" class="search-btn">Tìm kiếm</button>
                @if(request('search'))
                    <a href="{{ route('admin.products.index') }}" class="btn-reset d-flex align-items-center" title="Xóa bộ lọc">
                        <i class="fa-solid fa-rotate fa-sm"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:48px;">ID</th>
                        <th style="width:60px;">Ảnh</th>
                        <th>Tên Sản Phẩm</th>
                        <th>Danh Mục</th>
                        <th>Thương Hiệu</th>
                        <th class="text-center">Khối Lượng</th>
                        <th class="text-end">Giá Bán</th>
                        <th class="text-center">Kho</th>
                        <th class="text-center" style="width:90px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="text-muted" style="font-size:.78rem;font-weight:600;">#{{ $product->id }}</td>
                            <td>
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" class="prod-thumb" alt="{{ $product->name }}">
                                @else
                                    <div class="prod-thumb-placeholder">
                                        <i class="fa-solid fa-washing-machine text-muted fa-sm opacity-40"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-700 text-dark" style="font-weight:700;font-size:.9rem;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $product->name }}</div>
                            </td>
                            <td>
                                <span class="cat-badge">{{ $product->category->name ?? 'N/A' }}</span>
                            </td>
                            <td class="fw-600 text-muted" style="font-weight:600;font-size:.88rem;">{{ $product->brand }}</td>
                            <td class="text-center" style="font-size:.88rem;">
                                @if($product->capacity_kg)
                                    <span style="color:#334155;font-weight:600;">{{ $product->capacity_kg }} <span style="font-size:.72rem;color:#94a3b8;">kg</span></span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="fw-700 text-danger" style="font-weight:700;font-size:.9rem;">{{ number_format($product->price, 0, ',', '.') }} đ</span>
                            </td>
                            <td class="text-center">
                                <span class="stock-badge {{ $product->stock_quantity > 0 ? 'in' : 'out' }}">
                                    <i class="fa-solid fa-circle fa-xs"></i>
                                    {{ $product->stock_quantity }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn-action edit" title="Chỉnh sửa">
                                        <i class="fa-solid fa-pen-to-square fa-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Xóa sản phẩm này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action delete" title="Xóa">
                                            <i class="fa-solid fa-trash fa-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fa-solid fa-box-open fa-2x text-muted opacity-30 mb-2 d-block"></i>
                                <span class="text-muted">Chưa có dữ liệu. Hãy thêm sản phẩm mới!</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-4 pt-3 border-top" style="border-color:#f1f5f9 !important;">
            {{ $products->links() }}
        </div>
    </div>
</div>

@endsection