<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Division;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::with('division')->latest()->get();
        return view('admin.positions.index', compact('positions'));
    }

    public function create()
    {
        $divisions = Division::all();
        return view('admin.positions.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'division_id' => 'nullable|exists:divisions,id',
        ]);

        Position::create($validated);

        return redirect()->route('admin.positions')->with('success', 'Jabatan created.');
    }

    public function edit(Position $position)
    {
        $divisions = Division::all();
        return view('admin.positions.edit', compact('position', 'divisions'));
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'division_id' => 'nullable|exists:divisions,id',
        ]);

        $position->update($validated);

        return redirect()->route('admin.positions')->with('success', 'Jabatan updated.');
    }

    public function destroy(Position $position)
    {
        $position->delete();
        return redirect()->route('admin.positions')->with('success', 'Jabatan deleted.');
    }
}
