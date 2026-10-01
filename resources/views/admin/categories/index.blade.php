@extends('admin.layout')

@section('content')

<style>
    .admin-card { background:#fff;border:1px solid #e2e8f0;border-radius:20px;overflow:hidden; }
    .admin-card-header { padding:1.25rem 1.75rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem; }
    .admin-card-title { font-size:1rem;font-weight:700;color:#0f172a;display:flex;align-items:center;gap:.5rem;margin:0; }
    .btn-add-new { display:inline-flex;align-items:center;gap:.4rem;padding:.55rem 1.1rem;border-radius:50px;font-size:.85rem;font-weight:600;background:#2563eb;color:#fff;border:none;text-decoration:none;transition:all .2s;box-shadow:0 2px 8px rgba(37,99,235,.2); }
    .btn-add-new:hover { background:#1d4ed8;color:#fff;transform:translateY(-1px); }
    .admin-table { width:100%;border-collapse:separate;border-spacing:0; }
    .admin-table thead th { padding:.7rem 1rem;font-size:.72rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;background:#f8faff;border-bottom:1px solid #e2e8f0; }
    .admin-table tbody td { padding:.9rem 1rem;border-bottom:1px solid #f1f5f9;vertical-align:middle;font-size:.9rem;background:#fff; }
    .admin-table tbody tr:last-child td { border-bottom:none; }
    .admin-table tbody tr:hover td { background:#f8faff; }
    .btn-action { width:32px;height:32px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;text-decoration:none;border:none;cursor:pointer;transition:all .2s; }
    .btn-action.edit { background:#fef9c3;color:#92400e;border:1px solid #fde68a; }
    .btn-action.edit:hover { background:#f59e0b;color:#fff;border-color:#f59e0b; }
    .btn-action.delete { background:#fef2f2;color:#991b1b;border:1px solid #fecaca; }
    .btn-action.delete:hover { background:#ef4444;color:#fff;border-color:#ef4444; }
    .cat-row-icon { width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#eff6ff,#dbeafe);display:flex;align-items:center;justify-content:center;flex-shrink:0; }
</style>

<div class="admin-card shadow-sm">
    <div class="admin-card-header">
        <h5 class="admin-card-title">
            <i class="fa-solid fa-layer-group text-primary"></i> Danh Mục Sản Phẩm
        </h5>
        <a href="{{ route('admin.categories.create') }}" class="btn-add-new">
            <i class="fa-solid fa-plus fa-xs"></i> Thêm Danh Mục
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

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:48px;">ID</th>
                        <th>Tên Danh Mục</th>
                        <th>Mô Tả</th>
                        <th class="text-center" style="width:100px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="text-muted" style="font-size:.78rem;font-weight:600;">#{{ $category->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="cat-row-icon">
                                        <i class="fa-solid fa-layer-group text-primary fa-sm"></i>
                                    </div>
                                    <span class="fw-700 text-dark" style="font-weight:700;font-size:.92rem;">{{ $category->name }}</span>
                                </div>
                            </td>
                            <td class="text-muted" style="font-size:.88rem;">{{ $category->description ?? '—' }}</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn-action edit" title="Chỉnh sửa">
                                        <i class="fa-solid fa-pen-to-square fa-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?')">
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
                            <td colspan="4" class="text-center py-5">
                                <i class="fa-solid fa-folder-open fa-2x text-muted opacity-30 mb-2 d-block"></i>
                                <span class="text-muted">Chưa có danh mục nào. Hãy thêm mới!</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-4 pt-3 border-top" style="border-color:#f1f5f9 !important;">
            {{ $categories->links() }}
        </div>
    </div>
</div>

@endsection