<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
        ]);

        \App\Models\Note::insert([
            ['user_id' => 1, 'content' => 'Belajar Laravel untuk backend', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 1, 'content' => 'Belajar React untuk frontend', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 1, 'content' => 'Membangun aplikasi catatan sederhana', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 1, 'content' => 'Integrasi Laravel + React sebagai fullstack', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
