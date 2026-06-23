<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\UserSchedule;
use App\Models\Holiday;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    private function saveFoto(string $base64, int $userId, string $tanggal, string $type): string
    {
        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $imageData = base64_decode($base64);
        if ($imageData === false) {
            throw new \InvalidArgumentException('Invalid base64 image data');
        }
        $filename = "{$userId}_{$tanggal}_{$type}.jpg";
        $path = "attendance/{$filename}";
        Storage::disk('public')->put($path, $imageData);
        return $path;
    }

    private function findSchedule(int $userId, string $today): ?UserSchedule
    {
        $schedule = UserSchedule::with('shift')
            ->where('user_id', $userId)->where('tanggal', $today)
            ->first();

        if (!$schedule) {
            $schedule = UserSchedule::with('shift')
                ->where('user_id', $userId)
                ->where('tanggal', now()->subDay()->format('Y-m-d'))
                ->whereHas('shift', fn($q) => $q->where('is_overnight', true))
                ->first();
        }

        return $schedule;
    }

    private function findAttendance(int $userId, string $today): ?Attendance
    {
        $attendance = Attendance::where('user_id', $userId)
            ->where('tanggal', $today)
            ->first();

        if (!$attendance) {
            $attendance = Attendance::where('user_id', $userId)
                ->where('tanggal', now()->subDay()->format('Y-m-d'))
                ->whereNull('check_out')
                ->first();
        }

        return $attendance;
    }

    public function today(Request $request)
    {
        $user = $request->user();
        $today = now()->format('Y-m-d');

        $schedule = $this->findSchedule($user->id, $today);
        $attendance = $this->findAttendance($user->id, $today);

        if (! $attendance) {
            return response()->json([
                'checked_in' => false,
                'checked_out' => false,
                'has_schedule' => $schedule !== null,
                'schedule' => $schedule?->shift,
                'work_from' => $schedule?->work_from ?? 'wfo',
            ]);
        }

        return response()->json([
            'checked_in' => true,
            'checked_out' => $attendance->check_out !== null,
            'has_schedule' => $schedule !== null,
            'attendance' => $attendance,
            'work_from' => $attendance->work_from ?? 'wfo',
        ]);
    }

    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'address' => 'nullable|string',
            'foto' => 'required|string',
            'work_from' => 'required|in:wfo,wfa',
        ]);

        $user = $request->user();
        $today = now()->format('Y-m-d');

        $schedule = $this->findSchedule($user->id, $today);

        if (!$schedule) {
            return response()->json(['message' => 'Tidak ada jadwal kerja hari ini'], 422);
        }

        $terlambat = 0;
        if ($schedule?->shift) {
            $shiftStart = \Carbon\Carbon::parse($schedule->shift->jam_masuk);
            $checkInTime = now();

            if ($schedule->shift->is_overnight) {
                $midnight = \Carbon\Carbon::parse('00:00');
                $pulang = \Carbon\Carbon::parse($schedule->shift->jam_pulang);
                if ($checkInTime->between($midnight, $pulang)) {
                    $shiftStart->subDay();
                }
            }

            $terlambat = (int) $shiftStart->diffInMinutes($checkInTime, false);
            if ($terlambat < 0) $terlambat = 0;
        }

        $tanggal = $schedule->tanggal;
        $fotoPath = $this->saveFoto($validated['foto'], $user->id, $tanggal, 'in');

        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'tanggal' => $tanggal],
            [
                'check_in' => now()->format('H:i:s'),
                'check_in_lat' => $validated['lat'],
                'check_in_lon' => $validated['lon'],
                'check_in_address' => $validated['address'] ?? null,
                'check_in_foto' => $fotoPath,
                'ip_address' => $request->ip(),
                'work_from' => $validated['work_from'],
                'terlambat_menit' => $terlambat,
                'status' => $terlambat > 0 ? 'terlambat' : 'hadir',
            ]
        );

        if (!$attendance->wasRecentlyCreated) {
            return response()->json(['message' => 'Sudah absen masuk hari ini'], 422);
        }

        return response()->json($attendance, 201);
    }

    public function checkOut(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'address' => 'nullable|string',
            'foto' => 'required|string',
        ]);

        $user = $request->user();
        $today = now()->format('Y-m-d');

        $attendance = $this->findAttendance($user->id, $today);

        if (! $attendance) {
            return response()->json(['message' => 'Belum absen masuk'], 422);
        }

        if ($attendance->check_out) {
            return response()->json(['message' => 'Sudah absen keluar'], 422);
        }

        $fotoPath = $this->saveFoto($validated['foto'], $user->id, $attendance->tanggal, 'out');

        $attendance->update([
            'check_out' => now()->format('H:i:s'),
            'check_out_lat' => $validated['lat'],
            'check_out_lon' => $validated['lon'],
            'check_out_address' => $validated['address'] ?? null,
            'check_out_foto' => $fotoPath,
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
