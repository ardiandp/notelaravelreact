<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    // Menampilkan semua catatan (terbaru di atas)
    public function index()
    {
        return response()->json(Note::latest()->get());
    }

    // Menyimpan catatan baru
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // Simpan ke database
        $note = Note::create($validated);

        // Kembalikan response JSON dengan status 201 (created)
        return response()->json($note, 201);
    }

    // Menghapus catatan berdasarkan ID
    public function destroy(Note $note)
    {
        $note->delete();

        return response()->json(['message' => 'Note deleted']);
    }
}
