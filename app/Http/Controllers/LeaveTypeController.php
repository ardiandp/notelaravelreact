<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use App\Models\ApprovalChain;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::with('approvalChain')->get();
        return view('admin.leave-types.index', compact('leaveTypes'));
    }

    public function create()
    {
        $chains = ApprovalChain::all();
        return view('admin.leave-types.create', compact('chains'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:leave_types,slug',
            'deskripsi' => 'nullable|string',
            'kuota_per_tahun' => 'nullable|integer|min:0',
            'is_paid' => 'boolean',
            'requires_attachment' => 'boolean',
            'is_active' => 'boolean',
            'max_days' => 'nullable|integer|min:0',
            'approval_chain_id' => 'nullable|exists:approval_chains,id',
        ]);

        $validated['is_paid'] = $request->boolean('is_paid');
        $validated['requires_attachment'] = $request->boolean('requires_attachment');
        $validated['is_active'] = $request->boolean('is_active');

        LeaveType::create($validated);

        return redirect()->route('admin.leave-types.index')->with('success', 'Jenis cuti created.');
    }

    public function edit(LeaveType $leaveType)
    {
        $chains = ApprovalChain::all();
        return view('admin.leave-types.edit', compact('leaveType', 'chains'));
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:leave_types,slug,' . $leaveType->id,
            'deskripsi' => 'nullable|string',
            'kuota_per_tahun' => 'nullable|integer|min:0',
            'is_paid' => 'boolean',
            'requires_attachment' => 'boolean',
            'is_active' => 'boolean',
            'max_days' => 'nullable|integer|min:0',
            'approval_chain_id' => 'nullable|exists:approval_chains,id',
        ]);

        $validated['is_paid'] = $request->boolean('is_paid');
        $validated['requires_attachment'] = $request->boolean('requires_attachment');
        $validated['is_active'] = $request->boolean('is_active');

        $leaveType->update($validated);

        return redirect()->route('admin.leave-types.index')->with('success', 'Jenis cuti updated.');
    }

    public function destroy(LeaveType $leaveType)
    {
        $leaveType->delete();
        return redirect()->route('admin.leave-types.index')->with('success', 'Jenis cuti deleted.');
    }
}
