<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserSchedule;
use App\Models\Holiday;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $schedules = UserSchedule::with('shift')
            ->where('user_id', $user->id)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'tanggal' => $s->tanggal->format('Y-m-d'),
                'shift' => $s->shift ? [
                    'id' => $s->shift->id,
                    'nama' => $s->shift->nama,
                    'jam_masuk' => substr($s->shift->jam_masuk, 0, 5),
                    'jam_pulang' => substr($s->shift->jam_pulang, 0, 5),
                    'is_overnight' => $s->shift->is_overnight,
                ] : null,
                'work_from' => $s->work_from,
            ]);

        $holidays = Holiday::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal')
            ->get()
            ->map(fn ($h) => [
                'id' => $h->id,
                'tanggal' => $h->tanggal->format('Y-m-d'),
                'keterangan' => $h->keterangan,
                'is_recurring' => $h->is_recurring,
            ]);

        return response()->json([
            'schedules' => $schedules,
            'holidays' => $holidays,
            'bulan' => (int) $bulan,
            'tahun' => (int) $tahun,
        ]);
    }
}
