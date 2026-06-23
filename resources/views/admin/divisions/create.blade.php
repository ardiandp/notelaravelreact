@extends('adminlte::page')
@section('title', 'Tambah Bagian')
@section('content_header')<h1>Tambah Bagian</h1>@stop
@section('content')
    <div class="card"><div class="card-body">
        <form action="{{ route('admin.divisions.store') }}" method="POST">
            @csrf
            <div class="form-group"><label>Nama Bagian</label><input type="text" name="nama_bagian" class="form-control @error('nama_bagian') is-invalid @enderror" value="{{ old('nama_bagian') }}" required>@error('nama_bagian')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label>Parent</label><select name="parent_id" class="form-control"><option value="">Tidak ada parent</option>@foreach($parents as $p)<option value="{{ $p->id }}">{{ $p->nama_bagian }}</option>@endforeach</select></div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.divisions') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div></div>
@stop
