<?php

namespace Database\Seeders;

use App\Models\Shift;
use App\Models\WorkLocation;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            AdminSeeder::class,
        ]);

        Shift::create(['nama' => 'Pagi', 'jam_masuk' => '08:00', 'jam_pulang' => '17:00', 'toleransi_menit' => 15, 'is_active' => true]);
        Shift::create(['nama' => 'Siang', 'jam_masuk' => '12:00', 'jam_pulang' => '21:00', 'toleransi_menit' => 15, 'is_active' => true]);

        WorkLocation::create(['nama' => 'Kantor Pusat', 'alamat' => 'Jl. Contoh No. 1', 'lat' => -6.2088, 'lon' => 106.8456, 'radius' => 100, 'is_office' => true, 'is_active' => true]);
    }
}
