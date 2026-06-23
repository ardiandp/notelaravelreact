@extends('adminlte::page')
@section('title', 'Hari Libur')
@section('content_header')<h1>Hari Libur</h1>@stop
@section('content')
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card">
        <div class="card-header"><h3 class="card-title">Daftar Libur</h3><button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#createModal">+ Tambah Libur</button></div>
        <div class="card-body p-0">
            <table class="table table-hover">
                <thead><tr><th>Tanggal</th><th>Keterangan</th><th>Berulang</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach($holidays as $h)
                    <tr>
                        <td>{{ $h->tanggal->format('d M Y') }}</td><td>{{ $h->keterangan }}</td>
                        <td>@if($h->is_recurring)<span class="badge badge-info">Ya</span>@else<span class="badge badge-secondary">Tidak</span>@endif</td>
                        <td>
                            <form action="{{ route('admin.holidays.delete', $h) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus libur ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="createModal"><div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h4>Tambah Hari Libur</h4><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <form action="{{ route('admin.holidays.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group"><label>Tanggal</label><input type="date" name="tanggal" class="form-control" required></div>
                <div class="form-group"><label>Keterangan</label><input type="text" name="keterangan" class="form-control" required></div>
                <div class="form-check"><input type="checkbox" name="is_recurring" class="form-check-input" id="is_recurring" value="1"><label class="form-check-label" for="is_recurring">Libur tahunan (berulang setiap tahun)</label></div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Simpan</button></div>
        </form>
    </div></div></div>
@stop
