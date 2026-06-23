@extends('adminlte::page')
@section('title', 'Jenis Cuti')
@section('content_header')<h1>Jenis Cuti</h1>@stop
@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush
@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card">
    <div class="card-header"><h3 class="card-title">Daftar Jenis Cuti</h3><a href="{{ route('admin.leave-types.create') }}" class="btn btn-primary btn-sm float-right">+ Tambah Jenis Cuti</a></div>
    <div class="card-body">
        <table id="leaveTypeTable" class="table table-hover">
            <thead><tr><th>Nama</th><th>Kuota/Tahun</th><th>Dibayar</th><th>Approval Chain</th><th>Aktif</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($leaveTypes as $lt)
                <tr>
                    <td>{{ $lt->nama }} <code class="ml-1">{{ $lt->slug }}</code></td>
                    <td>{{ $lt->kuota_per_tahun ?? '-' }} hari</td>
                    <td><span class="badge {{ $lt->is_paid ? 'badge-success' : 'badge-secondary' }}">{{ $lt->is_paid ? 'Ya' : 'Tidak' }}</span></td>
                    <td>{{ $lt->approvalChain?->nama ?? '-' }}</td>
                    <td><span class="badge {{ $lt->is_active ? 'badge-success' : 'badge-secondary' }}">{{ $lt->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td>
                        <a href="{{ route('admin.leave-types.edit', $lt) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.leave-types.delete', $lt) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus jenis cuti ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop
@push('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>$('#leaveTypeTable').DataTable()</script>
@endpush
