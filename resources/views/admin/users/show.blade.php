@extends('admin.layout')
@section('content')
    <div class="container-fluid">
        <h2 class="mb-4">Thông tin người dùng</h2>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="fw-bold" style="width: 150px;">ID:</td>
                        <td>{{ $user->id }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Tên:</td>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Email:</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Vai trò:</td>
                        <td>
                            <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary mt-3 px-4">Quay lại</a>
    </div>
@endsection