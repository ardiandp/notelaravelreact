<?php

namespace Database\Seeders;

use App\Models\Note;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    // Mengisi database dengan data dummy catatan
    public function run(): void
    {
        $notes = [
            'Belajar Laravel untuk backend development',
            'Belajar React untuk frontend development',
            'Membuat aplikasi catatan sederhana',
            'Integrasi Laravel dengan React menggunakan Inertia',
        ];

        foreach ($notes as $content) {
            Note::create(['content' => $content]);
        }
    }
}
