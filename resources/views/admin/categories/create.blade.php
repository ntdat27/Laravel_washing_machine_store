@extends('admin.layout')

@section('content')

<style>
    .admin-form-card { background:#fff;border:1px solid #e2e8f0;border-radius:20px;overflow:hidden; }
    .admin-form-header { padding:1.25rem 1.75rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between; }
    .admin-form-title { font-size:1.05rem;font-weight:700;color:#0f172a;display:flex;align-items:center;gap:.6rem;margin:0; }
    .form-label-custom { font-size:.78rem;font-weight:700;color:#334155;margin-bottom:.4rem;display:block; }
    .form-label-custom .req { color:#ef4444;margin-left:2px; }
    .form-input, .form-textarea { width:100%;border:2px solid #e2e8f0;border-radius:12px;padding:.7rem 1rem;font-size:.92rem;color:#0f172a;background:#fff;outline:none;transition:border-color .2s,box-shadow .2s; }
    .form-input:focus, .form-textarea:focus { border-color:#2563eb;box-shadow:0 0 0 4px rgba(37,99,235,.08); }
    .form-input::placeholder, .form-textarea::placeholder { color:#94a3b8; }
    .form-textarea { resize:vertical;min-height:120px; }
    .btn-save { display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 2rem;border-radius:50px;font-size:.92rem;font-weight:700;background:#2563eb;color:#fff;border:none;cursor:pointer;transition:all .3s;box-shadow:0 4px 14px rgba(37,99,235,.25); }
    .btn-save:hover { background:#1d4ed8;transform:translateY(-2px);box-shadow:0 8px 20px rgba(37,99,235,.3); }
    .btn-back { display:inline-flex;align-items:center;gap:.4rem;padding:.55rem 1.1rem;border-radius:50px;font-size:.85rem;font-weight:600;background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;text-decoration:none;transition:all .2s; }
    .btn-back:hover { background:#e2e8f0;color:#334155; }
    .error-alert { background:#fef2f2;border:1px solid #fecaca;border-radius:14px;padding:1rem 1.25rem;margin-bottom:1.5rem; }
    .error-alert ul { margin:0;padding-left:1.25rem;color:#991b1b;font-size:.88rem; }
</style>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="admin-form-card shadow-sm">
            <div class="admin-form-header">
                <h5 class="admin-form-title">
                    <div style="width:32px;height:32px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="fa-solid fa-plus text-primary fa-sm"></i>
                    </div>
                    Thêm Danh Mục Mới
                </h5>
                <a href="{{ route('admin.categories.index') }}" class="btn-back">
                    <i class="fa-solid fa-arrow-left fa-xs"></i> Quay lại
                </a>
            </div>

            <div class="p-4 p-md-5">
                @if($errors->any())
                    <div class="error-alert">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="fa-solid fa-triangle-exclamation text-danger"></i>
                            <span class="fw-700 text-danger" style="font-weight:700;font-size:.9rem;">Có lỗi xảy ra:</span>
                        </div>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label-custom">Tên danh mục <span class="req">*</span></label>
                        <input type="text" name="name" class="form-input"
                               placeholder="VD: Máy giặt cửa ngang, Máy giặt sấy..."
                               value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-5">
                        <label class="form-label-custom">Mô tả</label>
                        <textarea name="description" class="form-textarea"
                                  placeholder="Nhập mô tả chi tiết cho danh mục này...">{{ old('description') }}</textarea>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.categories.index') }}" class="btn-back">
                            <i class="fa-solid fa-arrow-left fa-xs"></i> Hủy
                        </a>
                        <button type="submit" class="btn-save">
                            <i class="fa-solid fa-floppy-disk fa-sm"></i> Lưu Danh Mục
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection