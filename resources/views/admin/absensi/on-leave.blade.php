@extends('adminlte::page')
@section('title', 'Karyawan Izin / Cuti')
@section('content_header')<h1>Karyawan Izin / Cuti</h1>@stop
@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush
@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" class="form-inline">
            <div class="form-group mr-2">
                <label class="mr-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control form-control-sm">
            </div>
            <button type="submit" class="btn btn-sm btn-info"><i class="fas fa-search"></i> Tampilkan</button>
        </form>
    </div>
    <div class="card-body">
        <h5 class="mb-3">Sedang Izin/Cuti ({{ $leaveRequests->count() }})</h5>
        <table class="table table-bordered table-hover" id="leaveTable">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Bagian</th>
                    <th>Jenis</th>
                    <th>Dari</th>
                    <th>Sampai</th>
                    <th>Hari</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaveRequests as $l)
                <tr>
                    <td>{{ $l->user->name }}</td>
                    <td>{{ $l->user->employeeDetail?->division?->nama_bagian ?? '-' }}</td>
                    <td><span class="badge badge-info">{{ $l->leaveType->nama ?? '-' }}</span></td>
                    <td>{{ $l->tanggal_mulai }}</td>
                    <td>{{ $l->tanggal_selesai }}</td>
                    <td>{{ $l->jumlah_hari }} hari</td>
                    <td>{{ $l->keterangan }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted">Tidak ada</td></tr>
                @endforelse
            </tbody>
        </table>

        <hr class="mt-4">
        <h5 class="mb-3">Semua Pengajuan Aktif</h5>
        <table class="table table-bordered table-hover" id="activeTable">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Bagian</th>
                    <th>Jenis</th>
                    <th>Dari</th>
                    <th>Sampai</th>
                    <th>Hari</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activeLeaves as $l)
                <tr>
                    <td>{{ $l->user->name }}</td>
                    <td>{{ $l->user->employeeDetail?->division?->nama_bagian ?? '-' }}</td>
                    <td><span class="badge badge-info">{{ $l->leaveType->nama ?? '-' }}</span></td>
                    <td>{{ $l->tanggal_mulai }}</td>
                    <td>{{ $l->tanggal_selesai }}</td>
                    <td>{{ $l->jumlah_hari }} hari</td>
                    <td>
                        @switch($l->status)
                            @case('pending') <span class="badge badge-warning">Pending</span> @break
                            @case('approved') <span class="badge badge-success">Disetujui</span> @break
                            @default <span class="badge badge-secondary">{{ $l->status }}</span>
                        @endswitch
                    </td>
                    <td>{{ $l->keterangan }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted">Tidak ada</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop
@push('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>$('#leaveTable, #activeTable').DataTable({ordering:true, paging:false, info:false})</script>
@endpush
