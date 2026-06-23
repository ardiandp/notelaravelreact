<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('no_ktp', 20)->nullable();
            $table->string('file_ktp')->nullable();
            $table->string('file_kk')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Pria', 'Wanita'])->nullable();
            $table->string('agama')->nullable();
            $table->string('kewarganegaraan')->default('WNI');
            $table->integer('tinggi_badan')->nullable();
            $table->integer('berat_badan')->nullable();
            $table->enum('status_marital', ['Belum Menikah', 'Menikah', 'Cerai', 'Duda', 'Janda'])->nullable();
            $table->enum('tempat_tinggal', ['Kos', 'Kontrakan', 'Rumah Milik Sendiri', 'Asrama', 'Lainnya'])->nullable();
            $table->text('alamat_sekarang')->nullable();
            $table->string('rt_rw_sekarang')->nullable();
            $table->string('provinsi_id', 2)->nullable();
            $table->string('kabupaten_id', 4)->nullable();
            $table->string('kecamatan_id', 7)->nullable();
            $table->string('kelurahan_id', 10)->nullable();
            $table->text('alamat_asal')->nullable();
            $table->string('rt_rw_asal')->nullable();
            $table->string('provinsi_asal_id', 2)->nullable();
            $table->string('kabupaten_asal_id', 4)->nullable();
            $table->string('kecamatan_asal_id', 7)->nullable();
            $table->string('kelurahan_asal_id', 10)->nullable();
            $table->string('telp_rumah')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email_pribadi')->nullable();
            $table->string('kontak_darurat_nama')->nullable();
            $table->string('kontak_darurat_hp')->nullable();
            $table->enum('status_karyawan', ['Tetap', 'Kontrak', 'PKWT', 'Outsource', 'Magang'])->nullable();
            $table->date('tgl_masuk')->nullable();
            $table->date('tgl_akhir')->nullable();
            $table->foreignId('division_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->decimal('gaji_harian', 15, 2)->default(0);
            $table->decimal('uang_makan', 15, 2)->default(0);
            $table->decimal('uang_transport', 15, 2)->default(0);
            $table->decimal('uang_lembur', 15, 2)->default(0);
            $table->decimal('tunj_tetap', 15, 2)->default(0);
            $table->decimal('pot_terlambat', 15, 2)->default(0);
            $table->decimal('pot_lebih_cuti', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_details');
    }
};
