<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = ['tanggal', 'keterangan', 'is_recurring'];

    protected function casts(): array
    {
        return ['tanggal' => 'date', 'is_recurring' => 'boolean'];
    }
}
