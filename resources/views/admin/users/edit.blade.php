@extends('admin.layout')
@section('content')
    <div class="container-fluid">
        <h2 class="mb-4">Chỉnh sửa người dùng</h2>
        <div class="card card-body shadow-sm border-0">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group mb-3">
                    <label class="fw-bold mb-1">Tên</label>
                    <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
                </div>
                <div class="form-group mb-3">
                    <label class="fw-bold mb-1">Email</label>
                    <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
                </div>
                <div class="form-group mb-4">
                    <label class="fw-bold mb-1">Vai trò</label>
                    <select name="role" class="form-select" required>
                        <option value="user" @selected($user->role === 'user')>Người dùng</option>
                        <option value="admin" @selected($user->role === 'admin')>Quản trị</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary fw-bold px-4">Cập nhật</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary px-4">Hủy</a>
            </form>
        </div>
    </div>
@endsection