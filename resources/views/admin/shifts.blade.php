@extends('adminlte::page')
@section('title', 'Shift')
@section('content_header')<h1>Shift Kerja</h1>@stop
@section('content')
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card">
        <div class="card-header"><h3 class="card-title">Daftar Shift</h3><button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#createModal">+ Tambah Shift</button></div>
        <div class="card-body p-0">
            <table class="table table-hover">
                <thead><tr><th>Nama</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Toleransi</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach($shifts as $s)
                    <tr>
                        <td>{{ $s->nama }}</td>
                        <td>{{ substr($s->jam_masuk, 0, 5) }}</td>
                        <td>{{ substr($s->jam_pulang, 0, 5) }}</td>
                        <td>{{ $s->toleransi_menit }} menit</td>
                        <td><span class="badge {{ $s->is_active ? 'badge-success' : 'badge-secondary' }}">{{ $s->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td>
                            <form action="{{ route('admin.shifts.delete', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus shift ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="createModal"><div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h4>Tambah Shift</h4><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <form action="{{ route('admin.shifts.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group"><label>Nama</label><input type="text" name="nama" class="form-control" required></div>
                <div class="form-group"><label>Jam Masuk</label><input type="time" name="jam_masuk" class="form-control" required></div>
                <div class="form-group"><label>Jam Pulang</label><input type="time" name="jam_pulang" class="form-control" required></div>
                <div class="form-group"><label>Toleransi (menit)</label><input type="number" name="toleransi_menit" class="form-control" value="0"></div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Simpan</button></div>
        </form>
    </div></div></div>
@stop
