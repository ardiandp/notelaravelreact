@extends('adminlte::page')
@section('title', 'Lokasi Absensi')
@section('content_header')<h1>Lokasi Absensi</h1>@stop
@section('content')
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card">
        <div class="card-header"><h3 class="card-title">Daftar Lokasi</h3><button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#createModal">+ Tambah Lokasi</button></div>
        <div class="card-body p-0">
            <table class="table table-hover">
                <thead><tr><th>Nama</th><th>Alamat</th><th>Lat</th><th>Lon</th><th>Radius</th><th>Tipe</th><th>Aktif</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach($locations as $l)
                    <tr>
                        <td>{{ $l->nama }}</td><td>{{ $l->alamat }}</td><td>{{ $l->lat }}</td><td>{{ $l->lon }}</td>
                        <td>{{ $l->radius }}m</td>
                        <td><span class="badge {{ $l->is_office ? 'badge-info' : 'badge-warning' }}">{{ $l->is_office ? 'WFO' : 'WFA' }}</span></td>
                        <td><span class="badge {{ $l->is_active ? 'badge-success' : 'badge-secondary' }}">{{ $l->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td>
                            <form action="{{ route('admin.locations.delete', $l) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus lokasi ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="createModal"><div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h4>Tambah Lokasi</h4><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <form action="{{ route('admin.locations.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group"><label>Nama</label><input type="text" name="nama" class="form-control" required></div>
                <div class="form-group"><label>Alamat</label><textarea name="alamat" class="form-control" required></textarea></div>
                <div class="row"><div class="col"><div class="form-group"><label>Latitude</label><input type="text" name="lat" class="form-control" required></div></div><div class="col"><div class="form-group"><label>Longitude</label><input type="text" name="lon" class="form-control" required></div></div></div>
                <div class="form-group"><label>Radius (meter)</label><input type="number" name="radius" class="form-control" value="100"></div>
                <div class="form-check"><input type="checkbox" name="is_office" class="form-check-input" id="is_office" value="1" checked><label class="form-check-label" for="is_office">Kantor (WFO)</label></div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Simpan</button></div>
        </form>
    </div></div></div>
@stop
