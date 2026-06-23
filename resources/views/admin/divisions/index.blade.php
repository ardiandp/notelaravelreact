@extends('adminlte::page')
@section('title', 'Bagian')
@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Bagian / Divisi</h1>
        <a href="{{ route('admin.divisions.create') }}" class="btn btn-primary btn-sm">+ Tambah Bagian</a>
    </div>
@stop
@section('content')
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover">
                <thead><tr><th>Nama</th><th>Parent</th><th>Kepala</th><th>Aktif</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach($divisions as $d)
                    <tr>
                        <td>{{ $d->nama_bagian }}</td>
                        <td>{{ $d->parent?->nama_bagian ?? '-' }}</td>
                        <td>{{ $d->head?->name ?? '-' }}</td>
                        <td><span class="badge {{ $d->is_active ? 'badge-success' : 'badge-secondary' }}">{{ $d->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td>
                            <a href="{{ route('admin.divisions.edit', $d) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('admin.divisions.delete', $d) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus bagian ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
