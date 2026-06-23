@extends('adminlte::page')
@section('title', 'Tambah Jenis Cuti')
@section('content_header')<h1>Tambah Jenis Cuti</h1>@stop
@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.leave-types.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group"><label>Nama</label><input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>@error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                </div>
                <div class="col-md-6">
                    <div class="form-group"><label>Slug</label><input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" required>@error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                </div>
            </div>
            <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi') }}</textarea></div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group"><label>Kuota per Tahun</label><input type="number" name="kuota_per_tahun" class="form-control" value="{{ old('kuota_per_tahun', 0) }}" min="0"></div>
                </div>
                <div class="col-md-4">
                    <div class="form-group"><label>Max Hari (opsional)</label><input type="number" name="max_days" class="form-control" value="{{ old('max_days') }}" min="0"></div>
                </div>
                <div class="col-md-4">
                    <div class="form-group"><label>Approval Chain</label>
                        <select name="approval_chain_id" class="form-control">
                            <option value="">-- Tanpa Approval --</option>
                            @foreach($chains as $c)
                            <option value="{{ $c->id }}" {{ old('approval_chain_id') == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-check"><input type="checkbox" name="is_paid" class="form-check-input" id="is_paid" value="1" checked><label class="form-check-label" for="is_paid">Dibayar</label></div>
                </div>
                <div class="col-md-4">
                    <div class="form-check"><input type="checkbox" name="requires_attachment" class="form-check-input" id="requires_attachment" value="1"><label class="form-check-label" for="requires_attachment">Requires Attachment</label></div>
                </div>
                <div class="col-md-4">
                    <div class="form-check"><input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" checked><label class="form-check-label" for="is_active">Aktif</label></div>
                </div>
            </div>
            <div class="form-group mt-3"><button type="submit" class="btn btn-primary">Simpan</button><a href="{{ route('admin.leave-types.index') }}" class="btn btn-secondary ml-2">Batal</a></div>
        </form>
    </div>
</div>
@stop
@push('js')
<script>
document.getElementById('nama')?.addEventListener('input', function() {
    const slug = document.getElementById('slug');
    if (!slug.dataset.manual) slug.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
});
document.getElementById('slug')?.addEventListener('input', function() { this.dataset.manual = '1'; });
</script>
@endpush
