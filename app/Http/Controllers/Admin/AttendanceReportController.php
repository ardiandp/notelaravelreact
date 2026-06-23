<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\UserSchedule;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\RequestApproval;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    public function daily(Request $request)
    {
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $users = User::whereHas('employeeDetail')->with('employeeDetail.division')->get();
        $attendances = Attendance::where('tanggal', $tanggal)->get()->keyBy('user_id');
        $schedules = UserSchedule::where('tanggal', $tanggal)->with('shift')->get()->keyBy('user_id');
        $leaves = LeaveRequest::where('status', 'approved')
            ->where('tanggal_mulai', '<=', $tanggal)
            ->where('tanggal_selesai', '>=', $tanggal)
            ->with('leaveType', 'user')
            ->get()
            ->keyBy('user_id');

        $stats = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'cuti' => 0, 'alpha' => 0, 'total' => count($users)];

        $rows = [];
        foreach ($users as $user) {
            $att = $attendances->get($user->id);
            $sch = $schedules->get($user->id);
            $leave = $leaves->get($user->id);

            if ($att) {
                $status = $att->status;
            } elseif ($leave) {
                $status = $leave->leaveType->nama === 'Sakit' ? 'sakit' : ($leave->leaveType->nama === 'Izin' ? 'izin' : 'cuti');
            } else {
                $status = $sch ? 'alpha' : 'alpha';
            }

            $stats[$status]++;
            $rows[] = compact('user', 'att', 'sch', 'leave', 'status');
        }

        return view('admin.absensi.daily', compact('rows', 'stats', 'tanggal'));
    }

    public function onLeave(Request $request)
    {
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));

        $leaveRequests = LeaveRequest::where('status', 'approved')
            ->where('tanggal_mulai', '<=', $tanggal)
            ->where('tanggal_selesai', '>=', $tanggal)
            ->with('user.employeeDetail.division', 'leaveType')
            ->orderBy('tanggal_mulai')
            ->get();

        $activeLeaves = LeaveRequest::whereIn('status', ['pending', 'approved'])
            ->where('tanggal_selesai', '>=', $tanggal)
            ->with('user.employeeDetail.division', 'leaveType')
            ->orderBy('tanggal_mulai')
            ->get();

        return view('admin.absensi.on-leave', compact('leaveRequests', 'activeLeaves', 'tanggal'));
    }

    public function pendingApprovals()
    {
        $pending = LeaveRequest::where('status', 'pending')
            ->with('user.employeeDetail.division', 'leaveType', 'approvalChain', 'approvals.approver')
            ->orderBy('created_at', 'desc')
            ->get();

        $histories = LeaveRequest::whereIn('status', ['approved', 'rejected', 'cancelled'])
            ->with('user.employeeDetail.division', 'leaveType', 'approvals.approver')
            ->orderBy('updated_at', 'desc')
            ->limit(50)
            ->get();

        return view('admin.absensi.pending-approvals', compact('pending', 'histories'));
    }

    public function detail(Request $request, User $user)
    {
        $dari = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('tanggal', [$dari, $sampai])
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalHadir = $attendances->whereIn('status', ['hadir', 'terlambat'])->count();
        $totalTerlambat = $attendances->where('status', 'terlambat')->count();
        $totalAlpha = $attendances->where('status', 'alpha')->count();
        $totalIzin = $attendances->where('status', 'izin')->count();
        $totalSakit = $attendances->where('status', 'sakit')->count();
        $totalCuti = $attendances->where('status', 'cuti')->count();

        $leaves = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('tanggal_mulai', '<=', $sampai)
            ->where('tanggal_selesai', '>=', $dari)
            ->with('leaveType')
            ->get();

        return view('admin.absensi.detail', compact(
            'user', 'attendances', 'dari', 'sampai',
            'totalHadir', 'totalTerlambat', 'totalAlpha', 'totalIzin', 'totalSakit', 'totalCuti', 'leaves'
        ));
    }

    public function approveLeave(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== 'pending') {
            return back()->withErrors(['msg' => 'Permintaan sudah diproses']);
        }

        $leaveRequest->update(['status' => 'approved']);

        $pendingApproval = $leaveRequest->approvals()->where('status', 'pending')->first();
        if ($pendingApproval) {
            $pendingApproval->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approver_id' => request()->user()->id,
                'catatan' => 'Disetujui oleh Admin',
            ]);
        }

        return back()->with('success', 'Cuti/izin disetujui.');
    }

    public function rejectLeave(Request $request, LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== 'pending') {
            return back()->withErrors(['msg' => 'Permintaan sudah diproses']);
        }

        $request->validate(['alasan' => 'nullable|string']);

        $leaveRequest->update(['status' => 'rejected']);

        $pendingApproval = $leaveRequest->approvals()->where('status', 'pending')->first();
        if ($pendingApproval) {
            $pendingApproval->update([
                'status' => 'rejected',
                'approved_at' => now(),
                'approver_id' => $request->user()->id,
                'catatan' => $request->alasan ?? 'Ditolak oleh Admin',
            ]);
        }

        return back()->with('success', 'Cuti/izin ditolak.');
    }
}
