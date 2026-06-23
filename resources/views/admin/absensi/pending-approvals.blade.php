@extends('adminlte::page')
@section('title', 'Persetujuan Cuti / Izin')
@section('content_header')<h1>Persetujuan Cuti / Izin</h1>@stop
@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
.card-header-tabs { border-bottom: 0; }
.card-header-tabs .nav-link {
    border: none; border-radius: 8px 8px 0 0;
    color: #6c757d; font-weight: 500; padding: 10px 20px;
    transition: all .2s ease;
    position: relative;
}
.card-header-tabs .nav-link:not(.active):hover {
    background: rgba(0,0,0,.03); color: #495057;
}
.card-header-tabs .nav-link.active {
    color: #4f46e5; background: #fff;
    box-shadow: 0 -2px 8px rgba(79,70,229,.12);
}
.card-header-tabs .nav-link.active::after {
    content: ''; position: absolute; bottom: -1px; left: 0; right: 0; height: 2px;
    background: #4f46e5; border-radius: 1px 1px 0 0;
}
.card-header-tabs .nav-link .badge {
    font-size: .7rem; vertical-align: middle;
}
</style>
@endpush
@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

<div class="card">
    <div class="card-header p-0">
        <ul class="nav nav-tabs card-header-tabs ml-3 pt-2" id="approvalTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="pending-tab" data-toggle="tab" href="#pending" role="tab">
                    Belum Diproses <span class="badge badge-warning ml-1">{{ $pending->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab">Riwayat</a>
            </li>
        </ul>
    </div>
    <div class="card-body p-0 tab-content">
        <div class="tab-pane active" id="pending" role="tabpanel">
            @if($pending->isEmpty())
                <p class="text-muted text-center py-4">Tidak ada pengajuan pending</p>
            @else
            <table class="table table-bordered table-hover mb-0" id="pendingTable">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>Bagian</th>
                        <th>Jenis</th>
                        <th>Lama</th>
                        <th>Keterangan</th>
                        <th>Approval Chain</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pending as $r)
                    <tr>
                        <td><small>{{ $r->created_at->format('d/m/Y') }}</small></td>
                        <td>{{ $r->user->name }}</td>
                        <td>{{ $r->user->employeeDetail?->division?->nama_bagian ?? '-' }}</td>
                        <td><span class="badge badge-info">{{ $r->leaveType->nama ?? '-' }}</span></td>
                        <td>{{ $r->tanggal_mulai }} s/d {{ $r->tanggal_selesai }} <br><small>({{ $r->jumlah_hari }} hari)</small></td>
                        <td><small>{{ Str::limit($r->keterangan, 40) }}</small></td>
                        <td><small>{{ $r->approvalChain?->nama ?? '-' }}</small></td>
                        <td>
                            @php $stepNow = $r->approvals->where('status','pending')->first(); @endphp
                            @if($stepNow)
                                <small>Step {{ $stepNow->step_order }}</small>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                        </td>
                        <td class="text-nowrap">
                            <form method="POST" action="{{ route('admin.absensi.approve', $r) }}" class="d-inline" onsubmit="return confirm('Setujui?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check"></i></button>
                            </form>
                            <button type="button" class="btn btn-sm btn-danger" onclick="showReject({{ $r->id }})"><i class="fas fa-times"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
        <div class="tab-pane" id="history" role="tabpanel">
            <table class="table table-bordered table-hover mb-0" id="historyTable">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>Bagian</th>
                        <th>Jenis</th>
                        <th>Lama</th>
                        <th>Status</th>
                        <th>Approval</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $r)
                    <tr>
                        <td><small>{{ $r->created_at->format('d/m/Y') }}</small></td>
                        <td>{{ $r->user->name }}</td>
                        <td>{{ $r->user->employeeDetail?->division?->nama_bagian ?? '-' }}</td>
                        <td><span class="badge badge-info">{{ $r->leaveType->nama ?? '-' }}</span></td>
                        <td><small>{{ $r->tanggal_mulai }} s/d {{ $r->tanggal_selesai }} ({{ $r->jumlah_hari }} hr)</small></td>
                        <td>
                            @switch($r->status)
                                @case('approved') <span class="badge badge-success">Disetujui</span> @break
                                @case('rejected') <span class="badge badge-danger">Ditolak</span> @break
                                @case('cancelled') <span class="badge badge-secondary">Batal</span> @break
                                @default <span class="badge badge-warning">{{ $r->status }}</span>
                            @endswitch
                        </td>
                        <td>
                            @foreach($r->approvals as $a)
                                <small class="d-block {{ $a->status === 'approved' ? 'text-success' : ($a->status === 'rejected' ? 'text-danger' : 'text-muted') }}">
                                    Step {{ $a->step_order }}: {{ $a->approver?->name ?? '-' }} ({{ $a->status }})
                                </small>
                            @endforeach
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted">Belum ada riwayat</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form method="POST" id="rejectForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Pengajuan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <textarea name="alasan" class="form-control" rows="3" placeholder="Alasan penolakan (opsional)"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
@push('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
function initDT(id) { if (!$.fn.DataTable.isDataTable(id)) { $(id).DataTable({ordering:true, paging:false, info:false}); } }
$('#pending-tab').on('shown.bs.tab', function(){ initDT('#pendingTable') });
$('#history-tab').on('shown.bs.tab', function(){ initDT('#historyTable') });
initDT('#pendingTable');
function showReject(id) {
    document.getElementById('rejectForm').action = '{{ url("admin/absensi/reject") }}/' + id;
    $('#rejectModal').modal('show');
}
</script>
@endpush
