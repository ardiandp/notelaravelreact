@extends('adminlte::page')
@section('title', 'Tambah Approval Chain')
@section('content_header')<h1>Tambah Approval Chain</h1>@stop
@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.approval-chains.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group"><label>Nama</label><input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>@error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                </div>
                <div class="col-md-6">
                    <div class="form-group"><label>Slug</label><input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" required>@error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                </div>
            </div>

            <hr>
            <h5>Approval Steps</h5>
            <div id="steps-wrapper">
                <div class="step-row row mb-2 align-items-end">
                    <div class="col-md-2"><label>Urutan</label><input type="number" name="steps[0][step_order]" class="form-control" value="1" min="1"></div>
                    <div class="col-md-3"><label>Tipe Approver</label><select name="steps[0][approver_type]" class="form-control approver-type" onchange="toggleRole(this, 0)"><option value="supervisor">Supervisor</option><option value="role">Role</option></select></div>
                    <div class="col-md-4 role-select-wrapper d-none"><label>Role</label><select name="steps[0][role_id]" class="form-control"><option value="">-- Pilih Role --</option>@foreach($roles as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach</select></div>
                    <div class="col-md-2"><button type="button" class="btn btn-danger btn-sm remove-step" onclick="this.closest('.step-row').remove()">Hapus</button></div>
                </div>
            </div>
            <button type="button" class="btn btn-success btn-sm mb-3" id="add-step">+ Tambah Step</button>

            <div class="form-group"><button type="submit" class="btn btn-primary">Simpan</button><a href="{{ route('admin.approval-chains.index') }}" class="btn btn-secondary ml-2">Batal</a></div>
        </form>
    </div>
</div>
@stop
@push('js')
<script>
let stepIndex = 1;
document.getElementById('add-step').addEventListener('click', function() {
    const wrapper = document.getElementById('steps-wrapper');
    const row = document.createElement('div');
    row.className = 'step-row row mb-2 align-items-end';
    row.innerHTML = `
        <div class="col-md-2"><label>Urutan</label><input type="number" name="steps[${stepIndex}][step_order]" class="form-control" value="${stepIndex + 1}" min="1"></div>
        <div class="col-md-3"><label>Tipe Approver</label><select name="steps[${stepIndex}][approver_type]" class="form-control approver-type" onchange="toggleRole(this, ${stepIndex})"><option value="supervisor">Supervisor</option><option value="role">Role</option></select></div>
        <div class="col-md-4 role-select-wrapper d-none"><label>Role</label><select name="steps[${stepIndex}][role_id]" class="form-control"><option value="">-- Pilih Role --</option>@foreach($roles as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach</select></div>
        <div class="col-md-2"><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.step-row').remove()">Hapus</button></div>
    `;
    wrapper.appendChild(row);
    stepIndex++;
});
function toggleRole(sel, idx) {
    const wrapper = sel.closest('.step-row').querySelector('.role-select-wrapper');
    wrapper.classList.toggle('d-none', sel.value !== 'role');
    if (sel.value !== 'role') wrapper.querySelector('select').value = '';
}
document.querySelector('input[name="nama"]').addEventListener('input', function() {
    const slug = document.getElementById('slug');
    if (!slug.dataset.manual) slug.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
});
document.getElementById('slug').addEventListener('input', function() { this.dataset.manual = '1'; });
</script>
@endpush
