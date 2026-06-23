<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = ['nama', 'jam_masuk', 'jam_pulang', 'toleransi_menit', 'is_active'];

    protected function casts(): array
    {
        return [
            'jam_masuk' => 'datetime:H:i',
            'jam_pulang' => 'datetime:H:i',
            'is_active' => 'boolean',
        ];
    }
}
