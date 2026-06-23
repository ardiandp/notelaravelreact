<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shift;
use App\Models\UserSchedule;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);
        $userId = $request->get('user_id');

        $users = User::whereHas('employeeDetail')->with('employeeDetail.division')->get();
        $shifts = Shift::where('is_active', true)->get();

        $startDate = Carbon::create($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $holidays = Holiday::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->orWhere('is_recurring', true)->get()
            ->filter(function ($h) use ($tahun) {
                if ($h->is_recurring) {
                    $h->tanggal = Carbon::parse($h->tanggal)->setYear($tahun);
                    return $h->tanggal->month >= Carbon::create($tahun, 1, 1)->month;
                }
                return true;
            });
        $holidayDates = $holidays->pluck('tanggal')->map(fn($d) => Carbon::parse($d)->toDateString())->toArray();

        $dates = [];
        $date = $startDate->copy();
        while ($date->lte($endDate)) {
            $dates[] = $date->copy();
            $date->addDay();
        }

        $query = UserSchedule::with('shift');
        if ($userId) {
            $query->where('user_id', $userId);
        }
        $schedules = $query->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->get()->groupBy('user_id');

        $scheduleGrid = [];
        foreach ($users as $user) {
            $userSchedules = $schedules->get($user->id, collect())->keyBy(fn($s) => $s->tanggal?->toDateString() ?? $s->tanggal);
            $scheduleGrid[$user->id] = $userSchedules;
        }

        $selectedUser = $userId ? User::find($userId) : null;

        return view('admin.schedules.index', compact(
            'users', 'shifts', 'dates', 'scheduleGrid', 'holidayDates',
            'bulan', 'tahun', 'selectedUser', 'startDate', 'endDate'
        ));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $bulan = (int) $request->bulan;
        $tahun = (int) $request->tahun;

        $users = $request->user_id
            ? User::whereHas('employeeDetail')->where('id', $request->user_id)->get()
            : User::whereHas('employeeDetail')->get();
        $defaultShift = Shift::where('is_active', true)->first();

        if (!$defaultShift) {
            return back()->withErrors(['msg' => 'No active shift found.']);
        }

        $startDate = Carbon::create($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $holidays = Holiday::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->orWhere('is_recurring', true)->get();
        $holidayDates = [];
        foreach ($holidays as $h) {
            $d = $h->is_recurring ? Carbon::parse($h->tanggal)->setYear($tahun) : Carbon::parse($h->tanggal);
            if ($d->year == $tahun && $d->month == $bulan) {
                $holidayDates[] = $d->toDateString();
            }
        }

        $generated = 0;
        $date = $startDate->copy();
        while ($date->lte($endDate)) {
            if ($date->isWeekend() || in_array($date->toDateString(), $holidayDates)) {
                $date->addDay();
                continue;
            }

            foreach ($users as $user) {
                $exists = UserSchedule::where('user_id', $user->id)->where('tanggal', $date->toDateString())->exists();
                if (!$exists) {
                    UserSchedule::create([
                        'user_id' => $user->id,
                        'shift_id' => $defaultShift->id,
                        'tanggal' => $date->toDateString(),
                        'work_from' => 'wfo',
                    ]);
                    $generated++;
                }
            }
            $date->addDay();
        }

        if ($request->user_id) {
            return redirect()->route('admin.schedules.user', ['user' => $request->user_id, 'bulan' => $bulan, 'tahun' => $tahun])
                ->with('success', "Generated $generated schedule entries for user.");
        }
        return redirect()->route('admin.schedules', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('success', "Generated $generated schedule entries.");
    }

    public function userSchedule(Request $request, User $user)
    {
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);

        $shifts = Shift::where('is_active', true)->get();
        $startDate = Carbon::create($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $holidays = Holiday::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->orWhere('is_recurring', true)->get()
            ->filter(function ($h) use ($tahun) {
                if ($h->is_recurring) {
                    $h->tanggal = Carbon::parse($h->tanggal)->setYear($tahun);
                    return $h->tanggal->month >= Carbon::create($tahun, 1, 1)->month;
                }
                return true;
            });
        $holidayDates = $holidays->pluck('tanggal')->map(fn($d) => Carbon::parse($d)->toDateString())->toArray();

        $dates = [];
        $date = $startDate->copy();
        while ($date->lte($endDate)) {
            $dates[] = $date->copy();
            $date->addDay();
        }

        $schedules = UserSchedule::with('shift')
            ->where('user_id', $user->id)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get()
            ->keyBy(fn($s) => $s->tanggal?->toDateString() ?? $s->tanggal);

        return view('admin.schedules.user', compact(
            'user', 'shifts', 'dates', 'schedules', 'holidayDates', 'bulan', 'tahun', 'startDate', 'endDate'
        ));
    }

    public function userScheduleUpdate(Request $request, User $user)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'shift_id' => 'nullable|exists:shifts,id',
            'work_from' => 'nullable|in:wfo,wfa',
        ]);

        $tanggal = $validated['tanggal'];

        if ($validated['shift_id']) {
            $data = [
                'is_override' => true,
                'shift_id' => $validated['shift_id'],
                'work_from' => $validated['work_from'] ?? 'wfo',
            ];
            UserSchedule::updateOrCreate(
                ['user_id' => $user->id, 'tanggal' => $tanggal],
                $data
            );
            $shift = Shift::find($validated['shift_id']);
        } else {
            UserSchedule::where('user_id', $user->id)->where('tanggal', $tanggal)->delete();
            $shift = null;
        }

        return response()->json([
            'success' => true,
            'message' => $shift ? "Jadwal {$shift->nama} untuk {$tanggal}" : 'Jadwal dihapus',
            'shift_name' => $shift?->nama,
            'shift_id' => $shift?->id,
            'work_from' => $validated['work_from'] ?? 'wfo',
        ]);
    }

    public function update(Request $request, UserSchedule $schedule)
    {
        $validated = $request->validate([
            'shift_id' => 'nullable|exists:shifts,id',
            'work_from' => 'nullable|in:wfo,wfa',
        ]);

        if ($validated['shift_id']) {
            $schedule->update([
                'is_override' => true,
                'shift_id' => $validated['shift_id'],
                'work_from' => $validated['work_from'] ?? $schedule->work_from ?? 'wfo',
            ]);
        } else {
            $schedule->delete();
        }

        return back()->with('success', 'Jadwal diperbarui.');
    }

    public function clear(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $query = UserSchedule::whereYear('tanggal', $request->tahun)->whereMonth('tanggal', $request->bulan);
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        $query->delete();

        if ($request->user_id) {
            return redirect()->route('admin.schedules.user', ['user' => $request->user_id, 'bulan' => $request->bulan, 'tahun' => $request->tahun])
                ->with('success', 'Schedules cleared for this user.');
        }
        return redirect()->route('admin.schedules', ['bulan' => $request->bulan, 'tahun' => $request->tahun])
            ->with('success', 'Schedules cleared for this month.');
    }
}
