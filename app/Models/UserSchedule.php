<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSchedule extends Model
{
    protected $fillable = ['user_id', 'shift_id', 'tanggal', 'is_override', 'work_from'];

    protected function casts(): array
    {
        return ['tanggal' => 'date', 'is_override' => 'boolean', 'work_from' => 'string'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
