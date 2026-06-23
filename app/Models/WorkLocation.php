<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkLocation extends Model
{
    protected $table = 'work_locations';

    protected $fillable = ['nama', 'alamat', 'lat', 'lon', 'radius', 'is_office', 'is_active'];

    protected function casts(): array
    {
        return ['is_office' => 'boolean', 'is_active' => 'boolean'];
    }
}
