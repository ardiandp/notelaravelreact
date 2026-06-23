<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    protected $fillable = [
        'user_id', 'tanggal_pengajuan', 'tanggal_lembur',
        'jam_mulai', 'jam_selesai', 'total_jam',
        'jenis', 'rupiah_per_jam', 'total_bayar',
        'keterangan', 'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'date',
            'tanggal_lembur' => 'date',
            'jam_mulai' => 'datetime:H:i',
            'jam_selesai' => 'datetime:H:i',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
