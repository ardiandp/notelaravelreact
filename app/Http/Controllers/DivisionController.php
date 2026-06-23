<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::with('parent', 'head')->latest()->get();
        return view('admin.divisions.index', compact('divisions'));
    }

    public function create()
    {
        $parents = Division::all();
        return view('admin.divisions.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_bagian' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:divisions,id',
            'head_id' => 'nullable|exists:users,id',
        ]);

        Division::create($validated + ['is_active' => true]);

        return redirect()->route('admin.divisions')->with('success', 'Bagian created.');
    }

    public function edit(Division $division)
    {
        $parents = Division::where('id', '!=', $division->id)->get();
        return view('admin.divisions.edit', compact('division', 'parents'));
    }

    public function update(Request $request, Division $division)
    {
        $validated = $request->validate([
            'nama_bagian' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:divisions,id',
            'head_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $division->update($validated);

        return redirect()->route('admin.divisions')->with('success', 'Bagian updated.');
    }

    public function destroy(Division $division)
    {
        $division->delete();
        return redirect()->route('admin.divisions')->with('success', 'Bagian deleted.');
    }
}
