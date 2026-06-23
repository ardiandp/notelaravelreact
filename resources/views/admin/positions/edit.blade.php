@extends('adminlte::page')
@section('title', 'Edit Jabatan')
@section('content_header')<h1>Edit Jabatan</h1>@stop
@section('content')
    <div class="card"><div class="card-body">
        <form action="{{ route('admin.positions.update', $position) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group"><label>Nama Jabatan</label><input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $position->nama) }}" required>@error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label>Divisi</label><select name="division_id" class="form-control"><option value="">Pilih Divisi</option>@foreach($divisions as $d)<option value="{{ $d->id }}" {{ $position->division_id == $d->id ? 'selected' : '' }}>{{ $d->nama_bagian }}</option>@endforeach</select></div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.positions') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div></div>
@stop
