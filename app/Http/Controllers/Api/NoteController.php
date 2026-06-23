<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Note::where('user_id', $request->user()->id)
                ->latest()
                ->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $note = Note::create([
            'content' => $validated['content'],
            'user_id' => $request->user()->id,
        ]);

        return response()->json($note, 201);
    }

    public function destroy(Request $request, Note $note)
    {
        if ($note->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $note->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
