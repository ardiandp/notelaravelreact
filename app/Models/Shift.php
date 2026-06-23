<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = ['nama', 'jam_masuk', 'jam_pulang', 'toleransi_menit', 'is_overnight', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_overnight' => 'boolean',
        ];
    }
}
