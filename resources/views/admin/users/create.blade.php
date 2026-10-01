@extends('admin.layout')
@section('content')
    <div class="container-fluid">
        <h2 class="mb-4">Thêm người dùng</h2>
        <div class="card card-body shadow-sm border-0">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label class="fw-bold mb-1">Tên</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group mb-3">
                    <label class="fw-bold mb-1">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group mb-3">
                    <label class="fw-bold mb-1">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group mb-4">
                    <label class="fw-bold mb-1">Vai trò</label>
                    <select name="role" class="form-select" required>
                        <option value="user">Người dùng</option>
                        <option value="admin">Quản trị</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success fw-bold px-4">Lưu</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary px-4">Hủy</a>
            </form>
        </div>
    </div>
@endsection