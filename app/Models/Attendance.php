<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id', 'tanggal', 'check_in', 'check_out',
        'check_in_lat', 'check_in_lon', 'check_out_lat', 'check_out_lon',
        'check_in_address', 'check_out_address', 'check_in_foto', 'check_out_foto',
        'ip_address', 'work_from', 'terlambat_menit', 'status', 'keterangan', 'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'check_in' => 'datetime:H:i:s',
            'check_out' => 'datetime:H:i:s',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function correction()
    {
        return $this->hasOne(AttendanceCorrection::class);
    }
}
