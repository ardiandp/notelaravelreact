<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDetail extends Model
{
    protected $fillable = [
        'user_id', 'no_ktp', 'file_ktp', 'file_kk', 'tempat_lahir', 'tanggal_lahir',
        'jenis_kelamin', 'agama', 'kewarganegaraan', 'tinggi_badan', 'berat_badan',
        'status_marital', 'tempat_tinggal',
        'alamat_sekarang', 'rt_rw_sekarang', 'provinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id',
        'alamat_asal', 'rt_rw_asal', 'provinsi_asal_id', 'kabupaten_asal_id', 'kecamatan_asal_id', 'kelurahan_asal_id',
        'telp_rumah', 'no_hp', 'email_pribadi', 'kontak_darurat_nama', 'kontak_darurat_hp',
        'status_karyawan', 'tgl_masuk', 'tgl_akhir', 'division_id', 'position_id', 'supervisor_id',
        'gaji_pokok', 'gaji_harian', 'uang_makan', 'uang_transport', 'uang_lembur', 'tunj_tetap',
        'pot_terlambat', 'pot_lebih_cuti',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
