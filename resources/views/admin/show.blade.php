@extends('layouts.admin')
@section('content')
    <div class="container-fluid">
        <h2 class="mb-4">Thông tin người dùng</h2>
        <div class="card">
            <div class="card-body">
                <p><strong>ID:</strong> {{ $user->id }}</p>
                <p><strong>Tên:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Vai trò:</strong>
                    <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }}">{{ $user->role }}</span>
                </p>
            </div>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary mt-3">Quay lại</a>
    </div>
@endsection