@extends('adminlte::page')
@section('title', 'Edit Bagian')
@section('content_header')<h1>Edit Bagian</h1>@stop
@section('content')
    <div class="card"><div class="card-body">
        <form action="{{ route('admin.divisions.update', $division) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group"><label>Nama Bagian</label><input type="text" name="nama_bagian" class="form-control @error('nama_bagian') is-invalid @enderror" value="{{ old('nama_bagian', $division->nama_bagian) }}" required>@error('nama_bagian')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label>Parent</label><select name="parent_id" class="form-control"><option value="">Tidak ada parent</option>@foreach($parents as $p)<option value="{{ $p->id }}" {{ $division->parent_id == $p->id ? 'selected' : '' }}>{{ $p->nama_bagian }}</option>@endforeach</select></div>
            <div class="form-group"><div class="form-check"><input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ $division->is_active ? 'checked' : '' }}><label class="form-check-label" for="is_active">Aktif</label></div></div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.divisions') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div></div>
@stop
