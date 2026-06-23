<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceCorrection extends Model
{
    protected $table = 'attendance_corrections';

    protected $fillable = ['attendance_id', 'user_id', 'alasan', 'foto', 'status'];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
