@extends('adminlte::page')
@section('title', 'Edit Approval Chain')
@section('content_header')<h1>Edit Approval Chain</h1>@stop
@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.approval-chains.update', $chain) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group"><label>Nama</label><input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $chain->nama) }}" required>@error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                </div>
                <div class="col-md-6">
                    <div class="form-group"><label>Slug</label><input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $chain->slug) }}" required>@error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                </div>
            </div>

            <hr>
            <h5>Approval Steps</h5>
            <div id="steps-wrapper">
                @foreach($chain->steps as $i => $step)
                <div class="step-row row mb-2 align-items-end">
                    <div class="col-md-2"><label>Urutan</label><input type="number" name="steps[{{ $i }}][step_order]" class="form-control" value="{{ $step->step_order }}" min="1"></div>
                    <div class="col-md-3"><label>Tipe Approver</label><select name="steps[{{ $i }}][approver_type]" class="form-control approver-type" onchange="toggleRole(this, {{ $i }})"><option value="supervisor" {{ $step->approver_type === 'supervisor' ? 'selected' : '' }}>Supervisor</option><option value="role" {{ $step->approver_type === 'role' ? 'selected' : '' }}>Role</option></select></div>
                    <div class="col-md-4 role-select-wrapper {{ $step->approver_type !== 'role' ? 'd-none' : '' }}"><label>Role</label><select name="steps[{{ $i }}][role_id]" class="form-control"><option value="">-- Pilih Role --</option>@foreach($roles as $r)<option value="{{ $r->id }}" {{ $step->role_id == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>@endforeach</select></div>
                    <div class="col-md-2"><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.step-row').remove()">Hapus</button></div>
                </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-success btn-sm mb-3" id="add-step">+ Tambah Step</button>

            <div class="form-group"><button type="submit" class="btn btn-primary">Update</button><a href="{{ route('admin.approval-chains.index') }}" class="btn btn-secondary ml-2">Batal</a></div>
        </form>
    </div>
</div>
@stop
@push('js')
<script>
let stepIndex = {{ count($chain->steps) }};
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
function toggleRole(sel) {
    const wrapper = sel.closest('.step-row').querySelector('.role-select-wrapper');
    wrapper.classList.toggle('d-none', sel.value !== 'role');
    if (sel.value !== 'role') wrapper.querySelector('select').value = '';
}
</script>
@endpush
