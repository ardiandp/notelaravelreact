@extends('adminlte::page')
@section('title', 'Jabatan')
@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Jabatan / Posisi</h1>
        <a href="{{ route('admin.positions.create') }}" class="btn btn-primary btn-sm">+ Tambah Jabatan</a>
    </div>
@stop
@section('content')
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover">
                <thead><tr><th>Nama</th><th>Divisi</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach($positions as $p)
                    <tr>
                        <td>{{ $p->nama }}</td>
                        <td>{{ $p->division?->nama_bagian ?? '-' }}</td>
                        <td>
                            <a href="{{ route('admin.positions.edit', $p) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('admin.positions.delete', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus jabatan ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
