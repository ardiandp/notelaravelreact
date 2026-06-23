@extends('adminlte::page')
@section('title', 'Approval Chains')
@section('content_header')<h1>Approval Chains</h1>@stop
@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush
@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card">
    <div class="card-header"><h3 class="card-title">Daftar Approval Chain</h3><a href="{{ route('admin.approval-chains.create') }}" class="btn btn-primary btn-sm float-right">+ Tambah Chain</a></div>
    <div class="card-body">
        <table id="approvalTable" class="table table-hover">
            <thead><tr><th>Nama</th><th>Slug</th><th>Steps</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($chains as $c)
                <tr>
                    <td>{{ $c->nama }}</td>
                    <td><code>{{ $c->slug }}</code></td>
                    <td>
                        @forelse($c->steps as $s)
                            <span class="badge badge-info mr-1">Step {{ $s->step_order }}: {{ $s->approver_type }}@if($s->role) ({{ $s->role->name }})@endif</span>
                        @empty
                            <span class="text-muted">No steps</span>
                        @endforelse
                    </td>
                    <td>
                        <a href="{{ route('admin.approval-chains.edit', $c) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.approval-chains.delete', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus chain ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
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
<script>$('#approvalTable').DataTable()</script>
@endpush
