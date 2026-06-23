@extends('adminlte::page')
@section('title', 'Detail Absensi - '.$user->name)
@section('content_header')
<h1>Detail Absensi: {{ $user->name }}
    <small class="text-muted">({{ $user->nip ?? 'N/A' }}) &middot; {{ $user->employeeDetail?->division?->nama_bagian ?? '-' }}</small>
</h1>
@stop
@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
.status-badge { font-size: .75rem; padding: 2px 8px; border-radius: 10px; white-space: nowrap; }
.status-hadir { background: #d4edda; color: #155724; }
.status-terlambat { background: #fff3cd; color: #856404; }
.status-alpha { background: #f8d7da; color: #721c24; }
.status-izin { background: #d1ecf1; color: #0c5460; }
.status-sakit { background: #e2d9f3; color: #563d7c; }
.status-cuti { background: #cce5ff; color: #004085; }
.stat-card { border-radius: 10px; padding: 12px 16px; text-align: center; }
.stat-card .num { font-size: 1.5rem; font-weight: 700; }
.stat-card .label { font-size: .75rem; }
</style>
@endpush
@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" class="form-inline">
            <div class="form-group mr-2">
                <label class="mr-1">Dari</label>
                <input type="date" name="dari" value="{{ $dari }}" class="form-control form-control-sm">
            </div>
            <div class="form-group mr-2">
                <label class="mr-1">Sampai</label>
                <input type="date" name="sampai" value="{{ $sampai }}" class="form-control form-control-sm">
            </div>
            <button type="submit" class="btn btn-sm btn-info mr-2"><i class="fas fa-search"></i> Tampilkan</button>
            <a href="{{ route('admin.absensi.daily') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </form>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-4 col-md-2 mb-2">
                <div class="stat-card bg-success text-white">
                    <div class="num">{{ $totalHadir }}</div>
                    <div class="label">Hadir</div>
                </div>
            </div>
            <div class="col-4 col-md-2 mb-2">
                <div class="stat-card bg-warning text-white">
                    <div class="num">{{ $totalTerlambat }}</div>
                    <div class="label">Telat</div>
                </div>
            </div>
            <div class="col-4 col-md-2 mb-2">
                <div class="stat-card bg-danger text-white">
                    <div class="num">{{ $totalAlpha }}</div>
                    <div class="label">Alpha</div>
                </div>
            </div>
            <div class="col-4 col-md-2 mb-2">
                <div class="stat-card bg-info text-white">
                    <div class="num">{{ $totalIzin }}</div>
                    <div class="label">Izin</div>
                </div>
            </div>
            <div class="col-4 col-md-2 mb-2">
                <div class="stat-card bg-secondary text-white">
                    <div class="num">{{ $totalSakit }}</div>
                    <div class="label">Sakit</div>
                </div>
            </div>
            <div class="col-4 col-md-2 mb-2">
                <div class="stat-card bg-primary text-white">
                    <div class="num">{{ $totalCuti }}</div>
                    <div class="label">Cuti</div>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-hover" id="detailTable">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Shift</th>
                    <th>Status</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Terlambat</th>
                    <th>WFH/WFO</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $att)
                <tr>
                    <td>{{ $att->tanggal }}</td>
                    <td>{{ $att->shift?->nama ?? '-' }}</td>
                    <td><span class="status-badge status-{{ $att->status }}">{{ ucfirst($att->status) }}</span></td>
                    <td>{{ $att->check_in ? substr($att->check_in, 0, 5) : '-' }}</td>
                    <td>{{ $att->check_out ? substr($att->check_out, 0, 5) : '-' }}</td>
                    <td>{{ $att->terlambat_menit ? $att->terlambat_menit.' menit' : '-' }}</td>
                    <td>{{ strtoupper($att->work_from ?? '-') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted">Tidak ada data absensi</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($leaves->isNotEmpty())
        <hr class="mt-4">
        <h5 class="mb-3">Cuti / Izin di Periode Ini</h5>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Jenis</th>
                    <th>Dari</th>
                    <th>Sampai</th>
                    <th>Hari</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leaves as $l)
                <tr>
                    <td><span class="badge badge-info">{{ $l->leaveType->nama ?? '-' }}</span></td>
                    <td>{{ $l->tanggal_mulai }}</td>
                    <td>{{ $l->tanggal_selesai }}</td>
                    <td>{{ $l->jumlah_hari }} hari</td>
                    <td>{{ $l->keterangan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@stop
@push('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>$('#detailTable').DataTable({ordering:true, paging:false, info:false})</script>
@endpush
