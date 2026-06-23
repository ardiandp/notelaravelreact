<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\RequestApproval;
use App\Models\ApprovalChain;
use App\Models\ApprovalChainStep;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $requests = LeaveRequest::with(['leaveType', 'approvals.approver'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($requests);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string',
            'attachment' => 'nullable|string',
        ]);

        $leaveType = \App\Models\LeaveType::findOrFail($validated['leave_type_id']);
        $user = $request->user();

        $from = \Carbon\Carbon::parse($validated['tanggal_mulai']);
        $to = \Carbon\Carbon::parse($validated['tanggal_selesai']);
        $totalDays = $from->diffInDays($to) + 1;

        $balance = LeaveBalance::where('user_id', $user->id)
            ->where('leave_type_id', $leaveType->id)
            ->where('year', now()->year)
            ->first();

        if ($balance && ($balance->terpakai + $totalDays) > $balance->kuota) {
            return response()->json(['message' => 'Sisa cuti tidak mencukupi'], 422);
        }

        $chainId = $leaveType->approval_chain_id;
        $leaveRequest = LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'approval_chain_id' => $chainId,
            'tanggal_pengajuan' => now()->toDateString(),
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'jumlah_hari' => $totalDays,
            'keterangan' => $validated['keterangan'],
            'attachment' => $validated['attachment'] ?? null,
        ]);

        if ($chainId) {
            $steps = ApprovalChainStep::where('approval_chain_id', $chainId)
                ->orderBy('step_order')
                ->get();

            foreach ($steps as $step) {
                $approverId = null;
                if ($step->approver_type === 'supervisor') {
                    $employee = $user->employeeDetail;
                    $approverId = $employee?->supervisor_id;
                } elseif ($step->approver_type === 'role' && $step->role_id) {
                    $approver = \App\Models\User::role($step->role->name)->first();
                    $approverId = $approver?->id;
                }

                if ($approverId) {
                    RequestApproval::create([
                        'requestable_type' => LeaveRequest::class,
                        'requestable_id' => $leaveRequest->id,
                        'approval_chain_step_id' => $step->id,
                        'step_order' => $step->step_order,
                        'approver_id' => $approverId,
                    ]);
                }
            }
        }

        return response()->json($leaveRequest->load(['leaveType', 'approvals.approver']), 201);
    }

    public function cancel(Request $request, LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($leaveRequest->status !== 'pending') {
            return response()->json(['message' => 'Cannot cancel non-pending request'], 422);
        }

        $leaveRequest->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Cancelled']);
    }
}
