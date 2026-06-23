@extends('adminlte::page')
@section('title', 'Atur Jadwal - '.$user->name)
@section('content_header')
<h1>Atur Jadwal: {{ $user->name }}
    <small class="text-muted">{{ $user->employeeDetail?->division?->nama_bagian ?? '-' }} &middot; {{ $user->email }}</small>
</h1>
@stop
@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
.schedule-table { font-size: .8rem; }
.schedule-table th, .schedule-table td { text-align: center; vertical-align: middle; padding: .3rem .2rem; min-width: 2.2rem; }
.schedule-table td { cursor: pointer; }
.schedule-table td:hover { background: #e9ecef; }
.schedule-table .weekend, .schedule-table .holiday { cursor: default; }
.schedule-table .weekend:hover, .schedule-table .holiday:hover { background: #f8f9fa; }
.schedule-cell { border-radius: 3px; padding: 2px 0; display: block; }
.weekday-header { font-weight: 600; }
.weekday-header.sat { color: #17a2b8; }
.weekday-header.sun { color: #dc3545; }
.selected-shift { background: #cce5ff; border-radius: 3px; }
</style>
@endpush
@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<div class="card">
    <div class="card-header">
        <form method="GET" class="form-inline">
            <div class="form-group mr-2"><label class="mr-1">Bulan</label>
                <select name="bulan" class="form-control form-control-sm">
                    @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>{{ Carbon\Carbon::create()->month($m)->locale('id')->monthName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mr-2"><label class="mr-1">Tahun</label>
                <select name="tahun" class="form-control form-control-sm">
                    @foreach(range(now()->year - 1, now()->year + 1) as $y)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-sm btn-info mr-2"><i class="fas fa-search"></i> Tampilkan</button>
            <a href="{{ route('admin.schedules') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </form>
    </div>
    <div class="card-body p-0" style="overflow-x:auto">
        <table class="table table-bordered schedule-table mb-0">
            <thead>
                <tr>
                    <th style="min-width:100px;text-align:left">Tanggal</th>
                    @foreach($dates as $d)
                    <th class="weekday-header {{ $d->format('D') === 'Sat' ? 'sat' : ($d->format('D') === 'Sun' ? 'sun' : '') }}">
                        {{ $d->format('j') }}<br><small>{{ mb_substr($d->locale('id')->dayName, 0, 2) }}</small>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align:left;font-weight:600">
                        {{ $user->name }}
                        <br><small class="text-muted">{{ $user->employeeDetail?->division?->nama_bagian ?? '-' }}</small>
                    </td>
                    @foreach($dates as $d)
                        @php
                            $dateStr = $d->toDateString();
                            $schedule = $schedules[$dateStr] ?? null;
                            $isWeekend = $d->isWeekend();
                            $isHoliday = in_array($dateStr, $holidayDates);
                        @endphp
                        <td class="{{ $isWeekend ? 'weekend' : ($isHoliday ? 'holiday' : '') }}"
                            data-date="{{ $dateStr }}"
                            data-shift-id="{{ $schedule?->shift_id ?? '' }}"
                            data-shift-name="{{ $schedule?->shift?->nama ?? '' }}"
                            @if(!$isWeekend && !$isHoliday) onclick="openShiftModal('{{ $dateStr }}')" @endif>
                            @if($isWeekend)
                                <small class="text-muted">—</small>
                            @elseif($isHoliday)
                                <small class="text-muted">✕</small>
                            @elseif($schedule)
                                <span class="badge badge-info">{{ $schedule->shift->nama }}</span>
                            @else
                                <small class="text-muted">+</small>
                            @endif
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <form method="POST" action="{{ route('admin.schedules.generate') }}" class="d-inline" onsubmit="return confirm('Generate jadwal untuk bulan ini? (tidak akan timpa yg sudah ada)')">
            @csrf
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-sync"></i> Generate (per user)</button>
        </form>
        <form method="POST" action="{{ route('admin.schedules.clear') }}" class="d-inline" onsubmit="return confirm('Hapus semua jadwal bulan ini untuk user ini?')">
            @csrf
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Clear (per user)</button>
        </form>
    </div>
</div>

<div class="modal fade" id="shiftModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Atur Jadwal</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="shiftForm">
                <div class="modal-body">
                    <p id="shiftDate" class="font-weight-bold"></p>
                    <div id="shiftOptions">
                        @foreach($shifts as $s)
                        <div class="form-check mb-2">
                            <input class="form-check-input shift-radio" type="radio" name="shift_id" id="shift_{{ $s->id }}" value="{{ $s->id }}">
                            <label class="form-check-label" for="shift_{{ $s->id }}">
                                <strong>{{ $s->nama }}</strong><br>
                                <small class="text-muted">{{ $s->jam_masuk ? substr($s->jam_masuk, 0, 5) : '--:--' }} - {{ $s->jam_pulang ? substr($s->jam_pulang, 0, 5) : '--:--' }}</small>
                            </label>
                        </div>
                        @endforeach
                        <hr>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="shift_id" id="shift_none" value="">
                            <label class="form-check-label text-danger" for="shift_none">Hapus jadwal</label>
                        </div>
                    </div>
                    <input type="hidden" name="tanggal" id="shiftTanggal">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveShift"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
@push('js')
<script>
function openShiftModal(dateStr) {
    const cell = document.querySelector(`td[data-date="${dateStr}"]`);
    const shiftId = cell?.dataset.shiftId || '';
    const shiftName = cell?.dataset.shiftName || '';
    document.getElementById('shiftDate').textContent = dateStr;
    document.getElementById('shiftTanggal').value = dateStr;
    document.querySelectorAll('.shift-radio').forEach(r => r.checked = false);
    document.getElementById('shift_none').checked = false;
    if (shiftId) {
        const radio = document.getElementById('shift_' + shiftId);
        if (radio) radio.checked = true;
    }
    $('#shiftModal').modal('show');
}

document.getElementById('shiftForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const tanggal = document.getElementById('shiftTanggal').value;
    const shiftRadio = document.querySelector('input[name="shift_id"]:checked');
    const shiftId = shiftRadio ? shiftRadio.value : '';
    const btn = document.getElementById('btnSaveShift');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    fetch('{{ route("admin.schedules.user.update", $user) }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ tanggal, shift_id: shiftId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const cell = document.querySelector(`td[data-date="${tanggal}"]`);
            if (cell) {
                if (data.shift_id) {
                    cell.innerHTML = `<span class="badge badge-info">${data.shift_name}</span>`;
                    cell.dataset.shiftId = data.shift_id;
                    cell.dataset.shiftName = data.shift_name;
                } else {
                    cell.innerHTML = '<small class="text-muted">+</small>';
                    cell.dataset.shiftId = '';
                    cell.dataset.shiftName = '';
                }
            }
            $('#shiftModal').modal('hide');
        } else {
            alert('Gagal menyimpan jadwal');
        }
    })
    .catch(() => alert('Terjadi kesalahan'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
    });
});
</script>
@endpush
