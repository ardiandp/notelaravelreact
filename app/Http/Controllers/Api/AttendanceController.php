<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\UserSchedule;
use App\Models\Holiday;
use App\Models\Shift;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function today(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        if (! $attendance) {
            $schedule = UserSchedule::with('shift')
                ->where('user_id', $user->id)
                ->where('tanggal', $today)
                ->first();

            return response()->json([
                'checked_in' => false,
                'checked_out' => false,
                'schedule' => $schedule?->shift,
            ]);
        }

        return response()->json([
            'checked_in' => true,
            'checked_out' => $attendance->check_out !== null,
            'attendance' => $attendance,
        ]);
    }

    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'address' => 'nullable|string',
            'foto' => 'nullable|string',
            'work_from' => 'required|in:wfo,wfa',
        ]);

        $user = $request->user();
        $today = now()->toDateString();

        $exists = Attendance::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        if ($exists) {
            return response()->json(['message' => 'Already checked in today'], 422);
        }

        $schedule = UserSchedule::with('shift')
            ->where('user_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        $terlambat = 0;
        if ($schedule?->shift) {
            $shiftStart = \Carbon\Carbon::parse($schedule->shift->jam_masuk);
            $tolerance = $schedule->shift->toleransi_menit;
            $checkInTime = now();
            $terlambat = (int) $shiftStart->diffInMinutes($checkInTime, false);
            if ($terlambat < 0) $terlambat = 0;
        }

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'tanggal' => $today,
            'check_in' => now()->format('H:i:s'),
            'check_in_lat' => $validated['lat'],
            'check_in_lon' => $validated['lon'],
            'check_in_address' => $validated['address'] ?? null,
            'check_in_foto' => $validated['foto'] ?? null,
            'ip_address' => $request->ip(),
            'work_from' => $validated['work_from'],
            'terlambat_menit' => $terlambat,
            'status' => $terlambat > 0 ? 'terlambat' : 'hadir',
        ]);

        return response()->json($attendance, 201);
    }

    public function checkOut(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'address' => 'nullable|string',
            'foto' => 'nullable|string',
        ]);

        $user = $request->user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        if (! $attendance) {
            return response()->json(['message' => 'Not checked in yet'], 422);
        }

        if ($attendance->check_out) {
            return response()->json(['message' => 'Already checked out'], 422);
        }

        $attendance->update([
            'check_out' => now()->format('H:i:s'),
            'check_out_lat' => $validated['lat'],
            'check_out_lon' => $validated['lon'],
            'check_out_address' => $validated['address'] ?? null,
            'check_out_foto' => $validated['foto'] ?? null,
        ]);

        return response()->json($attendance);
    }

    public function history(Request $request)
    {
        $validated = $request->validate([
            'bulan' => 'nullable|integer|min:1|max:12',
            'tahun' => 'nullable|integer|min:2020',
        ]);

        $user = $request->user();
        $bulan = $validated['bulan'] ?? now()->month;
        $tahun = $validated['tahun'] ?? now()->year;

        $attendances = Attendance::where('user_id', $user->id)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json($attendances);
    }

    public function correction(Request $request)
    {
        $validated = $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
            'alasan' => 'required|string',
            'foto' => 'nullable|string',
        ]);

        $attendance = Attendance::findOrFail($validated['attendance_id']);
        if ($attendance->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $correction = AttendanceCorrection::create([
            'attendance_id' => $attendance->id,
            'user_id' => $request->user()->id,
            'alasan' => $validated['alasan'],
            'foto' => $validated['foto'] ?? null,
        ]);

        return response()->json($correction, 201);
    }
}
