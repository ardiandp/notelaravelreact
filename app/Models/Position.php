<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = ['nama', 'division_id'];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'position_id');
    }
}
