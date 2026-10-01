@extends('admin.layout')

@section('content')

<style>
    .admin-form-card { background:#fff;border:1px solid #e2e8f0;border-radius:20px;overflow:hidden; }
    .admin-form-header { padding:1.25rem 1.75rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between; }
    .admin-form-title { font-size:1.05rem;font-weight:700;color:#0f172a;display:flex;align-items:center;gap:.6rem;margin:0; }
    .form-section-label { display:flex;align-items:center;gap:.6rem;font-size:.72rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#94a3b8;margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:1px solid #f1f5f9; }
    .form-label-custom { font-size:.78rem;font-weight:700;color:#334155;margin-bottom:.4rem;display:block; }
    .form-label-custom .req { color:#ef4444;margin-left:2px; }
    .form-input, .form-textarea, .form-sel { width:100%;border:2px solid #e2e8f0;border-radius:12px;padding:.7rem 1rem;font-size:.92rem;color:#0f172a;background:#fff;outline:none;transition:border-color .2s,box-shadow .2s;appearance:none; }
    .form-input:focus, .form-textarea:focus, .form-sel:focus { border-color:#2563eb;box-shadow:0 0 0 4px rgba(37,99,235,.08); }
    .form-input::placeholder, .form-textarea::placeholder { color:#94a3b8; }
    .form-textarea { resize:vertical;min-height:110px; }
    .form-file { width:100%;border:2px dashed #e2e8f0;border-radius:12px;padding:.7rem 1rem;font-size:.88rem;color:#64748b;background:#f8faff;cursor:pointer;transition:border-color .2s; }
    .form-file:hover { border-color:#2563eb; }
    .btn-save { display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 2rem;border-radius:50px;font-size:.92rem;font-weight:700;background:#f59e0b;color:#fff;border:none;cursor:pointer;transition:all .3s;box-shadow:0 4px 14px rgba(245,158,11,.25); }
    .btn-save:hover { background:#d97706;transform:translateY(-2px);box-shadow:0 8px 20px rgba(245,158,11,.3); }
    .btn-back { display:inline-flex;align-items:center;gap:.4rem;padding:.55rem 1.1rem;border-radius:50px;font-size:.85rem;font-weight:600;background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;text-decoration:none;transition:all .2s; }
    .btn-back:hover { background:#e2e8f0;color:#334155; }
    .error-alert { background:#fef2f2;border:1px solid #fecaca;border-radius:14px;padding:1rem 1.25rem;margin-bottom:1.5rem; }
    .error-alert ul { margin:0;padding-left:1.25rem;color:#991b1b;font-size:.88rem; }
    .current-img-box { background:#f8faff;border:1px solid #e2e8f0;border-radius:12px;padding:.75rem;text-align:center;margin-top:.5rem; }
    .info-note { background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:1rem 1.25rem;display:flex;gap:.75rem;align-items:flex-start;margin-top:1.5rem; }
    .info-note i { color:#2563eb;margin-top:2px;flex-shrink:0; }
</style>

<div class="admin-form-card shadow-sm mb-4">
    <div class="admin-form-header">
        <h5 class="admin-form-title">
            <div style="width:32px;height:32px;background:#fef9c3;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="fa-solid fa-pen-to-square text-warning fa-sm"></i>
            </div>
            Chỉnh Sửa Sản Phẩm
            <span style="font-size:.8rem;color:#94a3b8;font-weight:400;">— {{ $product->name }}</span>
        </h5>
        <a href="{{ route('admin.products.index') }}" class="btn-back">
            <i class="fa-solid fa-arrow-left fa-xs"></i> Quay lại
        </a>
    </div>

    <div class="p-4 p-md-5">
        @if($errors->any())
            <div class="error-alert">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="fa-solid fa-triangle-exclamation text-danger"></i>
                    <span class="fw-700 text-danger" style="font-weight:700;font-size:.9rem;">Vui lòng kiểm tra lại:</span>
                </div>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-section-label">
                <div style="width:24px;height:24px;border-radius:50%;background:#f59e0b;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:800;">1</div>
                Thông Tin Cơ Bản
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="form-label-custom">Tên sản phẩm <span class="req">*</span></label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', $product->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Danh mục <span class="req">*</span></label>
                    <select name="category_id" class="form-sel" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Thương hiệu <span class="req">*</span></label>
                    <input type="text" name="brand" class="form-input" value="{{ old('brand', $product->brand) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label-custom">Giá gốc (đ) <span class="req">*</span></label>
                    <input type="number" name="price" class="form-input" value="{{ old('price', $product->price) }}" required min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label-custom">Khối lượng (kg) <span class="req">*</span></label>
                    <input type="number" step="0.1" name="capacity_kg" class="form-input" value="{{ old('capacity_kg', $product->capacity_kg) }}" required min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Số lượng kho <span class="req">*</span></label>
                    <input type="number" name="stock_quantity" class="form-input" value="{{ old('stock_quantity', $product->stock_quantity) }}" required min="0">
                </div>
                <div class="col-md-8">
                    <label class="form-label-custom">Cập nhật ảnh chính</label>
                    <input type="file" name="image" class="form-file" accept="image/*">
                    @if($product->image)
                        <div class="current-img-box">
                            <span class="text-muted d-block mb-2" style="font-size:.75rem;font-weight:600;">Ảnh hiện tại:</span>
                            <img src="{{ asset('storage/' . $product->image) }}" class="rounded-3" style="width:80px;height:80px;object-fit:contain;border:1px solid #e2e8f0;background:#f8faff;padding:4px;">
                        </div>
                    @endif
                </div>
                <div class="col-12">
                    <label class="form-label-custom">Mô tả sản phẩm</label>
                    <textarea name="description" class="form-textarea">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>

            <div class="info-note">
                <i class="fa-solid fa-circle-info fa-sm"></i>
                <div>
                    <strong style="font-size:.88rem;font-weight:700;color:#1d4ed8;">Ghi chú về biến thể:</strong>
                    <p class="mb-0" style="font-size:.85rem;color:#334155;margin-top:.25rem;">Tính năng cập nhật biến thể màu sắc đang được tạm ẩn để tránh xung đột dữ liệu. Hãy tạo sản phẩm mới nếu muốn thay đổi cấu trúc màu sắc.</p>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn-save">
                    <i class="fa-solid fa-rotate"></i> Lưu Cập Nhật
                </button>
            </div>
        </form>
    </div>
</div>

@endsection