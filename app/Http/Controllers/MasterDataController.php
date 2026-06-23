<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function shifts()
    {
        $shifts = \App\Models\Shift::all();
        return view('admin.shifts', compact('shifts'));
    }

    public function storeShift(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
            'toleransi_menit' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        \App\Models\Shift::create($validated);

        return redirect()->route('admin.shifts')->with('success', 'Shift created.');
    }

    public function updateShift(Request $request, \App\Models\Shift $shift)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
            'toleransi_menit' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $shift->update($validated);

        return redirect()->route('admin.shifts')->with('success', 'Shift updated.');
    }

    public function destroyShift(\App\Models\Shift $shift)
    {
        $shift->delete();
        return redirect()->route('admin.shifts')->with('success', 'Shift deleted.');
    }

    public function locations()
    {
        $locations = \App\Models\WorkLocation::all();
        return view('admin.locations', compact('locations'));
    }

    public function storeLocation(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'radius' => 'integer|min:1',
            'is_office' => 'boolean',
            'is_active' => 'boolean',
        ]);

        \App\Models\WorkLocation::create($validated);

        return redirect()->route('admin.locations')->with('success', 'Lokasi created.');
    }

    public function updateLocation(Request $request, \App\Models\WorkLocation $location)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'radius' => 'integer|min:1',
            'is_office' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $location->update($validated);

        return redirect()->route('admin.locations')->with('success', 'Lokasi updated.');
    }

    public function destroyLocation(\App\Models\WorkLocation $location)
    {
        $location->delete();
        return redirect()->route('admin.locations')->with('success', 'Lokasi deleted.');
    }

    public function holidays()
    {
        $holidays = \App\Models\Holiday::orderBy('tanggal', 'desc')->get();
        return view('admin.holidays', compact('holidays'));
    }

    public function storeHoliday(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date|unique:holidays',
            'keterangan' => 'required|string|max:255',
            'is_recurring' => 'boolean',
        ]);

        \App\Models\Holiday::create($validated);

        return redirect()->route('admin.holidays')->with('success', 'Hari libur created.');
    }

    public function destroyHoliday(\App\Models\Holiday $holiday)
    {
        $holiday->delete();
        return redirect()->route('admin.holidays')->with('success', 'Hari libur deleted.');
    }
}
