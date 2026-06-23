<?php

namespace App\Http\Controllers;

use App\Models\ApprovalChain;
use App\Models\ApprovalChainStep;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class ApprovalChainController extends Controller
{
    public function index()
    {
        $chains = ApprovalChain::with('steps.role')->get();
        return view('admin.approval-chains.index', compact('chains'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.approval-chains.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:approval_chains,slug',
            'steps' => 'nullable|array',
            'steps.*.step_order' => 'required|integer|min:1',
            'steps.*.approver_type' => 'required|in:supervisor,role',
            'steps.*.role_id' => 'nullable|exists:roles,id',
        ]);

        $chain = ApprovalChain::create(['nama' => $validated['nama'], 'slug' => $validated['slug']]);

        if (!empty($validated['steps'])) {
            foreach ($validated['steps'] as $step) {
                ApprovalChainStep::create([
                    'approval_chain_id' => $chain->id,
                    'step_order' => $step['step_order'],
                    'approver_type' => $step['approver_type'],
                    'role_id' => $step['approver_type'] === 'role' ? $step['role_id'] : null,
                ]);
            }
        }

        return redirect()->route('admin.approval-chains.index')->with('success', 'Approval chain created.');
    }

    public function edit(ApprovalChain $approvalChain)
    {
        $roles = Role::all();
        $chain = $approvalChain->load('steps');
        return view('admin.approval-chains.edit', compact('chain', 'roles'));
    }

    public function update(Request $request, ApprovalChain $approvalChain)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:approval_chains,slug,' . $approvalChain->id,
            'steps' => 'nullable|array',
            'steps.*.step_order' => 'required|integer|min:1',
            'steps.*.approver_type' => 'required|in:supervisor,role',
            'steps.*.role_id' => 'nullable|exists:roles,id',
        ]);

        $approvalChain->update(['nama' => $validated['nama'], 'slug' => $validated['slug']]);
        $approvalChain->steps()->delete();

        if (!empty($validated['steps'])) {
            foreach ($validated['steps'] as $step) {
                ApprovalChainStep::create([
                    'approval_chain_id' => $approvalChain->id,
                    'step_order' => $step['step_order'],
                    'approver_type' => $step['approver_type'],
                    'role_id' => $step['approver_type'] === 'role' ? $step['role_id'] : null,
                ]);
            }
        }

        return redirect()->route('admin.approval-chains.index')->with('success', 'Approval chain updated.');
    }

    public function destroy(ApprovalChain $approvalChain)
    {
        $approvalChain->delete();
        return redirect()->route('admin.approval-chains.index')->with('success', 'Approval chain deleted.');
    }
}
