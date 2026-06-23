@extends('adminlte::page')

@section('title', 'User Management')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>User Management</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">+ Tambah User</a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                    <tr>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td><span class="badge {{ $u->is_admin ? 'badge-warning' : 'badge-info' }}">{{ $u->is_admin ? 'Admin' : 'User' }}</span></td>
                        <td>{{ $u->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-warning">Edit</a>
                            @if($u->id !== auth()->id())
                            <form action="{{ route('admin.users.delete', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="card-footer">{{ $users->links() }}</div>
        @endif
    </div>
@stop
