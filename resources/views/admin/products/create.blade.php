@extends('admin.layout')

@section('content')

<style>
    /* ── SHARED FORM STYLES ── */
    .admin-form-card { background:#fff;border:1px solid #e2e8f0;border-radius:20px;overflow:hidden; }
    .admin-form-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between;
        background: #fff;
    }
    .admin-form-title { font-size:1.05rem;font-weight:700;color:#0f172a;display:flex;align-items:center;gap:.6rem;margin:0; }

    /* Section dividers */
    .form-section-label {
        display: flex; align-items: center; gap: .6rem;
        font-size: .72rem; font-weight: 700; letter-spacing: 1.5px;
        text-transform: uppercase; color: #94a3b8;
        margin-bottom: 1.25rem;
        padding-bottom: .75rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .form-section-num {
        width: 24px; height: 24px; border-radius: 50%;
        background: #2563eb; color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: .72rem; font-weight: 800;
    }

    /* Form controls */
    .form-label-custom {
        font-size: .78rem; font-weight: 700;
        color: #334155; margin-bottom: .4rem; display: block;
    }
    .form-label-custom .req { color: #ef4444; margin-left: 2px; }
    .form-input, .form-textarea, .form-sel {
        width: 100%;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: .7rem 1rem;
        font-size: .92rem; color: #0f172a;
        background: #fff; outline: none;
        transition: border-color .2s, box-shadow .2s;
        appearance: none;
    }
    .form-input:focus, .form-textarea:focus, .form-sel:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37,99,235,.08);
    }
    .form-input::placeholder, .form-textarea::placeholder { color: #94a3b8; }
    .form-textarea { resize: vertical; min-height: 110px; }

    /* Color input */
    .form-color { width:100%;height:44px;border-radius:10px;border:2px solid #e2e8f0;padding:3px;cursor:pointer; }

    /* File input */
    .form-file {
        width: 100%;
        border: 2px dashed #e2e8f0;
        border-radius: 12px;
        padding: .7rem 1rem;
        font-size: .88rem; color: #64748b;
        background: #f8faff; cursor: pointer;
        transition: border-color .2s;
    }
    .form-file:hover { border-color: #2563eb; }

    /* Variant table */
    .variant-table { width:100%;border-collapse:separate;border-spacing:0; }
    .variant-table thead th {
        padding: .6rem .75rem;
        font-size: .72rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;
        color: #94a3b8; background: #f8faff;
        border-bottom: 1px solid #e2e8f0;
    }
    .variant-table tbody td {
        padding: .6rem .75rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        background: #fff;
    }
    .variant-table tbody tr:last-child td { border-bottom: none; }
    .variant-table tbody tr:hover td { background: #fafbff; }

    /* Variant remove btn */
    .btn-remove-row {
        width: 30px; height: 30px; border-radius: 8px;
        background: #fef2f2; color: #ef4444;
        border: 1px solid #fecaca;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all .2s;
    }
    .btn-remove-row:hover { background: #ef4444; color: #fff; border-color: #ef4444; }

    /* Action buttons */
    .btn-save {
        display: inline-flex; align-items: center; gap: .5rem;
        padding: .75rem 2rem; border-radius: 50px;
        font-size: .92rem; font-weight: 700;
        background: #2563eb; color: #fff; border: none;
        cursor: pointer; transition: all .3s;
        box-shadow: 0 4px 14px rgba(37,99,235,.25);
    }
    .btn-save:hover { background: #1d4ed8; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(37,99,235,.3); }
    .btn-back {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .55rem 1.1rem; border-radius: 50px;
        font-size: .85rem; font-weight: 600;
        background: #f1f5f9; color: #64748b;
        border: 1px solid #e2e8f0; text-decoration: none;
        transition: all .2s;
    }
    .btn-back:hover { background: #e2e8f0; color: #334155; }
    .btn-add-variant {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .45rem 1rem; border-radius: 50px;
        font-size: .82rem; font-weight: 600;
        background: #eff6ff; color: #2563eb;
        border: 1.5px solid #bfdbfe; cursor: pointer;
        transition: all .2s;
    }
    .btn-add-variant:hover { background: #2563eb; color: #fff; border-color: #2563eb; }

    /* Error alert */
    .error-alert {
        background: #fef2f2; border: 1px solid #fecaca; border-radius: 14px;
        padding: 1rem 1.25rem; margin-bottom: 1.5rem;
    }
    .error-alert ul { margin: 0; padding-left: 1.25rem; color: #991b1b; font-size: .88rem; }
</style>

<div class="admin-form-card shadow-sm mb-4">
    <div class="admin-form-header">
        <h5 class="admin-form-title">
            <div style="width:32px;height:32px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="fa-solid fa-plus text-primary fa-sm"></i>
            </div>
            Thêm Sản Phẩm Mới
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
                    <span class="fw-700 text-danger" style="font-weight:700;font-size:.9rem;">Vui lòng kiểm tra lại các trường sau:</span>
                </div>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Section 1: Thông tin cơ bản --}}
            <div class="form-section-label">
                <div class="form-section-num">1</div>
                Thông Tin Cơ Bản
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="form-label-custom">Tên sản phẩm <span class="req">*</span></label>
                    <input type="text" name="name" class="form-input"
                           value="{{ old('name') }}"
                           placeholder="VD: Máy giặt LG Inverter 9kg FC1409S2W" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Danh mục <span class="req">*</span></label>
                    <select name="category_id" class="form-sel" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Thương hiệu <span class="req">*</span></label>
                    <input type="text" name="brand" class="form-input"
                           value="{{ old('brand') }}"
                           placeholder="VD: LG, Panasonic, Toshiba" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label-custom">Giá gốc (đ) <span class="req">*</span></label>
                    <input type="number" name="price" class="form-input"
                           value="{{ old('price') }}" placeholder="VD: 8500000" required min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label-custom">Khối lượng giặt (kg) <span class="req">*</span></label>
                    <input type="number" step="0.1" name="capacity_kg" class="form-input"
                           value="{{ old('capacity_kg') }}" placeholder="VD: 9.0" required min="0">
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Ảnh đại diện chính</label>
                    <input type="file" name="image" id="image" class="form-file" accept="image/*">
                </div>
                <div class="col-12">
                    <label class="form-label-custom">Mô tả sản phẩm</label>
                    <textarea name="description" class="form-textarea"
                              placeholder="Nhập mô tả chi tiết tính năng máy giặt...">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- Section 2: Biến thể --}}
            <div class="form-section-label mt-4">
                <div class="form-section-num">2</div>
                Phân Loại Màu Sắc (Biến Thể)
                <button type="button" id="add-variant-btn" class="btn-add-variant ms-auto">
                    <i class="fa-solid fa-plus fa-xs"></i> Thêm màu
                </button>
            </div>

            <div class="table-responsive rounded-3 border mb-5" style="border-color:#e2e8f0 !important;">
                <table class="variant-table">
                    <thead>
                        <tr>
                            <th>Tên màu <span style="color:#ef4444;">*</span></th>
                            <th style="width:100px;">Mã màu</th>
                            <th>Giá riêng (đ) <span style="color:#ef4444;">*</span></th>
                            <th style="width:110px;">Tồn kho <span style="color:#ef4444;">*</span></th>
                            <th>Ảnh màu</th>
                            <th style="width:55px;" class="text-center">Xóa</th>
                        </tr>
                    </thead>
                    <tbody id="variants-tbody">
                        <tr>
                            <td>
                                <input type="text" name="variants[0][color_name]" class="form-input" placeholder="VD: Đỏ / Bạc" required>
                                <input type="hidden" name="variants[0][sku]" value="SKU-{{ rand(1000,9999) }}">
                            </td>
                            <td>
                                <input type="color" name="variants[0][color_code]" class="form-color" value="#ff0000">
                            </td>
                            <td>
                                <input type="number" name="variants[0][price]" class="form-input" placeholder="8500000" required min="0">
                            </td>
                            <td>
                                <input type="number" name="variants[0][stock]" class="form-input" value="10" required min="0">
                            </td>
                            <td>
                                <input type="file" name="variants[0][image]" class="form-file" accept="image/*">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn-remove-row remove-row">
                                    <i class="fa-solid fa-xmark fa-xs"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn-save">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu Sản Phẩm
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let variantIndex = 1;
    document.getElementById('add-variant-btn').addEventListener('click', function () {
        let tbody = document.getElementById('variants-tbody');
        let randomSku = Math.floor(1000 + Math.random() * 9000);
        let newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <input type="text" name="variants[${variantIndex}][color_name]" class="form-input" placeholder="VD: Đen / Trắng" required>
                <input type="hidden" name="variants[${variantIndex}][sku]" value="SKU-${randomSku}">
            </td>
            <td><input type="color" name="variants[${variantIndex}][color_code]" class="form-color" value="#000000"></td>
            <td><input type="number" name="variants[${variantIndex}][price]" class="form-input" placeholder="8500000" required min="0"></td>
            <td><input type="number" name="variants[${variantIndex}][stock]" class="form-input" value="10" required min="0"></td>
            <td><input type="file" name="variants[${variantIndex}][image]" class="form-file" accept="image/*"></td>
            <td class="text-center">
                <button type="button" class="btn-remove-row remove-row"><i class="fa-solid fa-xmark fa-xs"></i></button>
            </td>
        `;
        tbody.appendChild(newRow);
        variantIndex++;
    });

    document.addEventListener('click', function (e) {
        if (e.target && e.target.closest('.remove-row')) {
            let row = e.target.closest('tr');
            if (document.querySelectorAll('#variants-tbody tr').length > 1) {
                row.remove();
            } else {
                alert('Bạn phải giữ lại ít nhất một phân loại màu!');
            }
        }
    });
</script>

@endsection