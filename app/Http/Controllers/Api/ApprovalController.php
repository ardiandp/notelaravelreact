<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RequestApproval;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function pending(Request $request)
    {
        $approvals = RequestApproval::with([
                'requestable.leaveType',
                'requestable.user',
                'approvalChainStep'
            ])
            ->where('approver_id', $request->user()->id)
            ->where('status', 'pending')
            ->get();

        return response()->json($approvals);
    }

    public function approve(Request $request, RequestApproval $approval)
    {
        if ($approval->approver_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($approval->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 422);
        }

        $validated = $request->validate(['catatan' => 'nullable|string']);

        $approval->update([
            'status' => 'approved',
            'catatan' => $validated['catatan'] ?? null,
            'action_at' => now(),
        ]);

        $requestable = $approval->requestable;

        $nextStep = \App\Models\RequestApproval::where('requestable_type', $approval->requestable_type)
            ->where('requestable_id', $approval->requestable_id)
            ->where('status', 'pending')
            ->orderBy('step_order')
            ->first();

        if (! $nextStep && $requestable instanceof LeaveRequest) {
            $requestable->update(['status' => 'approved']);

            LeaveBalance::updateOrCreate(
                [
                    'user_id' => $requestable->user_id,
                    'leave_type_id' => $requestable->leave_type_id,
                    'year' => $requestable->created_at->year,
                ],
                [
                    'kuota' => $requestable->leaveType->kuota_per_tahun ?? 0,
                ]
            );

            $balance = LeaveBalance::where('user_id', $requestable->user_id)
                ->where('leave_type_id', $requestable->leave_type_id)
                ->where('year', $requestable->created_at->year)
                ->first();

            if ($balance) {
                $balance->increment('terpakai', $requestable->jumlah_hari);
            }
        }

        return response()->json(['message' => 'Approved']);
    }

    public function reject(Request $request, RequestApproval $approval)
    {
        if ($approval->approver_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($approval->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 422);
        }

        $validated = $request->validate(['catatan' => 'nullable|string']);

        $approval->update([
            'status' => 'rejected',
            'catatan' => $validated['catatan'] ?? null,
            'action_at' => now(),
        ]);

        $requestable = $approval->requestable;
        if ($requestable) {
            $requestable->update(['status' => 'rejected']);
        }

        return response()->json(['message' => 'Rejected']);
    }
}
