@extends('adminlte::page')
@section('title', 'Rekap Absen Harian')
@section('content_header')<h1>Rekap Absen Harian</h1>@stop
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
                <label class="mr-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control form-control-sm">
            </div>
            <button type="submit" class="btn btn-sm btn-info"><i class="fas fa-search"></i> Tampilkan</button>
        </form>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-4 col-md-2 mb-2">
                <div class="stat-card bg-success text-white">
                    <div class="num">{{ $stats['hadir'] }}</div>
                    <div class="label">Hadir</div>
                </div>
            </div>
            <div class="col-4 col-md-2 mb-2">
                <div class="stat-card bg-warning text-white">
                    <div class="num">{{ $stats['terlambat'] }}</div>
                    <div class="label">Terlambat</div>
                </div>
            </div>
            <div class="col-4 col-md-2 mb-2">
                <div class="stat-card bg-info text-white">
                    <div class="num">{{ $stats['izin'] }}</div>
                    <div class="label">Izin</div>
                </div>
            </div>
            <div class="col-4 col-md-2 mb-2">
                <div class="stat-card bg-secondary text-white">
                    <div class="num">{{ $stats['sakit'] }}</div>
                    <div class="label">Sakit</div>
                </div>
            </div>
            <div class="col-4 col-md-2 mb-2">
                <div class="stat-card bg-primary text-white">
                    <div class="num">{{ $stats['cuti'] }}</div>
                    <div class="label">Cuti</div>
                </div>
            </div>
            <div class="col-4 col-md-2 mb-2">
                <div class="stat-card bg-danger text-white">
                    <div class="num">{{ $stats['alpha'] }}</div>
                    <div class="label">Alpha</div>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-hover" id="dailyTable">
            <thead>
                <tr>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Bagian</th>
                    <th>Shift</th>
                    <th>Status</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Terlambat</th>
                    <th>WFH/WFO</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $r)
                <tr>
                    <td><small>{{ $r['user']->nip ?? '-' }}</small></td>
                    <td><a href="{{ route('admin.absensi.detail', $r['user']) }}?dari={{ $tanggal }}&sampai={{ $tanggal }}" class="text-indigo-600 hover:underline font-weight-medium">{{ $r['user']->name }}</a></td>
                    <td>{{ $r['user']->employeeDetail?->division?->nama_bagian ?? '-' }}</td>
                    <td>{{ $r['sch']?->shift?->nama ?? '-' }}</td>
                    <td>
                        @php $cls = str_replace(['hadir','terlambat','alpha','izin','sakit','cuti'],['hadir','terlambat','alpha','izin','sakit','cuti'], $r['status']); @endphp
                        <span class="status-badge status-{{ $r['status'] }}">{{ ucfirst($r['status']) }}</span>
                    </td>
                    <td>{{ $r['att']?->check_in ? substr($r['att']->check_in, 0, 5) : '-' }}</td>
                    <td>{{ $r['att']?->check_out ? substr($r['att']->check_out, 0, 5) : '-' }}</td>
                    <td>{{ $r['att']?->terlambat_menit ? $r['att']->terlambat_menit.' menit' : ($r['status']==='terlambat' ? 'Ya' : '-') }}</td>
                    <td>{{ strtoupper($r['att']?->work_from ?? ($r['sch']?->shift?->nama ?? '-')) }}</td>
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
<script>$('#dailyTable').DataTable({ordering:true, paging:false, info:false})</script>
@endpush
