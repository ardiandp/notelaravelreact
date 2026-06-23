@extends('adminlte::page')
@section('title', 'Kalender Kerja')
@section('content_header')<h1>Kalender Kerja</h1>@stop
@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
.schedule-table { font-size: .8rem; }
.schedule-table th, .schedule-table td { text-align: center; vertical-align: middle; padding: .3rem .2rem; min-width: 2.2rem; }
.schedule-table .user-name { text-align: left; min-width: 160px; white-space: nowrap; }
.schedule-table .weekend, .schedule-table .holiday { background: #f8f9fa !important; color: #adb5bd; }
.schedule-cell { cursor: pointer; border-radius: 3px; padding: 2px 0; }
.schedule-cell .badge { font-size: .65rem; }
.edit-schedule-form { display: inline; }
.edit-schedule-form select { font-size: .7rem; padding: 0 .2rem; height: auto; width: 60px; }
.weekday-header { font-weight: 600; }
.weekday-header.sat { color: #17a2b8; }
.weekday-header.sun { color: #dc3545; }
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
            <div class="form-group mr-2"><label class="mr-1">User</label>
                <select name="user_id" class="form-control form-control-sm">
                    <option value="">-- Semua --</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ $selectedUser && $selectedUser->id == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-sm btn-info mr-2"><i class="fas fa-search"></i> Tampilkan</button>
        </form>
        <div class="float-right">
            <form method="POST" action="{{ route('admin.schedules.generate') }}" class="d-inline" onsubmit="return confirm('Generate jadwal untuk bulan ini? (tidak akan timpa yg sudah ada)')">
                @csrf
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-sync"></i> Generate</button>
            </form>
            <form method="POST" action="{{ route('admin.schedules.clear') }}" class="d-inline" onsubmit="return confirm('Hapus semua jadwal bulan ini?')">
                @csrf
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Clear</button>
            </form>
        </div>
    </div>
    <div class="card-body p-0" style="overflow-x:auto">
        <table class="table table-bordered schedule-table mb-0">
            <thead>
                <tr>
                    <th class="user-name">Nama</th>
                    @foreach($dates as $d)
                    <th class="weekday-header {{ $d->format('D') === 'Sat' ? 'sat' : ($d->format('D') === 'Sun' ? 'sun' : '') }}">
                        {{ $d->format('j') }}<br><small>{{ mb_substr($d->locale('id')->dayName, 0, 2) }}</small>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="user-name"><a href="{{ route('admin.schedules.user', $user) }}">{{ $user->name }}</a> <small class="text-muted">({{ $user->employeeDetail?->division?->nama_bagian ?? '-' }})</small></td>
                    @foreach($dates as $d)
                        @php
                            $dateStr = $d->toDateString();
                            $schedule = $scheduleGrid[$user->id][$dateStr] ?? null;
                            $isWeekend = $d->isWeekend();
                            $isHoliday = in_array($dateStr, $holidayDates);
                        @endphp
                        <td class="{{ $isWeekend ? 'weekend' : ($isHoliday ? 'holiday' : '') }}">
                            @if($isWeekend)
                                <small class="text-muted">—</small>
                            @elseif($isHoliday)
                                <small class="text-muted">✕</small>
                            @elseif($schedule)
                                <form action="{{ route('admin.schedules.update', $schedule) }}" method="POST" class="edit-schedule-form" onchange="this.submit()">
                                    @csrf @method('PUT')
                                    <select name="shift_id" class="form-control d-block mx-auto mb-1">
                                        @foreach($shifts as $s)
                                        <option value="{{ $s->id }}" {{ $schedule->shift_id == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                                        @endforeach
                                        <option value="">— Hapus —</option>
                                    </select>
                                    <select name="work_from" class="form-control d-block mx-auto">
                                        <option value="wfo" {{ ($schedule->work_from ?? 'wfo') === 'wfo' ? 'selected' : '' }}>WFO</option>
                                        <option value="wfa" {{ ($schedule->work_from ?? 'wfo') === 'wfa' ? 'selected' : '' }}>WFA</option>
                                    </select>
                                </form>
                            @else
                                <small class="text-muted">—</small>
                            @endif
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop
